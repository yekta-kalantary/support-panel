<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'project_id',
        'subject',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'closed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->oldest();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(TicketMessage::class)->latestOfMany('created_at');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('customer_id', $user->id);
    }

    public function isClosed(): bool
    {
        return $this->status === TicketStatus::CLOSED;
    }
}
