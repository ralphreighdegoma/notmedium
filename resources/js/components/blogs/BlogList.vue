<template>
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
        <blog-item 
          v-for="blog in blogs" 
          :key="blog.id"
          :blog="blog"
        />
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
</template>

<script setup>
import { ref, computed } from 'vue';
import BlogItem from './BlogItem.vue';

const props = defineProps({
  blogs: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  totalPages: {
    type: Number,
    required: true
  },
  initialPage: {
    type: Number,
    default: 1
  }
});

const emit = defineEmits(['page-change']);

const currentPage = ref(props.initialPage);

const onPageChange = (page) => {
  emit('page-change', page);
};
</script>

<style scoped>
.q-page {
  min-height: 100%;
  overflow-y: auto;
}
</style> 