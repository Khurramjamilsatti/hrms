<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingBlock extends Model
{
    public const TYPE_LOGO = 'logo';
    public const TYPE_HIGHLIGHT = 'highlight';
    public const TYPE_INDUSTRY = 'industry';
    public const TYPE_INTEGRATION = 'integration';

    protected $fillable = [
        'type',
        'icon',
        'title',
        'description',
        'url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
