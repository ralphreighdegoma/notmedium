<template>
  <q-page padding>
    <!-- Search Component -->
    <div class="row justify-center q-mb-lg">
      <div class="col-12 col-sm-8 col-md-6">
        <q-input
          v-model="searchQuery"
          filled
          placeholder="Search articles..."
          class="blog-search"
          @keyup.enter="onSearch"
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
    </div>

    <!-- Blog Listing -->
    <div class="row justify-center q-gutter-md">
      <div v-if="loading" class="text-center q-pa-md">
        <q-spinner color="primary" size="3em" />
        <div class="q-mt-sm">Loading articles...</div>
      </div>

      <div v-else-if="blogs.length === 0" class="text-center q-pa-xl full-width">
        <q-icon name="description" size="3em" color="grey-7" />
        <div class="text-h6 q-mt-md text-grey-7">No articles found</div>
        <div class="text-subtitle1 text-grey-6">Try a different search term or check back later</div>
      </div>

      <div v-else class="col-12 col-sm-10 col-md-6">
        <q-list bordered separator>
          <q-item 
            v-for="blog in blogs" 
            :key="blog.id" 
            clickable 
            v-ripple
            @click="viewBlog(blog)"
            class="blog-article-item q-py-md"
          >
            <q-item-section>
              <div class="row items-start no-wrap">
                <div class="col-grow">
                  <q-item-label class="text-h6" style="width: 300px;">{{ blog.title }}</q-item-label>
                  <q-item-label caption lines="2" class="q-mt-sm blog-preview-text" style="width: 400px;">
                    {{ truncateContent(blog.content) }}
                  </q-item-label>
                  <div class="row q-mt-sm text-grey-7 items-center">
                    <q-icon name="event" size="xs" class="q-mr-xs" />
                    <span class="text-caption">{{ formatDate(blog.created_at) }}</span>
                    <q-separator vertical spaced="sm" class="q-mx-sm" />
                    <q-icon name="person" size="xs" class="q-mr-xs" />
                    <span class="text-caption">{{ blog.user ? blog.user.name : 'Unknown' }}</span>
                    <q-separator vertical spaced="sm" class="q-mx-sm" />
                    <q-icon name="schedule" size="xs" class="q-mr-xs" />
                    <span class="text-caption">{{ blog.read_time }} min read</span>
                    <q-space />
                  </div>
                </div>
                <div class="col-auto q-ml-md" v-if="blog.image_preview">
                  <q-img
                    :src="blog.image_preview"
                    :ratio="1"
                    width="200px"
                    height="100px"
                    class="rounded-borders blog-thumbnail"
                    contain
                  />
                </div>
              </div>
            </q-item-section>
          </q-item>
        </q-list>

        <div class="row justify-center q-mt-lg">
          <q-pagination
            v-model="currentPage"
            :max="totalPages"
            :max-pages="6"
            direction-links
            boundary-links
            @update:model-value="onPageChange"
          />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth.js';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();

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

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Truncate content for preview
const truncateContent = (content) => {
  // Remove HTML tags
  const textOnly = content.replace(/<[^>]*>/g, '');
  if (textOnly.length <= 150) return textOnly;
  return textOnly.substring(0, 150) + '...';
};

// Handle search input
const onSearch = () => {
  currentPage.value = 1;
  fetchBlogs();
};

// Handle pagination
const onPageChange = (page) => {
  fetchBlogs();
};

// View blog detail
const viewBlog = (blog) => {
  router.push(`/blogs/${blog.slug}`);
};

onMounted(() => {
  fetchBlogs();
});
</script>

<style scoped>
.blog-search {
  border-radius: 8px;
}

.blog-article-item {
  transition: background-color 0.3s;
}

.blog-article-item:hover {
  background-color: #f5f5f5;
}

.blog-thumbnail {
  object-fit: cover;
  max-width: 100%;
  overflow: hidden;
}

.blog-preview-text {
  max-width: 100%;
  overflow: hidden;
}

@media (max-width: 599px) {
  .blog-thumbnail {
    width: 60px;
    height: 60px;
  }
  
  .q-item-label.text-h6 {
    font-size: 1rem;
  }
  
  .blog-article-item {
    padding: 8px;
  }
  
  .col-auto[v-if="blog.image_preview"] {
    display: none;
  }
  
  .blog-preview-text {
    width: 100% !important;
  }
  
  .q-page {
    min-height: 100%;
    overflow-y: auto;
  }
}
</style> 