<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Requests\Project\UpdateProjectStatusRequest;
use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects.
     */
    public function index(Request $request): View
    {
        // Check and mark delayed projects
        $this->projectService->checkAllDelayedProjects();

        // Handle view preference
        if ($request->has('view')) {
            session(['projects_view' => $request->get('view')]);
        }
        $view = session('projects_view', 'grid');
        $perPage = $view === 'table' ? 25 : 15;

        $query = Project::with(['client', 'projectManager', 'teams', 'products', 'members', 'attachments']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('order_no', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sortable = ['order_no', 'name', 'status', 'delivery_date', 'created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $projects = $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();

        // Get data for the "New Project" modal
        $productsList = Product::where('is_active', true)->orderBy('name')->get();
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $users = User::whereNull('deleted_at')->where('status', 'active')->orderBy('first_name')->get();

        return view('projects.index', compact('projects', 'productsList', 'clients', 'users', 'view', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        $products = Product::where('is_active', true)->get();
        $teams = Team::where('is_active', true)->get();
        $users = User::whereNull('deleted_at')->get();
        $clients = Client::where('is_active', true)->get();

        return view('projects.create', compact('products', 'teams', 'users', 'clients'));
    }

    /**
     * Check whether a project name is already taken (used for live validation).
     */
    public function checkName(Request $request): \Illuminate\Http\JsonResponse
    {
        $exists = Project::where('name', $request->query('name'))->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projectService->createProject($request->validated());

        // Check if project should be marked as delayed (in case delivery date is in past)
        $this->projectService->checkAndMarkDelayed($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): View
    {
        // Check if this project should be marked as delayed
        $this->projectService->checkAndMarkDelayed($project);

        $project->load([
            'client',
            'projectManager',
            'products.category',
            'teams.users',
            'members',
            'attachments.uploader',
            'calendarEvents',
            'activities' => function ($query) {
                $query->with('user')->latest()->limit(20);
            },
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        // Check if this project should be marked as delayed
        $this->projectService->checkAndMarkDelayed($project);

        $project->load(['products', 'teams', 'members']);

        $products = Product::where('is_active', true)->get();
        $teams = Team::where('is_active', true)->get();
        $users = User::whereNull('deleted_at')->get();
        $clients = Client::where('is_active', true)->get();

        return view('projects.edit', compact('project', 'products', 'teams', 'users', 'clients'));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->updateProject($project, $request->validated());

        // Check if project should be marked as delayed after update
        $this->projectService->checkAndMarkDelayed($project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project (soft delete).
     */
    public function destroy(Project $project): RedirectResponse
    {
        // Release any reserved materials before deleting -- only if they were
        // reserved but not yet consumed (materials already deducted for
        // in-production/finished/delivered projects should not be added back).
        if ($project->status === 'confirmed') {
            app(\App\Services\InventoryService::class)->releaseForProject($project);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Update the status of the specified project.
     */
    public function updateStatus(UpdateProjectStatusRequest $request, Project $project): RedirectResponse
    {
        try {
            $this->projectService->transitionStatus($project, $request->validated('status'));

            // Check if project should be marked as delayed after status change
            $this->projectService->checkAndMarkDelayed($project);

            return redirect()->back()
                ->with('success', 'Project status updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
