# Frontend Permission Implementation Guide

## Overview
This guide shows how to implement permission checks in Vue components using the permission system.

## Quick Start

### 1. Using the usePermissions Composable

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can, canAny, canAll, canAccessModule, isSuperAdmin } = usePermissions()
</script>

<template>
  <!-- Show button only if user has permission -->
  <button v-if="can('employees.create')" @click="createEmployee">
    Create Employee
  </button>
  
  <!-- Show section if user can access module -->
  <div v-if="canAccessModule('employees')">
    <!-- Employee management content -->
  </div>
  
  <!-- Show if user has any of the permissions -->
  <button v-if="canAny(['employees.update', 'employees.create'])">
    Manage Employees
  </button>
  
  <!-- Super admin bypass -->
  <div v-if="isSuperAdmin">
    <p>You have full access to everything!</p>
  </div>
</template>
```

### 2. Using Permission Store Directly

```vue
<script setup>
import { usePermissionStore } from '@/stores/permission'

const permissionStore = usePermissionStore()

// Check single permission
const canCreate = permissionStore.hasPermission('employees.create')

// Check multiple (OR)
const canManage = permissionStore.hasAnyPermission(['employees.create', 'employees.update'])

// Check multiple (AND)
const fullAccess = permissionStore.hasAllPermissions(['employees.view', 'employees.create', 'employees.update', 'employees.delete'])

// Check module access
const hasEmployeeAccess = permissionStore.canAccessModule('employees')
</script>
```

## Common Patterns

### Pattern 1: CRUD Operations Buttons

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can } = usePermissions()

const handleCreate = () => {
  // Create logic
}

const handleEdit = (id) => {
  // Edit logic
}

const handleDelete = (id) => {
  // Delete logic
}
</script>

<template>
  <div class="flex space-x-2">
    <button 
      v-if="can('employees.create')" 
      @click="handleCreate"
      class="btn btn-primary">
      Create
    </button>
    
    <button 
      v-if="can('employees.update')" 
      @click="handleEdit(employee.id)"
      class="btn btn-secondary">
      Edit
    </button>
    
    <button 
      v-if="can('employees.delete')" 
      @click="handleDelete(employee.id)"
      class="btn btn-danger">
      Delete
    </button>
  </div>
</template>
```

### Pattern 2: Navigation Menu Filtering

```vue
<script setup>
import { computed } from 'vue'
import { usePermissions } from '@/composables/usePermissions'

const { canAccessModule } = usePermissions()

const menuItems = computed(() => [
  {
    name: 'Employees',
    path: '/employees',
    icon: 'users',
    show: canAccessModule('employees')
  },
  {
    name: 'Departments',
    path: '/departments',
    icon: 'building',
    show: canAccessModule('departments')
  },
  {
    name: 'Attendance',
    path: '/attendance',
    icon: 'clock',
    show: canAccessModule('attendance')
  },
  // ... more items
].filter(item => item.show))
</script>

<template>
  <nav>
    <router-link 
      v-for="item in menuItems" 
      :key="item.path" 
      :to="item.path">
      {{ item.name }}
    </router-link>
  </nav>
</template>
```

### Pattern 3: Table Action Columns

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can, canAny } = usePermissions()

const props = defineProps({
  employees: Array
})
</script>

<template>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Department</th>
        <th v-if="canAny(['employees.update', 'employees.delete'])">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="employee in employees" :key="employee.id">
        <td>{{ employee.name }}</td>
        <td>{{ employee.email }}</td>
        <td>{{ employee.department }}</td>
        <td v-if="canAny(['employees.update', 'employees.delete'])">
          <button v-if="can('employees.update')" @click="edit(employee)">
            Edit
          </button>
          <button v-if="can('employees.delete')" @click="remove(employee)">
            Delete
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</template>
```

### Pattern 4: Form Access Control

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'
import { useRouter } from 'vue-router'

const { can } = usePermissions()
const router = useRouter()

// Redirect if no permission
if (!can('employees.create')) {
  router.push('/unauthorized')
}

const handleSubmit = () => {
  if (!can('employees.create')) {
    alert('You do not have permission to create employees')
    return
  }
  // Submit logic
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <!-- Form fields -->
    <button type="submit" :disabled="!can('employees.create')">
      Create Employee
    </button>
  </form>
</template>
```

