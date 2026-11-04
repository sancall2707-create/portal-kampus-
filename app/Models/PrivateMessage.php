<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'batch_code',
        'subject',
        'body',
        'category',
        'is_confidential',
        'read_at',
        'deleted_by_sender_at',
        'deleted_by_recipient_at',
    ];

    protected function casts(): array
    {
        return [
            'is_confidential' => 'boolean',
            'read_at' => 'datetime',
            'deleted_by_sender_at' => 'datetime',
            'deleted_by_recipient_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
