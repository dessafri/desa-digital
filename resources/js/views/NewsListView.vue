<template>
    <div class="bg-slate-50 min-h-screen">
        <section class="bg-white shadow-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h1 class="text-3xl font-bold text-slate-900">Berita Desa Sugihwaras</h1>
                <p class="mt-3 text-sm text-slate-600">
                    Update kegiatan, program, dan informasi penting yang berlangsung di Desa Sugihwaras.
                </p>
                <div class="mt-8 grid gap-4 sm:grid-cols-[1fr_auto_auto]">
                    <div class="relative">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Cari berita..."
                            class="w-full rounded-full border border-slate-200 bg-white px-5 py-3 text-sm shadow-sm focus:border-primary focus:outline-none"
                            @keyup.enter="loadNews()"
                        />
                        <button
                            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white"
                            @click="loadNews()"
                        >
                            Cari
                        </button>
                    </div>
                    <select
                        v-model="filters.category"
                        class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600"
                        @change="loadNews()"
                    >
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.slug">
                            {{ category.name }}
                        </option>
                    </select>
                    <select
                        v-model="filters.per_page"
                        class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600"
                        @change="loadNews()"
                    >
                        <option :value="6">6 / halaman</option>
                        <option :value="9">9 / halaman</option>
                        <option :value="12">12 / halaman</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
            <div v-else>
                <div v-if="posts.length" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="post in posts"
                        :key="post.id"
                        class="group overflow-hidden rounded-3xl bg-white shadow-lg border border-slate-100 transition hover:-translate-y-2"
                    >
                        <div class="relative h-48 overflow-hidden">
                            <img :src="post.cover_image" :alt="post.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                            <span class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-primary">
                                {{ post.category?.name ?? 'Informasi Desa' }}
                            </span>
                        </div>
                        <div class="p-6 space-y-3">
                            <p class="text-xs uppercase tracking-wide text-slate-400">{{ formatDate(post.published_at) }}</p>
                            <RouterLink :to="/berita/" class="block text-lg font-semibold text-slate-900 hover:text-primary line-clamp-2">
                                {{ post.title }}
                            </RouterLink>
                            <p class="text-sm text-slate-600 line-clamp-3">{{ plainText(post.excerpt) }}</p>
                            <RouterLink :to="/berita/" class="inline-flex items-center gap-2 text-sm font-semibold text-primary">
                                Baca Selengkapnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </RouterLink>
                        </div>
                    </article>
                </div>
                <div v-else class="rounded-3xl border border-slate-100 bg-white p-10 text-center text-sm text-slate-500">
                    Belum ada berita yang dapat ditampilkan.
                </div>

                <div v-if="pagination.total > pagination.per_page" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                    <button
                        v-for="page in pages"
                        :key="page"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border text-sm"
                        :class="page === pagination.current_page ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-primary'"
                        @click="changePage(page)"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const posts = ref([]);
const categories = ref([]);
const loading = ref(false);
const pagination = reactive({
    total: 0,
    current_page: 1,
    per_page: 6,
});

const filters = reactive({
    search: '',
    category: '',
    per_page: 6,
});

const pages = computed(() => {
    const totalPages = Math.ceil((pagination.total || 0) / pagination.per_page) || 1;
    return Array.from({ length: totalPages }, (_, index) => index + 1);
});

async function loadCategories() {
    const { data } = await axios.get('/news-categories');
    categories.value = data;
}

async function loadNews(page = 1) {
    loading.value = true;
    try {
        const { data } = await axios.get('/news', {
            params: {
                page,
                search: filters.search || undefined,
                category: filters.category || undefined,
                per_page: filters.per_page,
            },
        });
        posts.value = data.data ?? [];
        pagination.total = data.total ?? 0;
        pagination.current_page = data.current_page ?? 1;
        pagination.per_page = data.per_page ?? filters.per_page;
    } finally {
        loading.value = false;
    }
}

function changePage(page) {
    if (page === pagination.current_page) {
        return;
    }
    loadNews(page);
}

function formatDate(date) {
    if (!date) {
        return '';
    }
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
}

function plainText(html) {
    if (!html) return '';
    return String(html).replace(/<[^>]*>/g, '');
}

onMounted(async () => {
    await loadCategories();
    await loadNews();
});
</script>
