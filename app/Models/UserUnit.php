<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserUnit extends Model
{
    protected $fillable = ['user_id', 'unit_id', 'position', 'is_primary', 'start_date', 'end_date', 'note'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'start_date' => 'date', 'end_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
