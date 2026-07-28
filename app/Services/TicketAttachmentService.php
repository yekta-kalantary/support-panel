<?php

namespace App\Services;

use App\Models\TicketMessage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class TicketAttachmentService
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function store(TicketMessage $message, array $files): void
    {
        $disk = (string) config('support.attachments.disk', 'local');

        foreach ($files as $file) {
            $storedName = Str::uuid()->toString().'.'.$file->extension();
            $directory = 'ticket-attachments/'.$message->ticket_id;
            $path = $file->storeAs($directory, $storedName, $disk);

            $message->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'disk' => $disk,
                'path' => $path,
                'created_at' => now(),
            ]);
        }
    }
}
