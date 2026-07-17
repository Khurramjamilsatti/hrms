<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'price_period',
        'badge',
        'description',
        'features',
        'cta_text',
        'cta_link',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
