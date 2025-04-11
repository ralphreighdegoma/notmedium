<template>
  <q-page>
    <div v-if="loading" class="full-width row flex-center q-pa-xl">
      <q-spinner color="primary" size="3em" />
      <div class="q-ml-md text-h6">Loading preview...</div>
    </div>
    
    <div v-else-if="error" class="full-width row flex-center q-pa-xl text-negative">
      <q-icon name="error" size="2em" />
      <div class="q-ml-md text-body1">{{ error }}</div>
    </div>

    <!-- add status -->
  

    <div v-else class="blog-preview">
      <!-- Hero Section with Featured Image -->
      <div class="blog-hero" :style="blog.featured_image ? `background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)), url(${blog.featured_image})` : 'background-color: var(--q-primary)'">
        <div class="container q-pa-md">
          <div class="row justify-between items-center q-mb-md">
            <q-chip color="primary" text-color="white" icon="visibility">Preview Mode</q-chip>
            <q-btn 
              color="white" 
              flat
              icon="arrow_back" 
              label="Back to Dashboard" 
              @click="goBack" 
              class="q-px-md"
            />
          </div>
          
          <h1 class="blog-title text-white q-my-md">{{ blog.title }}</h1>
          
          <div class="blog-meta row items-center q-gutter-md text-white">
            <div class="row items-center">
              <q-icon name="event" size="sm" class="q-mr-xs" />
              <span>{{ formatDate(blog.created_at || new Date()) }}</span>
            </div>
            <div class="row items-center" v-if="blog.author">
              <q-icon name="person" size="sm" class="q-mr-xs" />
              <span>{{ blog.author }}</span>
            </div>
            <div class="row items-center" v-if="blog.read_time">
              <q-icon name="schedule" size="sm" class="q-mr-xs" />
              <span>{{ blog.read_time }} min read</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="container">
        <div class="blog-content-wrapper q-pa-lg">
          <!-- Content -->
          <div v-html="blog.content" class="blog-body text-body1"></div>
          
          <!-- Tags -->
          <div v-if="blog.tags && blog.tags.length" class="blog-tags q-mt-xl">
            <div class="text-subtitle1 q-mb-sm">Tags:</div>
            <div class="row q-gutter-sm">
              <q-chip 
                v-for="tag in blog.tags" 
                :key="tag"
                outline
                color="primary"
                text-color="primary"
              >
                {{ tag }}
              </q-chip>
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const blog = ref({});
const loading = ref(true);
const error = ref(null);

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Fetch the blog data
const fetchBlog = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`/api/blogs/${route.params.slug}/preview`);
    blog.value = response.data;
  } catch (err) {
    console.error('Error fetching blog preview:', err);
    error.value = 'Failed to load blog preview. The blog may not exist or you may not have permission to view it.';
  } finally {
    loading.value = false;
  }
};

// Navigate back to the dashboard
const goBack = () => {
  router.push('/dashboard/blogs');
};

onMounted(() => {
  fetchBlog();
});
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
}

.blog-hero {
  background-size: cover;
  background-position: center;
  padding: 80px 0 60px;
  position: relative;
}

.blog-title {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

.blog-meta {
  opacity: 0.9;
}

.blog-content-wrapper {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
  margin-top: -40px;
  position: relative;
  z-index: 2;
  margin-bottom: 60px;
}

.blog-body {
  line-height: 1.9;
  color: #333;
}

.blog-body p {
  margin-bottom: 1.5rem;
}

.blog-body img {
  max-width: 100%;
  height: auto;
  margin: 2rem 0;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.blog-body h1, .blog-body h2 {
  margin-top: 2.5rem;
  margin-bottom: 1.5rem;
  font-weight: 700;
  color: #222;
}

.blog-body h3, .blog-body h4 {
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-weight: 600;
  color: #333;
}

.blog-body a {
  color: var(--q-primary);
  text-decoration: none;
  border-bottom: 1px solid var(--q-primary);
  transition: all 0.3s ease;
}

.blog-body a:hover {
  opacity: 0.8;
}

.blog-body blockquote {
  border-left: 4px solid var(--q-primary);
  padding-left: 1.5rem;
  font-style: italic;
  margin: 2rem 0;
  color: #555;
}

.blog-body ul, .blog-body ol {
  padding-left: 1.5rem;
  margin-bottom: 1.5rem;
}

.blog-body li {
  margin-bottom: 0.5rem;
}

.blog-tags {
  border-top: 1px solid #eee;
  padding-top: 2rem;
}

@media (max-width: 767px) {
  .blog-title {
    font-size: 2rem;
  }
  
  .blog-hero {
    padding: 60px 0 40px;
  }
  
  .blog-content-wrapper {
    margin-top: -30px;
  }
}
</style> 