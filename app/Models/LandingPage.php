<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'nav_label',
        'excerpt',
        'content',
        'show_in_footer',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'show_in_footer' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
