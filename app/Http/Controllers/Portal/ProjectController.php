<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = $request->user()
            ->projects()
            ->withCount('tickets')
            ->latest()
            ->paginate(20);

        return view('portal.projects.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->loadCount('tickets');

        return view('portal.projects.show', [
            'project' => $project,
            'tickets' => $project->tickets()
                ->with('project')
                ->latest('updated_at')
                ->paginate(15),
        ]);
    }
}
