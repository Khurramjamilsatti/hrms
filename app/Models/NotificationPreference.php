<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'notification_type', 'enabled_events'
    ];

    protected $casts = [
        'enabled_events' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
