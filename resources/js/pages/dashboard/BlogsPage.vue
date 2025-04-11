<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="row q-mb-md items-center justify-between">
        <div class="text-h5">Blog Management</div>
        <q-btn 
          label="Add New Blog" 
          color="primary" 
          icon="add"
          @click="addNewBlog()"
        />
      </div>
      
      <div class="q-mb-md" style="width: 300px">
        <q-input
          v-model="searchQuery"
          filled
          placeholder="Search by title"
          class="q-mb-md"
          @update:model-value="onSearch"
          clearable
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>

      <q-table
        :rows="filteredBlogs"
        :columns="columns"
        row-key="id"
        :loading="loading"
        :pagination="pagination"
        @request="onRequest"
        binary-state-sort
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="image" :props="props">
              <q-img :src="props.row.image_preview" style="width: 100px; height: 40px;" />
            </q-td>
            <q-td key="title" :props="props">
              {{ props.row.title }}
            </q-td>
            <q-td key="status" :props="props">
              <q-badge style="font-size: 12px; padding: 5px 10px;" :color="props.row.status === 'published' ? 'green' : 'grey'">
                {{ props.row.status === 'published' ? 'Published' : 'Draft' }}
              </q-badge>
            </q-td>
            <q-td key="created_at" :props="props">
              {{ formatDate(props.row.created_at) }}
            </q-td>
            <q-td key="actions" :props="props">
              <q-btn-dropdown flat dense color="primary" label="Actions">
                <q-list>
                  <q-item clickable v-close-popup @click="editBlog(props.row)">
                    <q-item-section avatar>
                      <q-icon name="edit" />
                    </q-item-section>
                    <q-item-section>Edit</q-item-section>
                  </q-item>

                  <q-item clickable v-close-popup @click="previewBlog(props.row)">
                    <q-item-section avatar>
                      <q-icon name="preview" />
                    </q-item-section>
                    <q-item-section>Preview</q-item-section>
                  </q-item>

                  <q-item clickable v-close-popup @click="toggleStatus(props.row)">
                    <q-item-section avatar>
                      <q-icon :name="props.row.status === 'published' ? 'unpublished' : 'publish'" :color="props.row.status === 'published' ? 'grey' : 'green'" />
                    </q-item-section>
                    <q-item-section :class="props.row.status === 'published' ? '' : 'text-green'">
                      {{ props.row.status === 'published' ? 'Set to Draft' : 'Publish' }}
                    </q-item-section>
                  </q-item>

                  <q-item clickable v-close-popup @click="confirmArchive(props.row)">
                    <q-item-section avatar>
                      <q-icon name="archive" color="negative" />
                    </q-item-section>
                    <q-item-section class="text-negative">Archive</q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
            </q-td>
          </q-tr>
        </template>

        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-md text-grey-8">
            <q-icon name="sentiment_dissatisfied" size="24px" class="q-mr-md" />
            <span>No blogs found | Time to create your first blog.</span>
          </div>
        </template>
      </q-table>

      <q-dialog v-model="showBlogModal" persistent position="right">
        <blog-form-modal 
          :blog="selectedBlog" 
          @close="closeBlogModal" 
          @blog-added="refreshBlogs" 
          @blog-updated="refreshBlogs" 
        />
      </q-dialog>

      <q-dialog v-model="showArchiveDialog">
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="archive" color="negative" text-color="white" />
            <span class="q-ml-sm">Are you sure you want to archive this blog?</span>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Cancel" color="primary" v-close-popup />
            <q-btn flat label="Archive" color="negative" @click="archiveBlog" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { date } from 'quasar';
import axios from 'axios';
import BlogFormModal from '../../components/blogs/BlogFormModal.vue';

const columns = [
  { name: 'image', align: 'center', label: 'Image', field: 'image', sortable: false },
  { name: 'title', align: 'left', label: 'Title', field: 'title', sortable: true },
  { name: 'status', align: 'left', label: 'Status', field: 'status', sortable: true },
  { name: 'created_at', align: 'left', label: 'Created At', field: 'created_at', sortable: true },
  { name: 'actions', align: 'center', label: 'Actions', field: 'actions', sortable: false }
];

const blogs = ref([]);
const filteredBlogs = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
});

const showBlogModal = ref(false);
const selectedBlog = ref(null);
const showArchiveDialog = ref(false);

const formatDate = (dateString) => {
  return date.formatDate(dateString, 'YYYY-MM-DD HH:mm');
};

const fetchBlogs = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/blogs', {
      params: {
        page: pagination.value.page,
        perPage: pagination.value.rowsPerPage,
        sortBy: pagination.value.sortBy,
        descending: pagination.value.descending,
        search: searchQuery.value
      }
    });

    blogs.value = Array.isArray(response.data.data.data) ? response.data.data.data : [];
    filteredBlogs.value = blogs.value;
    pagination.value.rowsNumber = response.data.data.total || 0;
  } catch (error) {
    console.error('Failed to fetch blogs:', error);
    blogs.value = [];
    filteredBlogs.value = [];
  } finally {
    loading.value = false;
  }
};

const onRequest = (props) => {
  const { page, rowsPerPage, sortBy, descending } = props.pagination;
  pagination.value.page = page;
  pagination.value.rowsPerPage = rowsPerPage;
  pagination.value.sortBy = sortBy;
  pagination.value.descending = descending;
  fetchBlogs();
};

const onSearch = () => {
  fetchBlogs();
};

const refreshBlogs = () => {
  fetchBlogs();
};

const editBlog = (blog) => {
  selectedBlog.value = {...blog};
  showBlogModal.value = true;
};

const toggleStatus = async (blog) => {
  try {
    const newStatus = blog.status === 'published' ? 'draft' : 'published';
    await axios.patch(`/api/blogs/${blog.id}/status`, {
      status: newStatus
    });
    fetchBlogs();
  } catch (error) {
    console.error('Failed to update blog status:', error);
  }
};

const slugify = (text) => {
  return text.toLowerCase().replace(/ /g, '-');
};

const previewBlog = (blog) => {
  window.open(`/blogs/${blog.slug}`, '_blank');
};

const confirmArchive = (blog) => {
  selectedBlog.value = blog;
  showArchiveDialog.value = true;
};

const archiveBlog = async () => {
  try {
    await axios.delete(`/api/blogs/${selectedBlog.value.id}`);
    fetchBlogs();
  } catch (error) {
    console.error('Failed to archive blog:', error);
  }
};

const addNewBlog = () => {
  selectedBlog.value = null;
  showBlogModal.value = true;
};

const closeBlogModal = () => {
  showBlogModal.value = false;
  selectedBlog.value = null;
};

onMounted(() => {
  fetchBlogs();
});
</script> 