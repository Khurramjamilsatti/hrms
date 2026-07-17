import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = '/api';

function isCmsApiUrl(url = '') {
  const path = String(url).replace(/^\/api/, '').replace(/^\//, '');
  return path === 'cms' || path.startsWith('cms/') || path.startsWith('cms?');
}

// Attach the correct token per API surface (HRMS vs CMS)
window.axios.interceptors.request.use((config) => {
  const url = config.url || '';
  if (isCmsApiUrl(url)) {
    const cmsToken = localStorage.getItem('cms_auth_token');
    if (cmsToken) {
      config.headers.Authorization = `Bearer ${cmsToken}`;
    } else if (config.headers) {
      delete config.headers.Authorization;
    }
  } else {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    } else if (config.headers) {
      delete config.headers.Authorization;
    }
  }
  return config;
});

window.axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const url = error.config?.url || '';
      const onCmsLogin = window.location.pathname.startsWith('/cms/login');
      const onHrmsLogin = window.location.pathname === '/login';

      if (isCmsApiUrl(url)) {
        localStorage.removeItem('cms_auth_token');
        localStorage.removeItem('cms_user');
        if (!onCmsLogin && !String(url).includes('/cms/login')) {
          window.location.href = '/cms/login';
        }
      } else {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        if (!onHrmsLogin && !String(url).includes('/login')) {
          window.location.href = '/login';
        }
      }
    }
    return Promise.reject(error);
  }
);
