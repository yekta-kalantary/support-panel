<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function download(
        Request $request,
        TicketAttachment $attachment,
        ActivityLogger $logger,
    ): StreamedResponse {
        $attachment->loadMissing('ticketMessage.ticket');
        Gate::authorize('view', $attachment->ticketMessage->ticket);

        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        $logger->log('attachment.downloaded', $attachment, request: $request);

        return Storage::disk($attachment->disk)
            ->download($attachment->path, $attachment->original_name);
    }
}
