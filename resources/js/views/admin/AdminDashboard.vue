<template>
    <div class="min-h-full bg-slate-50 px-4 py-8 sm:px-6 lg:px-10">
        <!-- Hero summary -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-primary/85 to-secondary text-white shadow-xl">
            <div class="absolute -top-24 -right-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-8 p-8 sm:p-10 lg:flex-row lg:items-center lg:gap-16 lg:p-12">
                <div class="flex-1 space-y-5">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-white/80">
                        Dashboard Desa
                    </span>
                    <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">
                        Ringkasan Aktivitas & Layanan Desa Sugihwaras
                    </h1>
                    <p class="max-w-xl text-sm text-white/80">
                        Pantau performa portal desa secara real-time: publikasi berita, pemutakhiran layanan digital, statistik masyarakat, dan dokumentasi kegiatan.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button
                            class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2 text-xs font-semibold text-slate-900 shadow-md shadow-slate-900/10 transition hover:bg-white/90"
                            @click="loadDashboard"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 4.5v15m15-15v15m-15-7.5h15" />
                            </svg>
                            Muat ulang data
                        </button>
                        <RouterLink
                            to="/"
                            target="_blank"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 px-5 py-2 text-xs font-semibold text-white/80 transition hover:border-white hover:text-white"
                        >
                            Lihat portal publik
                        </RouterLink>
                    </div>
                </div>
                <div class="grid flex-1 gap-4 sm:grid-cols-2">
                    <article
                        v-for="metric in metrics"
                        :key="metric.title"
                        class="rounded-2xl border border-white/10 bg-white/10 p-5 text-left backdrop-blur"
                    >
                        <div class="flex items-center justify-between">
                            <div class="rounded-xl bg-white/15 p-2 text-white/80">
                                <component :is="metric.icon" class="h-5 w-5" />
                            </div>
                            <span class="text-[11px] font-semibold uppercase tracking-widest text-white/70">{{ metric.title }}</span>
                        </div>
                        <p class="mt-4 text-3xl font-semibold text-white">{{ metric.value }}</p>
                        <p class="mt-1 text-[11px] text-white/70">{{ metric.subtitle }}</p>
                    </article>
                </div>
            </div>
        </section>

        <transition name="fade">
            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
        </transition>

        <div v-if="!loading" class="mt-10 space-y-10">
            <!-- Quick insights -->
            <section class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
                <article class="rounded-3xl bg-white p-8 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Berita Terbaru</h2>
                            <p class="text-xs text-slate-500">{{ recentNews.length ? 'Status 3 berita terakhir' : 'Belum ada berita yang dipublikasikan' }}</p>
                        </div>
                        <RouterLink to="/admin/berita" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary/10 hover:text-primary">
                            Kelola Berita
                        </RouterLink>
                    </div>
                    <ul class="mt-6 divide-y divide-slate-100 text-sm">
                        <li v-for="post in newsPreview" :key="post.id" class="flex flex-col gap-1 py-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-slate-900 line-clamp-1">{{ post.title }}</p>
                                <span :class="post.status === 'published' ? 'text-emerald-600' : 'text-slate-400'" class="text-[11px] font-semibold uppercase tracking-widest">
                                    {{ post.status === 'published' ? 'Publish' : 'Draft' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">{{ formatDate(post.published_at) }}</p>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ post.excerpt || 'Tanpa ringkasan.' }}</p>
                        </li>
                        <li v-if="!newsPreview.length" class="py-10 text-center text-xs text-slate-400">
                            Belum ada berita yang dapat ditampilkan.
                        </li>
                    </ul>
                </article>
                <article class="rounded-3xl bg-white p-8 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Aktivitas Sistem</h2>
                            <p class="text-xs text-slate-500">Log singkat perubahan terbaru</p>
                        </div>
                        <RouterLink to="/admin/pengaturan" class="text-xs font-semibold text-primary hover:underline">
                            Detail
                        </RouterLink>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li v-for="log in activity" :key="log.id" class="rounded-2xl border border-slate-100 px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ log.title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ log.description }}</p>
                        </li>
                        <li v-if="!activity.length" class="text-xs text-slate-400">
                            Aktivitas terbaru akan muncul di sini.
                        </li>
                    </ul>
                </article>
            </section>

            <!-- Quick links -->
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <RouterLink
                    v-for="link in quickLinks"
                    :key="link.to"
                    :to="link.to"
                    class="group rounded-3xl border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-primary/40"
                >
                    <div class="flex items-center justify-between">
                        <span class="rounded-2xl bg-primary/10 p-3 text-primary transition group-hover:bg-primary group-hover:text-white">
                            <component :is="link.icon" class="h-5 w-5" />
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 transition group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-base font-semibold text-slate-900">
                        {{ link.label }}
                    </h3>
                    <p class="mt-2 text-xs text-slate-500">{{ link.description }}</p>
                </RouterLink>
            </section>

            <!-- Statistics detail -->
            <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <article class="rounded-3xl bg-white p-8 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Statistik Desa</h2>
                            <p class="text-xs text-slate-500">Ikhtisar data demografi & layanan</p>
                        </div>
                        <RouterLink to="/admin/statistik" class="text-xs font-semibold text-primary hover:underline">
                            Kelola Statistik
                        </RouterLink>
                    </div>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div
                            v-for="stat in statPreview"
                            :key="stat.id"
                            class="rounded-2xl border border-slate-100 bg-slate-50 p-5"
                        >
                            <p class="text-xs uppercase tracking-widest text-primary/70">{{ stat.label }}</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ formatNumber(stat.value) }}</p>
                        </div>
                    </div>
                </article>
                <article class="rounded-3xl bg-white p-8 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Album Galeri Terakhir</h2>
                    <p class="text-xs text-slate-500">Snapshot dokumentasi kegiatan desa</p>
                    <div v-if="galleryPreview" class="mt-6 space-y-4">
                        <div class="h-40 overflow-hidden rounded-2xl">
                            <img :src="galleryPreview.cover_image || galleryPreview.items?.[0]?.image_url" :alt="galleryPreview.title" class="h-full w-full object-cover" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-900">{{ galleryPreview.title }}</p>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ galleryPreview.description }}</p>
                        </div>
                        <RouterLink to="/admin/galeri" class="inline-flex items-center gap-2 text-xs font-semibold text-primary">
                            Kelola Galeri
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </RouterLink>
                    </div>
                    <div v-else class="mt-8 text-center text-xs text-slate-400">
                        Belum ada galeri yang ditambahkan.
                    </div>
                </article>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import {
    Squares2X2Icon,
    NewspaperIcon,
    BoltIcon,
    ChartBarIcon,
    PhotoIcon,
    UsersIcon,
    Cog6ToothIcon,
} from '@heroicons/vue/24/outline';

