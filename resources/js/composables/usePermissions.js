import { computed } from 'vue'
import { usePermissionStore } from '@/stores/permission'

/**
 * Composable for permission checks in Vue components
 * Usage: const { can, canAny, canAll, isSuperAdmin, canAccessModule } = usePermissions()
 */
export function usePermissions() {
  const permissionStore = usePermissionStore()

  /**
   * Check if user has a specific permission
   * @param {string} permission - Permission slug (e.g., 'employees.create')
   * @returns {boolean}
   */
  const can = (permission) => {
    return permissionStore.hasPermission(permission)
  }

  /**
   * Check if user has any of the given permissions
   * @param {array} permissions - Array of permission slugs
   * @returns {boolean}
   */
  const canAny = (permissions) => {
    return permissionStore.hasAnyPermission(permissions)
  }

  /**
   * Check if user has all of the given permissions
   * @param {array} permissions - Array of permission slugs
   * @returns {boolean}
   */
  const canAll = (permissions) => {
    return permissionStore.hasAllPermissions(permissions)
  }

  /**
   * Check if user can access a module
   * @param {string} module - Module name (e.g., 'employees')
   * @returns {boolean}
   */
  const canAccessModule = (module) => {
    return permissionStore.canAccessModule(module)
  }

  /**
   * Check if user is super admin (bypasses all permission checks)
   * @returns {boolean}
   */
  const isSuperAdmin = computed(() => permissionStore.isSuperAdmin)

  /**
   * Get all permissions for the current user
   * @returns {array}
   */
  const permissions = computed(() => permissionStore.permissions)

  /**
   * Get all allowed modules for the current user
   * @returns {array}
   */
  const allowedModules = computed(() => permissionStore.allowedModules)

  /**
   * Get loading state
   * @returns {boolean}
   */
  const loading = computed(() => permissionStore.loading)

  /**
   * Get error state
   * @returns {string|null}
   */
  const error = computed(() => permissionStore.error)

  return {
    can,
    canAny,
    canAll,
    canAccessModule,
    isSuperAdmin,
    permissions,
    allowedModules,
    loading,
    error,
  }
}
