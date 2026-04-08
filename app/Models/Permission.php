<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'action',
        'description',
    ];

    /**
     * Get the roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * Get users who have this permission directly
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission')
            ->withPivot('is_granted')
            ->withTimestamps();
    }

    /**
     * Scope to filter by module
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get all permissions grouped by module
     */
    public static function getGroupedPermissions()
    {
        return self::all()->groupBy('module')->map(function ($permissions, $module) {
            return [
                'module' => $module,
                'permissions' => $permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'action' => $permission->action,
                        'description' => $permission->description,
                    ];
                }),
            ];
        })->values();
    }
}
