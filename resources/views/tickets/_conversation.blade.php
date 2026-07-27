<div class="conversation">
    @foreach ($ticket->messages as $message)
        <article class="message {{ $message->sender->isAdmin() ? 'message-admin' : 'message-customer' }}">
            <header>
                <div>
                    <strong>{{ $message->sender->full_name }}</strong>
                    <span class="message-role">{{ $message->sender->role->label() }}</span>
                </div>
                <time>{{ $message->created_at->format('Y/m/d H:i') }}</time>
            </header>

            <div class="message-body">{!! nl2br(e($message->message)) !!}</div>

            @if ($message->attachments->isNotEmpty())
                <div class="attachments">
                    @foreach ($message->attachments as $attachment)
                        <a href="{{ route('attachments.download', $attachment) }}">
                            {{ $attachment->original_name }}
                            <small>{{ number_format($attachment->size / 1024) }} KB</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </article>
    @endforeach
</div>
