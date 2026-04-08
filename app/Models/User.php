<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    public function managedEmployees()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Get the role assigned to this user
     */
    public function assignedRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get direct permissions assigned to this user
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withPivot('is_granted')
            ->withTimestamps();
    }

    // Permission Methods
    /**
     * Check if user has a specific permission
     * Checks both role permissions and direct user permissions
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // System admin (role === 'super_admin') has all permissions
        if ($this->isSuperAdmin() && $this->role === 'super_admin') {
            return true;
        }

        // Check if user has direct permission override (revoked = false)
        $directPermission = $this->directPermissions()
            ->where('slug', $permissionSlug)
            ->first();

        if ($directPermission) {
            return $directPermission->pivot->is_granted;
        }

        // Check role permissions if user has a role assigned
        if ($this->role_id && $this->assignedRole) {
            return $this->assignedRole->hasPermission($permissionSlug);
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for this user (from role and direct)
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Get permissions from role
        if ($this->role_id && $this->assignedRole) {
            $permissions = $this->assignedRole->permissions;
        }

        // Get direct permissions
        $directPermissions = $this->directPermissions;

        // Merge and handle overrides
        foreach ($directPermissions as $permission) {
            if ($permission->pivot->is_granted) {
                // Add granted permission
                if (!$permissions->contains('id', $permission->id)) {
                    $permissions->push($permission);
                }
            } else {
                // Remove revoked permission
                $permissions = $permissions->reject(function ($p) use ($permission) {
                    return $p->id === $permission->id;
                });
            }
        }

        return $permissions;
    }

    /**
     * Get grouped permissions by module
     */
    public function getGroupedPermissions()
    {
        return $this->getAllPermissions()
            ->groupBy('module')
            ->map(function ($permissions) {
                return $permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'action' => $permission->action,
                    ];
                })->values();
            });
    }

    /**
     * Get allowed modules for this user
     */
    public function getAllowedModules(): array
    {
        if ($this->isSuperAdmin() && $this->role === 'super_admin') {
            // System admin can access all modules
            return Permission::distinct('module')->pluck('module')->toArray();
        }

        return $this->getAllPermissions()
            ->pluck('module')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Grant a direct permission to this user
     */
    public function grantPermission(Permission $permission)
    {
        return $this->directPermissions()->syncWithoutDetaching([
            $permission->id => ['is_granted' => true]
        ]);
    }

    /**
     * Revoke a direct permission from this user
     */
    public function revokePermission(Permission $permission)
    {
        return $this->directPermissions()->syncWithoutDetaching([
            $permission->id => ['is_granted' => false]
        ]);
    }

    /**
     * Assign a role to this user (only by super admin)
     */
    public function assignRole(Role $role)
    {
        $this->role_id = $role->id;
        $this->role = $role->slug; // Keep backward compatibility
        return $this->save();
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isHRAdmin(): bool
    {
        return $this->role === 'hr_admin';
    }

    public function isSectionHead(): bool
    {
        return $this->role === 'section_head';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user can perform first-level approval (Section Head or higher)
     */
    public function canApproveFirst(): bool
    {
        return in_array($this->role, ['section_head', 'hr_admin', 'super_admin']);
    }

    /**
     * Check if user can perform final-level approval (HR Admin or Super Admin)
     */
    public function canApproveFinal(): bool
    {
        return in_array($this->role, ['hr_admin', 'super_admin']);
    }

    /**
     * Get the hierarchical level of the user's role
     * Higher number = higher authority
     */
    public function getRoleLevel(): int
    {
        return match($this->role) {
            'super_admin' => 5,
            'hr_admin' => 4,
            'section_head' => 3,
            'manager' => 2,
            'admin', 'employee' => 1,
            default => 0
        };
    }

    /**
     * Check if this user has higher authority than another user
     */
    public function hasHigherAuthorityThan(User $otherUser): bool
    {
        return $this->getRoleLevel() > $otherUser->getRoleLevel();
    }
}
