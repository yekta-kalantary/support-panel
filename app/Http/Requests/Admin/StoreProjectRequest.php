<?php

namespace App\Http\Requests\Admin;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(
                    fn (Builder $query) => $query->where('role', UserRole::CUSTOMER->value)
                ),
            ],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'website_url' => ['required', 'url:http,https', 'max:2048'],
            'status' => ['required', Rule::enum(RecordStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'مشتری',
            'name' => 'نام پروژه',
            'website_url' => 'آدرس وب‌سایت',
            'status' => 'وضعیت',
        ];
    }
}