### Pattern 5: Conditional Feature Display

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can } = usePermissions()

const tabs = computed(() => [
  { name: 'Basic Info', show: true },
  { name: 'Salary', show: can('salary-components.view') },
  { name: 'Performance', show: can('performance.view') },
  { name: 'Documents', show: can('files.view') }
].filter(tab => tab.show))
</script>

<template>
  <div class="tabs">
    <button 
      v-for="tab in tabs" 
      :key="tab.name" 
      class="tab">
      {{ tab.name }}
    </button>
  </div>
</template>
```

### Pattern 6: Approval Buttons

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can } = usePermissions()

const handleApprove = (item) => {
  // Approve logic
}

const handleReject = (item) => {
  // Reject logic
}
</script>

<template>
  <div v-if="leave.status === 'pending'">
    <div v-if="can('leave.approve')" class="flex space-x-2">
      <button @click="handleApprove(leave)" class="btn btn-success">
        Approve
      </button>
      <button @click="handleReject(leave)" class="btn btn-danger">
        Reject
      </button>
    </div>
    <div v-else>
      <p class="text-gray-500">Pending approval from manager</p>
    </div>
  </div>
</template>
```

## Module-Specific Implementations

### Employees Module - EmployeeList.vue

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'
import { ref, onMounted } from 'vue'

const { can, canAccessModule } = usePermissions()

// Redirect if no module access
if (!canAccessModule('employees')) {
  router.push('/')
}

const employees = ref([])

onMounted(async () => {
  if (can('employees.view')) {
    // Fetch employees
  }
})
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <h1>Employees</h1>
      <button 
        v-if="can('employees.create')" 
        @click="openCreateForm"
        class="btn btn-primary">
        Create Employee
      </button>
    </div>
    
    <table>
      <!-- Table content -->
      <tbody>
        <tr v-for="employee in employees" :key="employee.id">
          <!-- Employee data -->
          <td>
            <button 
              v-if="can('employees.update')" 
              @click="edit(employee)">
              Edit
            </button>
            <button 
              v-if="can('employees.delete')" 
              @click="remove(employee)">
              Delete  
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

### Leave Module - LeaveList.vue

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can } = usePermissions()

const approve = async (leaveId) => {
  // Approve leave
}

const reject = async (leaveId) => {
  // Reject leave
}
</script>

<template>
  <div>
    <button 
      v-if="can('leave.create')" 
      @click="openLeaveForm"
      class="btn btn-primary">
      Apply for Leave
    </button>
    
    <div v-for="leave in leaves" :key="leave.id">
      <h3>{{ leave.type }}</h3>
      <p>{{ leave.from_date }} to {{ leave.to_date }}</p>
      <p>Status: {{ leave.status }}</p>
      
      <div v-if="leave.status === 'pending' && can('leave.approve')">
        <button @click="approve(leave.id)" class="btn btn-success">
          Approve
        </button>
        <button @click="reject(leave.id)" class="btn btn-danger">
          Reject
        </button>
      </div>
    </div>
  </div>
