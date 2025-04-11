<template>
  <q-page class="flex flex-center">
    <q-card style="width: 400px">
      <q-card-section>
        <div class="text-h6">Login</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit.prevent="handleLogin">
          <q-input
            filled
            v-model="email"
            label="Email"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Please type your email']"
          />

          <q-input
            filled
            type="password"
            v-model="password"
            label="Password"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Please type your password']"
          />

           <q-banner v-if="errorMessage" class="text-white bg-red q-my-md">
            {{ errorMessage }}
          </q-banner>

          <div>
            <q-btn label="Login" type="submit" color="primary" :loading="loading"/>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth.js';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
    });
    
    const token = response.data.token;
    authStore.setToken(token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    
    router.push('/dashboard');
  } catch (error) {
    console.error('Login failed:', error);
    if (error.response && error.response.status === 422) {
      errorMessage.value = error.response.data.message || 'Invalid credentials.';
    } else if (error.response) {
       errorMessage.value = `Login failed: ${error.response.data.message || 'Server error'}`;
    } else {
      errorMessage.value = 'Login failed. Please check your connection.';
    }
  } finally {
    loading.value = false;
  }
};
</script> 