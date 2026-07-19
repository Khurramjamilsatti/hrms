const MODULE_PATHS = [
  '/dashboard',
  '/employees',
  '/attendance',
  '/leaves',
  '/leave-settings',
  '/short-leaves',
  '/payroll',
  '/salary-components',
  '/loans',
  '/salary-advances',
  '/cvs',
  '/deployments',
  '/departments',
  '/designations',
  '/profile',
  '/notifications',
  '/overtime',
  '/recruitment',
  '/performance',
  '/assets',
  '/announcements',
  '/timesheets',
  '/projects',
  '/onboarding',
  '/training',
  '/travel-expenses',
  '/advances',
  '/shifts',
  '/helpdesk',
  '/files',
  '/calendar',
  '/organization',
  '/admin',
];

const TYPE_ROUTES = [
  { match: /short_leave|exemption/, path: '/short-leaves', idKeys: ['short_leave_id'] },
  { match: /leave/, path: '/leaves', idKeys: ['leave_application_id', 'leave_id'] },
  { match: /payroll|salary(?!_advance|_component)/, path: '/payroll', idKeys: ['payroll_id'] },
  { match: /salary_advance|advance_request/, path: '/salary-advances', idKeys: ['advance_id', 'advance_request_id'] },
  { match: /loan/, path: '/loans', idKeys: ['loan_id'] },
  { match: /overtime/, path: '/overtime', idKeys: ['overtime_id', 'overtime_request_id'] },
  { match: /attendance|check_?in|check_?out/, path: '/attendance', idKeys: ['attendance_id'] },
  { match: /timesheet/, path: '/timesheets', idKeys: ['timesheet_id'] },
  { match: /training|course|enrollment/, path: '/training', idKeys: ['training_id', 'course_id', 'enrollment_id'] },
  { match: /shift|roster/, path: '/shifts', idKeys: ['shift_id', 'roster_id'] },
  { match: /ticket|helpdesk/, path: '/helpdesk', idKeys: ['ticket_id'] },
  { match: /expense|travel|mileage/, path: '/travel-expenses', idKeys: ['expense_id', 'travel_id', 'claim_id'] },
  { match: /meeting|calendar|event|reminder/, path: '/calendar', idKeys: ['event_id', 'calendar_event_id'] },
  { match: /document|file/, path: '/files', idKeys: ['file_id', 'document_id'] },
  { match: /recruit|interview|offer|job_application/, path: '/recruitment', idKeys: ['application_id', 'interview_id', 'offer_id', 'position_id'] },
  { match: /announcement/, path: '/announcements', idKeys: ['announcement_id'] },
  { match: /deployment/, path: '/deployments', idKeys: ['deployment_id'] },
  { match: /onboarding/, path: '/onboarding', idKeys: ['onboarding_id'] },
  { match: /performance|review|goal/, path: '/performance', idKeys: ['review_id', 'goal_id'] },
  { match: /asset/, path: '/assets', idKeys: ['asset_id'] },
  { match: /cv/, path: '/cvs', idKeys: ['cv_id'] },
  { match: /employee/, path: '/employees', idKeys: ['employee_id'] },
];

const RESOURCE_TYPE_ROUTES = {
  leave: '/leaves',
  short_leave: '/short-leaves',
  exemption: '/short-leaves',
  payroll: '/payroll',
  timesheet: '/timesheets',
  training: '/training',
  expense: '/travel-expenses',
  travel: '/travel-expenses',
  loan: '/loans',
  overtime: '/overtime',
  attendance: '/attendance',
  ticket: '/helpdesk',
  helpdesk: '/helpdesk',
  shift: '/shifts',
  recruitment: '/recruitment',
  announcement: '/announcements',
  deployment: '/deployments',
  onboarding: '/onboarding',
  file: '/files',
  document: '/files',
  calendar: '/calendar',
  meeting: '/calendar',
};

function parseData(data) {
  if (!data) return {};
  if (typeof data === 'object') return data;
  try {
    const parsed = JSON.parse(data);
    return typeof parsed === 'object' && parsed ? parsed : {};
  } catch (_) {
    return {};
  }
}

function normalizePath(rawUrl) {
  if (!rawUrl) return '';

  let value = String(rawUrl).trim();
  if (!value || value === '#' || value === 'null' || value === 'undefined') return '';

  try {
    if (/^https?:\/\//i.test(value)) {
      const parsed = new URL(value);
      if (typeof window !== 'undefined' && parsed.origin === window.location.origin) {
        value = `${parsed.pathname}${parsed.search}${parsed.hash}`;
      } else {
        return value; // external absolute URL
      }
    }
  } catch (_) {
    return '';
  }

  if (!value.startsWith('/')) {
    value = `/${value}`;
  }

  // Strip accidental /api prefix
  if (value.startsWith('/api/')) {
    value = value.slice(4);
  }

  return value;
}

function isValidAppPath(path) {
  if (!path || path === '/') return false;
  if (/^https?:\/\//i.test(path)) return true;

  const bare = path.split('?')[0].split('#')[0];
  return MODULE_PATHS.some((prefix) => bare === prefix || bare.startsWith(`${prefix}/`));
}

function firstId(data, keys = []) {
  for (const key of keys) {
    const value = data?.[key];
    if (value !== undefined && value !== null && value !== '') {
      return value;
    }
  }
  return data?.resource_id || data?.id || null;
}

function withOptionalId(basePath, id) {
  if (!id) return basePath;
  // Prefer query highlight for list screens without dedicated detail routes
  const detailRoutes = ['/loans', '/employees', '/deployments', '/cvs'];
  if (detailRoutes.some((route) => basePath === route || basePath.startsWith(`${route}/`))) {
    if (basePath === '/loans' || basePath === '/employees' || basePath === '/deployments' || basePath === '/cvs') {
      return `${basePath}/${id}`;
    }
  }
  return `${basePath}?id=${encodeURIComponent(id)}`;
}

function routeFromType(type, data) {
  const t = String(type || '').toLowerCase();
  if (!t) return '';

  for (const rule of TYPE_ROUTES) {
    if (rule.match.test(t)) {
      return withOptionalId(rule.path, firstId(data, rule.idKeys));
    }
  }
  return '';
}

function routeFromResourceType(data) {
  const resourceType = String(data?.resource_type || '').toLowerCase();
  if (!resourceType) return '';
  const path = RESOURCE_TYPE_ROUTES[resourceType];
  if (!path) return '';
  return withOptionalId(path, firstId(data));
}

/**
 * Resolve the in-app (or absolute) destination for a notification.
 */
export function resolveNotificationTarget(notification = {}) {
  const data = parseData(notification.data);
  const actionPath = normalizePath(notification.action_url);

  // Prefer a valid stored app path when present
  if (isValidAppPath(actionPath)) {
    if (/^https?:\/\//i.test(actionPath)) {
      return actionPath;
    }

    const bare = actionPath.split('?')[0].split('#')[0];
    const typePath = routeFromType(notification.type, data);
    const typeBare = typePath ? typePath.split('?')[0].split('#')[0] : '';

    // Deepen bare module links when type/data gives a more specific target
    if (
      typePath &&
      (bare === typeBare || typeBare.startsWith(`${bare}/`) || typePath.startsWith(`${bare}?`))
    ) {
      return typePath;
    }

    return actionPath;
  }

  return (
    routeFromType(notification.type, data) ||
    routeFromResourceType(data) ||
    ''
  );
}

export function hasNotificationTarget(notification) {
  return Boolean(resolveNotificationTarget(notification));
}