</template>
```

### Payroll Module - PayrollList.vue

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions'

const { can } = usePermissions()

const generatePayroll = () => {
  // Generate payroll
}

const processPayroll = (payrollId) => {
  // Process payroll
}

const markAsPaid = (payrollId) => {
  // Mark as paid
}
</script>

<template>
  <div>
    <button 
      v-if="can('payroll.create')" 
      @click="generatePayroll"
      class="btn btn-primary">
      Generate Payroll
    </button>
    
    <table>
      <tbody>
        <tr v-for="payroll in payrolls" :key="payroll.id">
          <td>{{ payroll.month }}</td>
          <td>{{ payroll.status }}</td>
          <td>
            <button 
              v-if="payroll.status === 'draft' && can('payroll.process')" 
              @click="processPayroll(payroll.id)">
              Process
            </button>
            <button 
              v-if="payroll.status === 'processed' && can('payroll.process')" 
              @click="markAsPaid(payroll.id)">
              Mark as Paid
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

## Router Guards

Update router to check module access:

```javascript
// router/index.js
import { usePermissionStore } from '@/stores/permission'

router.beforeEach((to, from, next) => {
  const permissionStore = usePermissionStore()
  
  // Check if route requires specific module access
  if (to.meta.module) {
    if (!permissionStore.canAccessModule(to.meta.module)) {
      next('/unauthorized')
      return
    }
  }
  
  // Check if route requires specific permission
  if (to.meta.permission) {
    if (!permissionStore.hasPermission(to.meta.permission)) {
      next('/unauthorized')
      return
    }
  }
  
  next()
})

// Example route with meta
{
  path: '/employees',
  component: EmployeeList,
  meta: {
    module: 'employees',
    requiresAuth: true
  }
},
{
  path: '/employees/create',
  component: EmployeeForm,
  meta: {
    module: 'employees',
    permission: 'employees.create',
    requiresAuth: true
  }
}
```

## Testing Permissions

### Manual Testing Checklist

1. **Login as Super Admin**
   - Verify all menu items visible
   - Verify all action buttons visible
   - Verify all forms accessible

2. **Login as Manager**
   - Verify appropriate menu items visible
   - Verify manager-level actions available
   - Verify restricted areas hidden

3. **Login as Employee**
   - Verify limited menu items visible
   - Verify only self-service actions available
   - Verify admin areas completely hidden

### Browser Console Testing

```javascript
// In browser console
const permissionStore = usePermissionStore()

// Check current permissions
console.log(permissionStore.permissions)

// Check specific permission
console.log(permissionStore.hasPermission('employees.create'))

// Check module access
console.log(permissionStore.canAccessModule('employees'))

// Check super admin
console.log(permissionStore.isSuperAdmin)
```

## Best Practices

1. **Always check module access first** before checking specific permissions
2. **Use composable** for cleaner code
3. **Hide UI elements** users don't have permission for (don't just disable)
4. **Backend validation** is mandatory - frontend checks are just for UX
5. **Fetch permissions** after login and role changes
6. **Clear permissions** on logout

## Complete Permission List

All available permissions:

- **Employees**: view, create, update, delete, manage
- **Departments**: view, create, update, delete
- **Attendance**: view, create, update, delete, manage
- **Leave**: view, create, update, delete, approve
- **Payroll**: view, create, process, manage
- **Assets**: view, create, update, delete, assign
- **Loans**: view, create, update, delete, approve, manage
- **Salary Advance**: view, create, update, delete, approve
- **Salary Components**: view, create, update, delete, manage
- **Shifts**: view, create, update, delete, assign, manage
- **Recruitment**: view, create, update, delete, manage
- **Performance**: view, create, update, manage
- **Training**: view, create, update, delete, manage
- **Travel**: view, create, update, approve, manage
- **Timesheets**: view, create, update, approve, manage
- **Onboarding**: view, create, update, delete, manage
- **Helpdesk**: view, create, update, delete, manage
- **Files**: view, create, update, delete, manage
- **Calendar**: view, create, update, delete
- **Announcements**: view, create, update, delete
- **Deployments**: view, create, update, delete, approve, manage
- **CV Bank**: view, create, update, delete
- **Organization**: view

## Implementation Status

✅ Backend permission middleware applied to all routes
✅ Permission composable created
✅ Permission store functional
✅ Login issue fixed
⏳ Frontend implementation in progress (use this guide to update components)
