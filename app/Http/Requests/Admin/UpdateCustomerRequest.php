<?php

namespace App\Http\Requests\Admin;

use App\Enums\RecordStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($customerId)],
            'mobile' => ['required', 'regex:/^09\d{9}$/', Rule::unique('users', 'mobile')->ignore($customerId)],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'status' => ['required', Rule::enum(RecordStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'email' => 'ایمیل',
            'mobile' => 'شماره موبایل',
            'password' => 'رمز عبور',
            'status' => 'وضعیت',
        ];
    }
}
