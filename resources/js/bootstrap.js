import axios from 'axios';
import { HRMS_LOGIN_PATH, CMS_LOGIN_PATH } from './config/authPaths';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = '/api';

function isCmsApiUrl(url = '') {
  const path = String(url).replace(/^\/api/, '').replace(/^\//, '');
  return path === 'cms' || path.startsWith('cms/') || path.startsWith('cms?');
}

function isPublicContactUrl(url = '') {
  const path = String(url).replace(/^\/api/, '').replace(/^\//, '');
  return path === 'contact' || path.startsWith('contact/');
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
      const path = window.location.pathname;
      const onCmsLogin = path.startsWith(CMS_LOGIN_PATH);
      const onHrmsLogin = path === HRMS_LOGIN_PATH || path.startsWith(`${HRMS_LOGIN_PATH}/`);

      if (isCmsApiUrl(url)) {
        localStorage.removeItem('cms_auth_token');
        localStorage.removeItem('cms_user');
        if (!onCmsLogin && !String(url).includes('/cms/login')) {
          window.location.href = CMS_LOGIN_PATH;
        }
      } else if (!isPublicContactUrl(url)) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        if (!onHrmsLogin && !String(url).includes('/login')) {
          window.location.href = HRMS_LOGIN_PATH;
        }
      }
    }
    return Promise.reject(error);
  }
);
