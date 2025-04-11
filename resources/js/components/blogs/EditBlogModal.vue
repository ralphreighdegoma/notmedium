<template>
  <q-card style="width: 700px; max-width: 90vw;">
    <q-card-section class="row items-center q-pb-none">
      <div class="text-h6">Edit Blog</div>
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

        <q-banner v-if="error" class="bg-negative text-white q-mb-md">
          {{ error }}
        </q-banner>

        <div class="row justify-end q-mt-md">
          <q-btn label="Cancel" flat color="negative" @click="onClose" class="q-mr-sm" />
          <q-btn label="Save Changes" type="submit" color="primary" :loading="loading" />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  blog: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'blog-updated']);

const form = ref({
  title: '',
  content: '',
  status: 'draft',
  _method: 'PUT'
});

const statusOptions = [
  { label: 'Draft', value: 'draft' },
  { label: 'Published', value: 'published' }
];

const loading = ref(false);
const error = ref('');

const initializeForm = () => {
  form.value = {
    title: props.blog.title || '',
    content: props.blog.content || '',
    status: props.blog.status || 'draft',
    _method: 'PUT'
  };
};

watch(() => props.blog, () => {
  if (props.blog) {
    initializeForm();
  }
}, { immediate: true });

const submitForm = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const formData = new FormData();
    
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key]);
    });
    
    await axios.post(`/api/blogs/${props.blog.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    emit('blog-updated');
    onClose();
  } catch (err) {
    console.error('Failed to update blog:', err);
    if (err.response && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Failed to update blog. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};

const onClose = () => {
  error.value = '';
  emit('close');
};

onMounted(() => {
  if (props.blog) {
    initializeForm();
  }
});
</script> 