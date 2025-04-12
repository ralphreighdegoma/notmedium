<template>
    <q-layout view="lHh Lpr lFf">
      <Sidebar 
        v-model="leftDrawerOpen" 
      />
  
      <q-page-container class="page-container-blog">
        <router-view />
      </q-page-container>
    </q-layout>
  </template>
  
  <script setup>
  import { useAuthStore } from './stores/auth';
  import { ref } from 'vue';
  import Sidebar from './components/Sidebar.vue';
  

  const authStore = useAuthStore();
  const leftDrawerOpen = ref(true);
  </script>