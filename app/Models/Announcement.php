<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'priority',
        'start_date',
        'end_date',
        'is_published',
        'created_by',
    ];

    protected $appends = [
        'is_active',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->is_published;
    }

    public function getExpiryDateAttribute(): ?string
    {
        return $this->end_date?->format('Y-m-d');
    }
}
