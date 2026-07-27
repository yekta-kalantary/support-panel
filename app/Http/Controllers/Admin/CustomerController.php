<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::query()
            ->customers()
            ->withCount('projects')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(
        StoreCustomerRequest $request,
        ActivityLogger $logger,
    ): RedirectResponse {
        $customer = DB::transaction(function () use ($request, $logger): User {
            $customer = User::query()->create([
                ...$request->safe()->except(['password_confirmation']),
                'email' => strtolower($request->validated('email')),
                'role' => UserRole::CUSTOMER,
            ]);

            $logger->log('customer.created', $customer, newValues: $customer->only([
                'first_name',
                'last_name',
                'email',
                'mobile',
                'status',
            ]), request: $request);

            return $customer;
        });

        $customer->notify(new AccountCreatedNotification);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'مشتری با موفقیت ایجاد شد.');
    }

    public function edit(User $customer): View
    {
        abort_unless($customer->isCustomer(), 404);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(
        UpdateCustomerRequest $request,
        User $customer,
        ActivityLogger $logger,
    ): RedirectResponse {
        abort_unless($customer->isCustomer(), 404);

        $oldValues = $customer->only([
            'first_name',
            'last_name',
            'email',
            'mobile',
            'status',
        ]);

        $data = $request->safe()->except(['password', 'password_confirmation']);
        $data['email'] = strtolower($data['email']);

        $statusChangedToInactive =
            $customer->status === RecordStatus::ACTIVE
            && $request->enum('status', RecordStatus::class) === RecordStatus::INACTIVE;

        if ($request->filled('password') || $statusChangedToInactive) {
            $data['auth_version'] = $customer->auth_version + 1;
        }

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $customer->update($data);

        $logger->log(
            'customer.updated',
            $customer,
            oldValues: $oldValues,
            newValues: $customer->only(array_keys($oldValues)),
            request: $request,
        );

        return back()->with('success', 'اطلاعات مشتری با موفقیت ویرایش شد.');
    }
}
