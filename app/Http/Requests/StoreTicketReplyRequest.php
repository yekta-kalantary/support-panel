<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reply', $this->route('ticket')) === true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:2', 'max:20000'],
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
            'message' => 'متن پاسخ',
            'attachments' => 'فایل‌های پیوست',
        ];
    }
}
