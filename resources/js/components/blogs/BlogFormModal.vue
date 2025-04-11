<template>
  <q-card style="width: 700px; height: 100vh;">
    <q-card-section class="row items-center q-pb-none">
      <div class="text-h6">{{ isEditMode ? 'Edit Blog' : 'Add New Blog' }}</div>
      <q-space />
      <q-btn icon="close" flat round dense v-close-popup @click="onClose" />
    </q-card-section>

    <q-card-section>
      <q-form @submit.prevent="submitForm">
        <q-input
          v-model="form.title"
          filled
          label="Title *"
          :rules="[val => !!val || 'Title is required']"
          class="q-mb-md"
        />

        <q-editor
          v-model="form.content"
          min-height="200px"
          class="q-mb-md"
          :rules="[val => !!val || 'Content is required']"
        />

        <div class="row q-mb-md">
          <q-select
            v-model="form.status"
            :options="statusOptions"
            filled
            label="Status *"
            class="col-12"
            emit-value
            map-options
          />
        </div>

        <q-file
          v-model="form.image"
          label="Image"
          filled
          class="q-mb-md"
        />

        <!-- image preview -->
        <div v-if="form.image_preview && form.image_preview !== ''" class="q-mb-md">
          <img :src="form.image_preview" alt="Image" class="q-mb-md" style="max-width: 300px;" />
        </div>

        <q-banner v-if="error" class="bg-negative text-white q-mb-md">
          {{ error }}
        </q-banner>

        <div class="row justify-end q-mt-md">
          <q-btn label="Cancel" flat color="negative" @click="onClose" class="q-mr-sm" />
          <q-btn :label="isEditMode ? 'Save Changes' : 'Save'" type="submit" color="primary" :loading="loading" />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  blog: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'blog-added', 'blog-updated']);

const isEditMode = computed(() => !!props.blog);

const form = ref({
  title: '',
  content: '',
  status: 'draft'
});

const statusOptions = [
  { label: 'Draft', value: 'draft' },
  { label: 'Published', value: 'published' }
];

const loading = ref(false);
const error = ref('');

const initializeForm = () => {
  if (isEditMode.value) {
    form.value = {
      title: props.blog.title || '',
      content: props.blog.content || '',
      status: props.blog.status || 'draft',
      image_preview: props.blog.image_preview || '',
      _method: 'PUT'
    };
  } else {
    form.value = {
      title: '',
      content: '',
      status: 'draft'
    };
  }
};

watch(() => props.blog, () => {
  initializeForm();
}, { immediate: true });

const submitForm = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const formData = new FormData();
    
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key]);
    });
    
    const url = isEditMode.value 
      ? `/api/blogs/${props.blog.id}`
      : '/api/blogs';
    
    await axios.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    if (isEditMode.value) {
      emit('blog-updated');
    } else {
      emit('blog-added');
    }
    onClose();
  } catch (err) {
    console.error(`Failed to ${isEditMode.value ? 'update' : 'create'} blog:`, err);
    if (err.response && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = `Failed to ${isEditMode.value ? 'update' : 'create'} blog. Please try again.`;
    }
  } finally {
    loading.value = false;
  }
};

const onClose = () => {
  error.value = '';
  initializeForm();
  emit('close');
};

onMounted(() => {
  initializeForm();
});
</script> 