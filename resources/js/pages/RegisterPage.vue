<template>
  <q-page class="row">
    <!-- Left column with image (hidden on small screens) -->
    <div class="col-12 col-md-6 flex flex-center" v-if="$q.screen.gt.sm">
      <q-img
        src="/images/login.jpg"
        alt="Register image"
        fit="cover"
        spinner-color="white"
        spinner-size="50px"
        class="register-side-image"
        style="margin: auto; transform: scale(0.79)"
      >
      </q-img>
    </div>

    <!-- Right column with registration form -->
    <div class="col-12 col-md-6 flex flex-center q-pa-md q-pa-lg-xl">
      <div class="q-pa-md" style="width: 100%; max-width: 400px;">
        <h4 class="text-h4 q-mb-md text-weight-bold text-center">NotMedium Blog</h4>
        <p class="text-subtitle1 q-mb-lg text-grey-8 text-center">Create your account</p>

        <q-form @submit.prevent="handleRegister" class="q-gutter-y-md">
          <q-input
            filled
            v-model="name"
            label="Full Name"
            type="text"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Please enter your name']"
          >
            <template v-slot:prepend>
              <q-icon name="person" />
            </template>
          </q-input>

          <q-input
            filled
            v-model="email"
            label="Email"
            type="email"
            lazy-rules
            :rules="[ 
              val => val && val.length > 0 || 'Please enter your email',
              val => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(val) || 'Please enter a valid email'
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="email" />
            </template>
          </q-input>

          <q-input
            filled
            type="password"
            v-model="password"
            label="Password"
            lazy-rules
            :rules="[ val => val && val.length >= 8 || 'Password must be at least 8 characters']"
          >
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
          </q-input>

          <q-input
            filled
            type="password"
            v-model="passwordConfirmation"
            label="Confirm Password"
            lazy-rules
            :rules="[ 
              val => val && val.length > 0 || 'Please confirm your password',
              val => val === password || 'Passwords do not match'
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
          </q-input>

          <q-banner v-if="errorMessage" class="text-white bg-red q-my-md">
            {{ errorMessage }}
          </q-banner>

          <q-btn 
            label="Sign Up" 
            type="submit" 
            color="primary" 
            class="full-width q-py-sm q-mt-md" 
            :loading="loading"
            unelevated
          />
        </q-form>
        
        <div class="q-mt-xl text-center text-grey-8">
          Already have an account? 
          <router-link to="/login" class="text-primary text-weight-bold">Sign in</router-link>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth.js';

const router = useRouter();
const authStore = useAuthStore();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errorMessage = ref('');

const handleRegister = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    const response = await axios.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    });
    
    const token = response.data.token;
    authStore.setToken(token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    
    // Fetch user info before navigating
    await authStore.fetchUser();
    
    router.push('/dashboard/blogs');
  } catch (error) {
    console.error('Registration failed:', error);
    if (error.response && error.response.status === 422) {
      const validationErrors = error.response.data.errors;
      if (validationErrors) {
        // Format validation errors
        errorMessage.value = Object.values(validationErrors)
          .flat()
          .join(', ');
      } else {
        errorMessage.value = error.response.data.message || 'Validation failed.';
      }
    } else if (error.response) {
       errorMessage.value = `Registration failed: ${error.response.data.message || 'Server error'}`;
    } else {
      errorMessage.value = 'Registration failed. Please check your connection.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-side-image {
  width: 100%;
  height: 100vh;
}

.bg-black-4 {
  background-color: rgba(0, 0, 0, 0.4);
}
</style> 