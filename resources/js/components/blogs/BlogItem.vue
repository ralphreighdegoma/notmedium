<template>
  <q-item 
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
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  blog: {
    type: Object,
    required: true
  }
});

// Format date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Truncate content for preview
const truncateContent = (content) => {
  const textOnly = content.replace(/<[^>]*>/g, '');
  if (textOnly.length <= 150) return textOnly;
  return textOnly.substring(0, 150) + '...';
};

// View blog detail
const viewBlog = (blog) => {
  router.push(`/blogs/${blog.slug}`);
};
</script>

<style scoped>
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
  
  .blog-preview-text {
    width: 100% !important;
  }
}
</style> 