const loading = ref(false);
const summary = reactive({
    news: 0,
    services: 0,
    statistics: 0,
    gallery: 0,
});
const recentNews = ref([]);
const statisticItems = ref([]);
const activity = ref([]);
const galleryPreview = ref(null);

const metrics = computed(() => [
    { title: 'Berita', value: summary.news, subtitle: 'Artikel termasuk draft', icon: NewspaperIcon },
    { title: 'Layanan', value: summary.services, subtitle: 'Layanan aktif di portal', icon: BoltIcon },
    { title: 'Statistik', value: summary.statistics, subtitle: 'Indikator desa tercatat', icon: ChartBarIcon },
    { title: 'Album Galeri', value: summary.gallery, subtitle: 'Dokumentasi kegiatan', icon: PhotoIcon },
]);

const quickLinks = [
    { label: 'Kelola Berita', description: 'Tambah, ubah, dan terbitkan berita desa', to: '/admin/berita', icon: NewspaperIcon },
    { label: 'Layanan Digital', description: 'Atur katalog layanan administratif', to: '/admin/layanan', icon: BoltIcon },
    { label: 'Perangkat Desa', description: 'Perbarui struktur dan kontak perangkat', to: '/admin/pejabat', icon: UsersIcon },
    { label: 'Pengaturan Portal', description: 'Identitas visual, sambutan, dan tema', to: '/admin/pengaturan', icon: Cog6ToothIcon },
];

const newsPreview = computed(() => recentNews.value.slice(0, 3));
const statPreview = computed(() => {
    if (!statisticItems.value.length) {
        return [
            { id: 'stat-fallback-1', label: 'Belum ada data statistik', value: '-' },
        ];
    }
    return statisticItems.value.slice(0, 4);
});

async function loadDashboard() {
    loading.value = true;
    try {
        const [newsRes, serviceRes, statisticRes, galleryRes] = await Promise.all([
            axios.get('/news', { params: { include_draft: true, per_page: 6 } }),
            axios.get('/services'),
            axios.get('/statistics'),
            axios.get('/gallery-albums'),
        ]);

        summary.news = newsRes.data.total ?? newsRes.data.data?.length ?? 0;
        summary.services = serviceRes.data.length ?? 0;
        summary.statistics = statisticRes.data.length ?? 0;
        statisticItems.value = statisticRes.data ?? [];
        summary.gallery = galleryRes.data.length ?? 0;
        recentNews.value = newsRes.data.data ?? [];
        galleryPreview.value = galleryRes.data[0] ?? null;

        activity.value = [
            {
                id: 'news',
                title: 'Pembaruan berita desa',
                description: `${recentNews.value.length} berita tersinkronisasi dengan sistem`,
            },
            {
                id: 'services',
                title: 'Layanan publik aktif',
                description: `${summary.services} layanan tersedia bagi warga secara daring`,
            },
            {
                id: 'gallery',
                title: 'Album galeri',
                description: `${summary.gallery} album mendokumentasikan kegiatan terbaru`,
            },
        ];
    } finally {
        loading.value = false;
    }
}

function formatNumber(value) {
    if (value === '-' || value === undefined || value === null) {
        return '-';
    }
    if (typeof value === 'string') {
        return value;
    }
    if (value > 1000) {
        return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(value);
    }
    return new Intl.NumberFormat('id-ID').format(value);
}

function formatDate(date) {
    if (!date) {
        return 'Draft';
    }
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
}

onMounted(loadDashboard);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
