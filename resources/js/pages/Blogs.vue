<template>
  <q-page padding>
    <blog-search 
      :initial-query="searchQuery" 
      @search="handleSearch" 
    />
    
    <blog-list
      :blogs="blogs"
      :loading="loading"
      :total-pages="totalPages"
      :initial-page="currentPage"
      @page-change="handlePageChange"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';
import BlogSearch from '../components/blogs/BlogSearch.vue';
import BlogList from '../components/blogs/BlogList.vue';

const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};

// Blog listing state
const blogs = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;

// Fetch blogs from the API
const fetchBlogs = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/for-you/blogs', {
      params: {
        page: currentPage.value,
        perPage: perPage,
        search: searchQuery.value,
        include: 'user'
      }
    });
    blogs.value = response.data.data.data || [];
    totalPages.value = Math.ceil(response.data.data.total / perPage);
  } catch (error) {
    console.error('Failed to fetch blogs:', error);
    blogs.value = [];
  } finally {
    loading.value = false;
  }
};

// Handle search input
const handleSearch = (query) => {
  searchQuery.value = query;
  currentPage.value = 1;
  fetchBlogs();
};

// Handle pagination
const handlePageChange = (page) => {
  currentPage.value = page;
  fetchBlogs();
};

onMounted(() => {
  fetchBlogs();
});
</script>

<style scoped>
/* No specific styles needed here as they've been moved to components */
</style> 