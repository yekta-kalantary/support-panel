<?php

namespace App\Http\Requests;

use App\Enums\RecordStatus;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() === true;
    }

    public function rules(): array
    {
        return [
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(
                    fn (Builder $query) => $query
                        ->where('customer_id', $this->user()->id)
                        ->where('status', RecordStatus::ACTIVE->value)
                ),
            ],
            'subject' => ['required', 'string', 'min:5', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:20000'],
            'attachments' => ['nullable', 'array', 'max:'.config('support.attachments.max_files', 5)],
            'attachments.*' => [
                File::types(config('support.attachments.extensions'))
                    ->max(config('support.attachments.max_size_kb')),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'پروژه',
            'subject' => 'عنوان',
            'message' => 'شرح درخواست',
            'attachments' => 'فایل‌های پیوست',
        ];
    }
}
