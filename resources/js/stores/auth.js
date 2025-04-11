import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null);
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const router = useRouter(); // Get router instance inside the store setup

  const isAuthenticated = computed(() => !!token.value && !!user.value);

  function setToken(newToken) {
    token.value = newToken;
    localStorage.setItem('token', newToken);
    axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
  }

  function setUser(newUser) {
    user.value = newUser;
    localStorage.setItem('user', JSON.stringify(newUser));
  }

  function clearAuth() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
  }

  async function fetchUser() {
    if (!token.value) return;
    try {
      const response = await axios.get('/api/user');
      setUser(response.data);
    } catch (error) {
      console.error('Failed to fetch user:', error);
      clearAuth(); // Clear auth state if fetching user fails (e.g., token expired)
      if (router) { // Check if router is available
        router.push('/login'); // Redirect to login if token is invalid
      }
    }
  }

  async function logout() {
    if (!token.value) return;
    try {
      await axios.post('/logout');
    } catch (error) {
      console.error('Logout failed:', error);
      // Still clear auth state even if API call fails
    } finally {
      clearAuth();
      if (router) { // Check if router is available
        router.push('/login');
      }
    }
  }

  // Attempt to fetch user data when the store is initialized if token exists
  if (token.value) {
    fetchUser();
  }

  return {
    token,
    user,
    isAuthenticated,
    setToken,
    setUser,
    clearAuth,
    fetchUser,
    logout,
  };
}); 