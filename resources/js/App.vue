<template>
    <q-layout view="lHh Lpr lFf">
      <q-header elevated>
        <q-toolbar>
          <q-toolbar-title>BlogQua</q-toolbar-title>
          <q-btn v-if="authStore.isAuthenticated" flat label="Logout" @click="handleLogout" />
        </q-toolbar>
      </q-header>
  
      <q-page-container>
        <router-view />
      </q-page-container>
    </q-layout>
  </template>
  
  <script setup>
  import { useAuthStore } from './stores/auth';

  const authStore = useAuthStore();

  const handleLogout = () => {
    authStore.logout();
  };
  </script>