import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';
import BlogPage from './pages/BlogPage.vue'; // Placeholder for your blog page
import BlogsPage from './pages/dashboard/BlogsPage.vue'; // Import the blog management page
import BlogPreviewPage from './pages/BlogPreviewPage.vue'; // Import the blog preview page
import { useAuthStore } from './stores/auth';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresGuest: true } // Only accessible if not logged in
  },
  {
    path: '/blog',
    name: 'Blog',
    component: BlogPage,
    meta: { requiresAuth: true } // Requires authentication
  },
  {
    path: '/dashboard/blogs',
    name: 'BlogManagement',
    component: BlogsPage,
    meta: { requiresAuth: true } // Requires authentication
  },
  // Blog preview route
  {
    path: '/blog/:slug/preview',
    name: 'BlogPreview',
    component: BlogPreviewPage,
    meta: { requiresAuth: true } // Requires authentication
  },
  // Redirect root path to blog if authenticated, otherwise to login
  {
    path: '/',
    redirect: () => {
      const authStore = useAuthStore();
      return authStore.isAuthenticated ? '/blog' : '/login';
    }
  },
  // Add other routes here
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest);

  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login page if trying to access a protected route without authentication
    next({ name: 'Login' });
  } else if (requiresGuest && authStore.isAuthenticated) {
    // Redirect to blog page if trying to access login page while already authenticated
    next({ name: 'Blog' });
  } else {
    // Proceed as normal
    next();
  }
});

export default router; 