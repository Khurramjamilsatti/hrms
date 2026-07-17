<template>
  <router-view />
  <ConfirmDialog />
  <NotificationContainer />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import NotificationContainer from '@/components/NotificationContainer.vue';

const authStore = useAuthStore();
useThemeStore();

onMounted(async () => {
  await authStore.checkAuth();
});
</script>
