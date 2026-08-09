<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectAttachmentController extends Controller
{
    /**
     * Store a new attachment for the specified project.
     */
    public function store(StoreAttachmentRequest $request, Project $project): RedirectResponse
    {
        $file = $request->file('file');

        $path = $file->store('project-attachments', 'private');

        $project->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => 'private',
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Attachment uploaded successfully.');
    }

    /**
     * Download the specified attachment.
     */
    public function download(Project $project, Attachment $attachment): BinaryFileResponse
    {
        // Ensure the attachment belongs to this project
        if ($attachment->attachable_id !== $project->id || $attachment->attachable_type !== Project::class) {
            abort(404);
        }

        $disk = Storage::disk($attachment->disk ?? 'private');

        if (!$disk->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download($disk->path($attachment->file_path), $attachment->file_name);
    }

    /**
     * Remove the specified attachment from the project.
     */
    public function destroy(Project $project, Attachment $attachment): RedirectResponse
    {
        // Ensure the attachment belongs to this project
        if ($attachment->attachable_id !== $project->id || $attachment->attachable_type !== Project::class) {
            abort(404);
        }

        Storage::disk($attachment->disk ?? 'private')->delete($attachment->file_path);

        $attachment->forceDelete();

        return redirect()->back()
            ->with('success', 'Attachment deleted successfully.');
    }
}
