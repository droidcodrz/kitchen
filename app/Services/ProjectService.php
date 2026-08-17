<?php

namespace App\Services;

use App\Models\Project;
use App\Notifications\ProjectDelayedNotification;
use App\Notifications\ProjectStatusChangedNotification;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected AlertNotifier $alertNotifier
    ) {}

    /**
     * Generate a unique order number (e.g. ORD-000001).
     */
    public function generateOrderNumber(): string
    {
        $prefix = config('manufacturing.order_no_prefix', 'ORD-');
        $padding = config('manufacturing.order_no_padding', 6);

        $lastProject = Project::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastProject ? $lastProject->id + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, $padding, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a project can transition to a new status.
     */
    public function canTransitionTo(Project $project, string $newStatus): bool
    {
        $currentStatus = $project->status;
        $statuses = config('manufacturing.project_statuses', []);

        if (!isset($statuses[$currentStatus])) {
            return false;
        }

        $allowedNext = $statuses[$currentStatus]['next'] ?? [];

        return in_array($newStatus, $allowedNext);
    }

    /**
     * Transition a project to a new status after validation.
     *
     * @throws \InvalidArgumentException
     */
    public function transitionStatus(Project $project, string $newStatus): Project
    {
        if (!$this->canTransitionTo($project, $newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition project from '{$project->status}' to '{$newStatus}'."
            );
        }

        // Reserve required materials once the order is confirmed
        if ($newStatus === 'confirmed') {
            $this->inventoryService->reserveForProject($project);
        }

        // Deduct (consume) reserved materials once production actually starts.
        // Throws if stock is insufficient, which blocks the status change.
        if ($newStatus === 'in_production') {
            $this->inventoryService->consumeForProject($project);
        }

        $fromStatus = $project->status;
        $project->update(['status' => $newStatus]);

        if ($newStatus === 'delayed') {
            $this->notifyDelayed($project);
        } else {
            $this->notifyWatchers($project, new ProjectStatusChangedNotification($project, $fromStatus, $newStatus));
        }

        return $project->fresh();
    }

    /**
     * Create a project with related products, teams, and members.
     */
    public function createProject(array $data): Project
    {
        $data['order_no'] = $this->generateOrderNumber();
        $data['slug'] = Str::slug($data['name']);
        $data['project_manager_id'] = $data['project_manager_id'] ?? auth()->id();

        $project = Project::create($data);

        // Sync products with pivot data
        if (!empty($data['products'])) {
            $productSync = [];
            foreach ($data['products'] as $product) {
                if (!empty($product['product_id'])) {
                    $productSync[$product['product_id']] = [
                        'quantity' => $product['quantity'] ?? 1,
                        'unit_price_at_time' => $product['unit_price_at_time'] ?? null,
                        'notes' => $product['notes'] ?? null,
                    ];
                }
            }
            $project->products()->sync($productSync);
        }

        // Sync inventory items attached directly to the project (raw materials
        // sold/used as-is, not wrapped in a manufactured Product)
        if (!empty($data['inventory_items'])) {
            $inventoryItemSync = [];
            foreach ($data['inventory_items'] as $entry) {
                if (!empty($entry['inventory_item_id'])) {
                    $inventoryItemSync[$entry['inventory_item_id']] = [
                        'quantity' => $entry['quantity'] ?? 1,
                        'notes' => $entry['notes'] ?? null,
                    ];
                }
            }
            $project->inventoryItems()->sync($inventoryItemSync);
        }

        // Sync teams
        if (!empty($data['team_ids'])) {
            $teamSync = [];
            foreach ($data['team_ids'] as $teamId) {
                $teamSync[$teamId] = ['assigned_at' => now()];
            }
            $project->teams()->sync($teamSync);
        }

        // Sync members
        if (!empty($data['members'])) {
            $memberSync = [];
            foreach ($data['members'] as $memberId) {
                $memberSync[$memberId] = ['assigned_at' => now()];
            }
            $project->members()->sync($memberSync);
        }

        // Handle attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $path = $file->store("project-attachments/{$project->id}", 'private');

                $project->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'private',
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        // If the project was created already confirmed, reserve its materials immediately
        if ($project->status === 'confirmed') {
            $this->inventoryService->reserveForProject($project);
        }

        return $project->load(['client', 'projectManager', 'products', 'inventoryItems', 'teams', 'members']);
    }

    /**
     * Update a project and sync its relations.
     */
    public function updateProject(Project $project, array $data): Project
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $project->update($data);

        // Sync products with pivot data
        if (array_key_exists('products', $data)) {
            $productSync = [];
            foreach ($data['products'] ?? [] as $product) {
                if (!empty($product['product_id'])) {
                    $productSync[$product['product_id']] = [
                        'quantity' => $product['quantity'] ?? 1,
                        'unit_price_at_time' => $product['unit_price_at_time'] ?? null,
                        'notes' => $product['notes'] ?? null,
                    ];
                }
            }
            $project->products()->sync($productSync);
        }

        // Sync inventory items attached directly to the project
        if (array_key_exists('inventory_items', $data)) {
            $inventoryItemSync = [];
            foreach ($data['inventory_items'] ?? [] as $entry) {
                if (!empty($entry['inventory_item_id'])) {
                    $inventoryItemSync[$entry['inventory_item_id']] = [
                        'quantity' => $entry['quantity'] ?? 1,
                        'notes' => $entry['notes'] ?? null,
                    ];
                }
            }
            $project->inventoryItems()->sync($inventoryItemSync);
        }

        // Sync teams
        if (array_key_exists('team_ids', $data)) {
            $teamSync = [];
            foreach ($data['team_ids'] ?? [] as $teamId) {
                $teamSync[$teamId] = ['assigned_at' => now()];
            }
            $project->teams()->sync($teamSync);
        }

        // Sync members
        if (array_key_exists('members', $data)) {
            $memberSync = [];
            foreach ($data['members'] ?? [] as $memberId) {
                $memberSync[$memberId] = ['assigned_at' => now()];
            }
            $project->members()->sync($memberSync);
        }

        // Delete attachments
        if (!empty($data['delete_attachments'])) {
            foreach ($data['delete_attachments'] as $attachmentId) {
                $attachment = $project->attachments()->find($attachmentId);
                if ($attachment) {
                    // Delete file from storage
                    Storage::disk($attachment->disk ?? 'private')->delete($attachment->file_path);
                    // Delete database record
                    $attachment->delete();
                }
            }
        }

        // Handle new attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $path = $file->store("project-attachments/{$project->id}", 'private');

                $project->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'private',
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return $project->fresh(['client', 'projectManager', 'products', 'inventoryItems', 'teams', 'members']);
    }

    /**
     * Check if a project should be marked as delayed based on delivery date.
     */
    public function checkAndMarkDelayed(Project $project): bool
    {
        // Only check projects that are actively in progress
        if (!in_array($project->status, ['confirmed', 'design', 'in_production', 'inspection'])) {
            return false;
        }

        // Check if delivery date exists and has passed
        if ($project->delivery_date && $project->delivery_date->isPast()) {
            $project->update(['status' => 'delayed']);
            $this->notifyDelayed($project);
            return true;
        }

        return false;
    }

    /**
     * Check all active projects and mark delayed ones, notifying watchers for each.
     */
    public function checkAllDelayedProjects(): int
    {
        $projects = Project::whereIn('status', ['confirmed', 'design', 'in_production', 'inspection'])
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '<', Carbon::today())
            ->get();

        foreach ($projects as $project) {
            $project->update(['status' => 'delayed']);
            $this->notifyDelayed($project);
        }

        return $projects->count();
    }

    /**
     * Notify configured roles that a project was marked delayed.
     */
    private function notifyDelayed(Project $project): void
    {
        $this->alertNotifier->notify(
            'project_delayed',
            fn (bool $viaEmail) => new ProjectDelayedNotification($project, $viaEmail),
            fn ($user) => $user->unreadNotifications()
                ->where('type', ProjectDelayedNotification::class)
                ->whereJsonContains('data->project_id', $project->id)
                ->exists()
        );
    }

    /**
     * Notify a project's manager and assigned members.
     */
    private function notifyWatchers(Project $project, Notification $notification): void
    {
        $recipients = collect([$project->projectManager])
            ->merge($project->members)
            ->filter()
            ->unique('id');

        foreach ($recipients as $user) {
            $user->notify($notification);
        }
    }
}
