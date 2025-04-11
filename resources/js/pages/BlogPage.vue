<template>
  <q-page padding>
    <h4>Blog Page (Protected)</h4>
    <p v-if="authStore.user">Welcome, {{ authStore.user.name }}!</p>
    <q-btn label="Logout" color="negative" @click="handleLogout" />
  </q-page>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js';

const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};
</script> 