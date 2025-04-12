<template>
  <div class="row justify-center q-mb-lg">
    <div class="col-12 col-sm-8 col-md-6">
      <q-input
        v-model="searchText"
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
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  initialQuery: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['search']);

const searchText = ref(props.initialQuery);

const onSearch = () => {
  emit('search', searchText.value);
};

watch(() => props.initialQuery, (newVal) => {
  searchText.value = newVal;
});
</script>

<style scoped>
.blog-search {
  border-radius: 8px;
}
</style> 