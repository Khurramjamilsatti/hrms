<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'parent_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(FileCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FileCategory::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(EmployeeFile::class, 'category_id');
    }
}
