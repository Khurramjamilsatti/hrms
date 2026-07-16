import { reactive } from 'vue';

const state = reactive({
  open: false,
  mode: 'confirm', // 'confirm' | 'alert'
  title: 'Confirm',
  message: '',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  variant: 'primary', // 'primary' | 'danger' | 'success'
  resolve: null,
});

function openDialog(mode, options = {}) {
  const opts = typeof options === 'string' ? { message: options } : (options || {});

  return new Promise((resolve) => {
    state.open = true;
    state.mode = mode;
    state.title = opts.title || (mode === 'alert' ? 'Notice' : 'Confirm');
    state.message = opts.message || '';
    state.confirmText = opts.confirmText || (mode === 'alert' ? 'OK' : 'Confirm');
    state.cancelText = opts.cancelText || 'Cancel';
    state.variant = opts.variant || (mode === 'alert' ? 'primary' : 'primary');
    state.resolve = resolve;
  });
}

function close(result) {
  const resolver = state.resolve;
  state.open = false;
  state.resolve = null;
  if (resolver) resolver(result);
}

export function useDialog() {
  const confirm = (options) => openDialog('confirm', options);
  const alert = (options) => openDialog('alert', options);

  const handleConfirm = () => close(true);
  const handleCancel = () => close(false);

  return {
    state,
    confirm,
    alert,
    handleConfirm,
    handleCancel,
  };
}
