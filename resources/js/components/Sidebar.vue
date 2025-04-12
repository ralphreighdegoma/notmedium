<template>
  <!-- Desktop Sidebar -->
  <q-drawer
    v-if="!mobileScreen"
    v-model="drawerOpen"
    show-if-above
    :width="100"
    :breakpoint="500"
    class="flex flex-center"
  >
    <q-list class="text-center">
        <q-item clickable @click="handleForYouBlogs" class="justify-center" :class="{ 'bg-grey-3': $route.path === '/blogs' }">
            <q-item-section avatar>
                <q-icon name="description" size="sm" />
            </q-item-section>
        </q-item>
        <q-item v-if="!authStore.isAuthenticated" clickable @click="handleLogin" class="justify-center" :class="{ 'bg-grey-3': $route.path === '/login' }">
            <q-item-section avatar>
                <q-icon name="lock" size="sm" />
            </q-item-section>
        </q-item>

        <template v-if="authStore.isAuthenticated">
            <q-item clickable @click="handleBlogsManagement" class="justify-center" :class="{ 'bg-grey-3': $route.path === '/dashboard/blogs' }">
                <q-item-section avatar>
                <q-icon name="settings" size="sm" />
                </q-item-section>
            </q-item>
            <q-item v-if="authStore.isAuthenticated" clickable @click="handleLogout" class="justify-center">
                <q-item-section avatar>
                <q-icon name="logout" size="sm" />
                </q-item-section>
            </q-item>
        </template>
    </q-list>
  </q-drawer>

  <!-- Mobile Bottom Navigation -->
  <q-footer v-if="mobileScreen" bordered class="bg-white text-primary">
    <q-tabs no-caps align="justify" class="text-primary">
      <q-tab @click="handleForYouBlogs" icon="description" :class="{ 'bg-grey-3': $route.path === '/blogs' }" />
      <q-tab v-if="!authStore.isAuthenticated" @click="handleLogin" icon="lock" :class="{ 'bg-grey-3': $route.path === '/login' }" />
      <template v-if="authStore.isAuthenticated">
        <q-tab @click="handleBlogsManagement" icon="settings" :class="{ 'bg-grey-3': $route.path === '/dashboard/blogs' }" />
        <q-tab @click="handleLogout" icon="logout" />
      </template>
    </q-tabs>
  </q-footer>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue']);

const router = useRouter();
const authStore = useAuthStore();
const mobileScreen = ref(false);

const drawerOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const handleLogout = () => {
  authStore.logout();
};

const handleLogin = () => {
  router.push('/login');
};

const handleBlogsManagement = () => {
  router.push('/dashboard/blogs');
};

const handleForYouBlogs = () => {
  router.push('/blogs');
};

const checkScreenSize = () => {
  mobileScreen.value = window.innerWidth < 500;
};

onMounted(() => {
  checkScreenSize();
  window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize);
});
</script> 