import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { usePermissions } from '@/composables/usePermissions';

/**
 * Permissions that allow creating a module record on behalf of another employee.
 */
const MODULE_CREATE_FOR_OTHERS_PERMISSIONS = {
  loans: ['loans.manage'],
  salary_advances: ['salary_advances.approve'],
  leaves: ['leaves.manage'],
  travel: ['travel.manage'],
  overtime: ['overtime.approve'],
  timesheets: ['timesheets.manage'],
  helpdesk: ['helpdesk.manage'],
  files: ['files.manage'],
  onboarding: ['onboarding.manage'],
  deployments: ['deployments.manage'],
  performance: ['performance.manage'],
  assets: ['assets.manage'],
  training: ['training.manage'],
  attendance: ['attendance.manage'],
  shifts: ['shifts.manage'],
  cv_bank: ['cv_bank.manage'],
  employees: ['employees.create', 'employees.manage'],
};

/**
 * Control employee dropdown visibility in create/edit forms.
 *
 * @param {string} module - Module key (e.g. 'loans', 'leaves')
 */
export function useEmployeeRecordPicker(module) {
  const authStore = useAuthStore();
  const { canAny } = usePermissions();

  const currentEmployeeId = computed(() => authStore.user?.employee?.id || null);

  const canCreateForOthers = computed(() => {
    const permissions = MODULE_CREATE_FOR_OTHERS_PERMISSIONS[module] || [];
    return permissions.length > 0 && canAny(permissions);
  });

  /** Show employee selector only when user may pick another employee. */
  const showEmployeePicker = computed(
    () => canCreateForOthers.value || !currentEmployeeId.value
  );

  function applyOwnEmployeeToForm(form, field = 'employee_id') {
    if (!showEmployeePicker.value && currentEmployeeId.value) {
      if (form && typeof form === 'object' && 'value' in form) {
        form.value[field] = currentEmployeeId.value;
      } else {
        form[field] = currentEmployeeId.value;
      }
    }
  }

  function resolveEmployeeId(form, field = 'employee_id') {
    const raw = form && typeof form === 'object' && 'value' in form ? form.value : form;
    if (showEmployeePicker.value) {
      return raw?.[field] || null;
    }
    return currentEmployeeId.value;
  }

  function validateEmployeeForSubmit(form, field = 'employee_id') {
    const employeeId = resolveEmployeeId(form, field);
    if (!employeeId) {
      return {
        valid: false,
        message: showEmployeePicker.value
          ? 'Please select an employee'
          : 'Your employee profile is not linked. Please contact HR.',
      };
    }
    return { valid: true, employeeId };
  }

  function buildPayload(form, field = 'employee_id') {
    const validation = validateEmployeeForSubmit(form, field);
    if (!validation.valid) {
      return { error: validation.message };
    }

    const raw = form && typeof form === 'object' && 'value' in form ? { ...form.value } : { ...form };
    if (showEmployeePicker.value) {
      raw[field] = validation.employeeId;
    } else {
      delete raw[field];
    }
    return { payload: raw };
  }

  return {
    currentEmployeeId,
    canCreateForOthers,
    showEmployeePicker,
    applyOwnEmployeeToForm,
    resolveEmployeeId,
    validateEmployeeForSubmit,
    buildPayload,
  };
}
