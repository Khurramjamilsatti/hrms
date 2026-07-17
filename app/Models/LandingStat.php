<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingStat extends Model
{
    protected $fillable = [
        'label',
        'value',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
