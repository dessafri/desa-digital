<template>
    <div class="space-y-8 p-8">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1.5">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-primary/70">Konten Desa</p>
                <h1 class="text-3xl font-semibold text-slate-900">Daftar Berita Desa</h1>
                <p class="text-sm text-slate-500">Kelola rilis berita yang terbit maupun masih draft dalam satu tempat.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-primary/90"
                    @click="goToCreate"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                    </svg>
                    Tambah Berita
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-primary hover:text-primary"
                    @click="loadNews()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 4.5v6h6M19.5 19.5v-6h-6M5.876 18.124A8.25 8.25 0 0 1 18.124 5.876" />
                    </svg>
                    Muat ulang
                </button>
            </div>
        </header>

        <div class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Total</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ summary.total }}</p>
                <p class="mt-1 text-xs text-slate-500">Seluruh berita tercatat</p>
            </article>
            <article class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5 text-emerald-700">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">Published</p>
                <p class="mt-3 text-3xl font-semibold">{{ summary.published }}</p>
                <p class="mt-1 text-xs opacity-80">Telah tayang di portal</p>
            </article>
            <article class="rounded-2xl border border-amber-100 bg-amber-50/80 p-5 text-amber-600">
                <p class="text-xs uppercase tracking-[0.3em] text-amber-500">Draft</p>
                <p class="mt-3 text-3xl font-semibold">{{ summary.draft }}</p>
                <p class="mt-1 text-xs opacity-80">Menunggu publikasi</p>
            </article>
        </div>

        <section class="space-y-6 rounded-3xl border border-slate-200 bg-white/95 p-6 shadow-sm backdrop-blur">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Katalog Berita</h2>
                    <p class="text-xs text-slate-500">Filter berdasarkan status rilis.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        :class="tabButtonClass(tab.id)"
                        @click="activeTab = tab.id"
                    >
                        {{ tab.label }}
                        <span :class="tabBadgeClass(tab.id)">{{ tabCounts[tab.id] ?? 0 }}</span>
                    </button>
                </div>
            </div>

            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>

            <template v-else>
                <div v-if="filteredPosts.length" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="post in filteredPosts"
                        :key="post.id"
                        class="flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <span :class="statusPillClass(post.status)">{{ statusLabel(post.status) }}</span>
                                <span class="text-[11px] uppercase tracking-[0.25em] text-slate-400">{{ formatDate(post.published_at || post.updated_at) }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 line-clamp-2">{{ post.title }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-3">{{ post.excerpt || 'Belum ada ringkasan singkat.' }}</p>
                        </div>
                        <div class="mt-5 flex items-center justify-between text-xs font-semibold">
                            <p class="text-slate-400">{{ post.category?.name ?? 'Tanpa kategori' }}</p>
                            <div class="flex items-center gap-4">
                                <button
                                    type="button"
                                    class="text-primary hover:text-primary/80"
                                    @click="goToEdit(post.id)"
                                >
                                    Ubah
                                </button>
                                <button class="text-rose-500 hover:text-rose-400" @click="deletePost(post)">Hapus</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 py-16 text-center">
                    <p class="text-sm font-semibold text-slate-600">Belum ada berita pada filter ini.</p>
                    <p class="mt-2 text-xs text-slate-400">Gunakan tombol "Tambah Berita" untuk mulai menulis.</p>
                </div>
            </template>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const goToCreate = () => {
    router.push('/admin/berita/tambah');
};

const goToEdit = (id) => {
    router.push(`/admin/berita/${id}/edit`);
};

const tabs = [
    { id: 'all', label: 'Semua' },
    { id: 'published', label: 'Published' },
    { id: 'draft', label: 'Draft' },
];

const posts = ref([]);
const loading = ref(false);
const activeTab = ref('all');

const filteredPosts = computed(() => {
    if (activeTab.value === 'all') {
        return posts.value;
    }
    return posts.value.filter((post) => post.status === activeTab.value);
});

const tabCounts = computed(() => {
    const counts = { all: posts.value.length, published: 0, draft: 0 };
    posts.value.forEach((post) => {
        if (post.status === 'published') counts.published += 1;
        if (post.status === 'draft') counts.draft += 1;
    });
    return counts;
});

const summary = computed(() => ({
    total: tabCounts.value.all,
    published: tabCounts.value.published,
    draft: tabCounts.value.draft,
}));

function tabButtonClass(id) {
    const base = 'inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold transition';
    if (activeTab.value === id) {
        return `${base} bg-primary text-white shadow`;
    }
    return `${base} border border-slate-200 text-slate-600 hover:border-primary/40 hover:text-primary`;
}

function tabBadgeClass(id) {
    if (activeTab.value === id) {
        return 'rounded-full bg-white/30 px-2 py-0.5 text-[10px] font-semibold text-white';
    }
    return 'rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-600';
}

function statusLabel(status) {
    return status === 'published' ? 'Published' : 'Draft';
}

function statusPillClass(status) {
    const base = 'inline-flex h-7 items-center rounded-full px-3 text-xs font-semibold';
    if (status === 'published') {
        return `${base} bg-emerald-50 text-emerald-600`;
    }
    return `${base} bg-slate-100 text-slate-500`;
}

async function loadNews() {
    loading.value = true;
    try {
        const response = await axios.get('/news', {
            params: {
                include_draft: 1,
                per_page: 100,
            },
        });
        const payload = response.data;
        const rows = Array.isArray(payload)
            ? payload
            : Array.isArray(payload?.data)
            ? payload.data
            : Array.isArray(payload?.data?.data)
            ? payload.data.data
            : [];
        posts.value = rows
            .map((item) => ({
                ...item,
                status: item.status || (item.published_at ? 'published' : 'draft'),
            }))
            .sort((a, b) =>
                new Date(b.published_at || b.updated_at || b.created_at || 0) -
                new Date(a.published_at || a.updated_at || a.created_at || 0)
            );
    } finally {
        loading.value = false;
    }
}

async function deletePost(post) {
    if (!confirm(`Hapus berita "${post.title}"?`)) {
        return;
    }
    await axios.delete(`/news/${post.id}`);
    await loadNews();
}

function formatDate(date) {
    if (!date) {
        return '-';
    }
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
}

onMounted(loadNews);
</script>

