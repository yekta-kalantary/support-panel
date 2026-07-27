<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with('customer')
            ->withCount('tickets')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('website_url', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $query) use ($search): void {
                            $query
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('customer_id'), fn (Builder $query) => $query->where('customer_id', $request->integer('customer_id')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $customers = User::customers()->orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.projects.index', compact('projects', 'customers'));
    }

    public function create(): View
    {
        return view('admin.projects.create', [
            'customers' => User::customers()->orderBy('first_name')->orderBy('last_name')->get(),
        ]);
    }

    public function store(
        StoreProjectRequest $request,
        ActivityLogger $logger,
    ): RedirectResponse {
        $project = Project::query()->create($request->validated());

        $logger->log(
            'project.created',
            $project,
            newValues: $project->only(['customer_id', 'name', 'website_url', 'status']),
            request: $request,
        );

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'پروژه با موفقیت ایجاد شد.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', [
            'project' => $project,
            'customers' => User::customers()->orderBy('first_name')->orderBy('last_name')->get(),
        ]);
    }

    public function update(
        UpdateProjectRequest $request,
        Project $project,
        ActivityLogger $logger,
    ): RedirectResponse {
        $newCustomerId = (int) $request->validated('customer_id');

        if ($newCustomerId !== $project->customer_id && $project->tickets()->exists()) {
            throw ValidationException::withMessages([
                'customer_id' => 'مالک پروژه‌ای که تیکت دارد قابل تغییر نیست.',
            ]);
        }

        $oldValues = $project->only(['customer_id', 'name', 'website_url', 'status']);
        $project->update($request->validated());

        $logger->log(
            'project.updated',
            $project,
            oldValues: $oldValues,
            newValues: $project->only(array_keys($oldValues)),
            request: $request,
        );

        return back()->with('success', 'پروژه با موفقیت ویرایش شد.');
    }
}
