<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    /**
     * Store a new milestone for the specified project.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'max:191'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['sort_order'] = $project->milestones()->count();

        $project->milestones()->create($validated);

        return redirect()->back()
            ->with('success', 'Milestone added.');
    }

    /**
     * Toggle a milestone's completion state, or update its details.
     */
    public function update(Request $request, Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        if ($milestone->project_id !== $project->id) {
            abort(404);
        }

        if ($request->has('toggle_complete')) {
            $milestone->update([
                'completed_at' => $milestone->is_complete ? null : now(),
            ]);

            return redirect()->back()
                ->with('success', $milestone->is_complete ? 'Milestone marked complete.' : 'Milestone reopened.');
        }

        $validated = $request->validate([
            'title' => ['required', 'max:191'],
            'due_date' => ['nullable', 'date'],
        ]);

        $milestone->update($validated);

        return redirect()->back()
            ->with('success', 'Milestone updated.');
    }

    /**
     * Remove the specified milestone.
     */
    public function destroy(Project $project, ProjectMilestone $milestone): RedirectResponse
    {
        if ($milestone->project_id !== $project->id) {
            abort(404);
        }

        $milestone->delete();

        return redirect()->back()
            ->with('success', 'Milestone deleted.');
    }
}
