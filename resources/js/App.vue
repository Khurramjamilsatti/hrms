<template>
  <router-view />
  <ConfirmDialog />
  <NotificationContainer />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import NotificationContainer from '@/components/NotificationContainer.vue';

const authStore = useAuthStore();

onMounted(async () => {
  await authStore.checkAuth();
});
</script>
