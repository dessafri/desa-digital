<template>
    <div class="min-h-screen bg-slate-100 text-slate-800">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside :class="sidebarWrapperClass">
                <div class="space-y-8">
                    <div :class="showSidebarLabels ? 'flex items-center justify-between' : 'flex flex-col items-center gap-6'">
                        <RouterLink
                            to="/"
                            class="flex items-center gap-3 text-left text-white transition hover:text-white/80"
                            :class="showSidebarLabels ? '' : 'justify-center'
                        "
                        >
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-lg font-semibold text-secondary">
                                DD
                            </span>
                            <div v-if="showSidebarLabels" class="leading-tight">
                                <p class="text-sm font-semibold tracking-wide text-white/60">Desa Sugihwaras</p>
                                <h1 class="text-lg font-bold">Admin Center</h1>
                            </div>
                        </RouterLink>
                        <button
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white/70 transition hover:border-white/30 hover:text-white"
                            @click="toggleSidebarCollapsed"
                            :title="showSidebarLabels ? 'Sembunyikan sidebar' : 'Tampilkan sidebar'"
                        >
                            <component :is="sidebarCollapsed ? ChevronDoubleRightIcon : ChevronDoubleLeftIcon" class="h-4 w-4" />
                        </button>
                    </div>

                    <nav class="space-y-1 text-sm font-medium">
                        <p v-if="showSidebarLabels" class="mb-3 text-xs uppercase tracking-wide text-white/40">Navigasi</p>
                        <RouterLink
                            v-for="item in menu"
                            :key="item.to"
                            :to="item.to"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition"
                            :class="[
                                sidebarCollapsed ? 'justify-center' : 'justify-start',
                                activeMatch(item.match)
                                    ? 'bg-white text-slate-900 shadow-lg'
                                    : 'text-white/70 hover:bg-white/10'
                            ]"
                            :title="sidebarCollapsed ? item.label : undefined"
                        >
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg"
                                :class="activeMatch(item.match) ? 'bg-slate-900/5 text-primary' : 'bg-white/10 text-white'"
                            >
                                <component :is="item.icon" class="h-5 w-5" />
                            </span>
                            <span v-if="showSidebarLabels">{{ item.label }}</span>
                        </RouterLink>
                    </nav>
                </div>

                <div>
                    <div v-if="showSidebarLabels" class="rounded-2xl bg-white/10 p-4 text-xs text-white/70">
                        <p class="font-semibold text-white">Bantuan?</p>
                        <p class="mt-2 leading-relaxed">Hubungi admin desa bila membutuhkan akses tambahan atau pelatihan penggunaan panel.</p>
                        <a href="mailto:admin@sugihwaras.desa.id" class="mt-3 inline-flex items-center gap-2 text-primary">
                            Email Admin
                        </a>
                    </div>
                    <button
                        v-else
                        class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-white/60 transition hover:bg-white/20 hover:text-white"
                        title="Hubungi admin"
                        @click="openMail"
                    >
                        <LifebuoyIcon class="h-5 w-5" />
                    </button>
                </div>
            </aside>

            <!-- Main -->
            <div class="flex min-h-screen flex-1 flex-col">
                <!-- Topbar -->
                <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
                    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-10">
                        <div class="flex items-center gap-3">
                            <button
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-primary hover:text-primary lg:hidden"
                                @click="sidebarOpen = !sidebarOpen"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Menu
                            </button>
                            <div class="hidden lg:block">
                                <h2 class="text-lg font-semibold text-slate-800">{{ currentTitle }}</h2>
                                <p class="text-xs text-slate-500">Kelola konten dan layanan digital Desa Sugihwaras</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a
                                href="/"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-primary hover:text-primary"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5M15 3h6m0 0v6m0-6-10 10" />
                                </svg>
                                Lihat Situs
                            </a>
                            <div class="hidden items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500 sm:flex">
                                <span class="h-2 w-2 rounded-full bg-secondary"></span>
                                Mode Admin Aktif
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Mobile sidebar -->
                <transition name="fade">
                    <div
                        v-if="sidebarOpen"
                        class="fixed inset-0 z-50 bg-slate-900/60 lg:hidden"
                        @click.self="sidebarOpen = false"
                    >
                        <aside class="absolute inset-y-0 left-0 w-72 bg-slate-900 px-6 py-8 text-white shadow-xl">
                            <button class="mb-8 inline-flex items-center gap-2 text-sm" @click="sidebarOpen = false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                Tutup
                            </button>
                            <nav class="space-y-2 text-sm font-medium">
                                <RouterLink
                                    v-for="item in menu"
                                    :key="item.to"
                                    :to="item.to"
                                    class="block rounded-xl px-4 py-3"
                                    :class="activeMatch(item.match) ? 'bg-white text-slate-900 shadow-lg' : 'text-white/70 hover:bg-white/10'"
                                    @click="sidebarOpen = false"
                                >
                                    {{ item.label }}
                                </RouterLink>
                            </nav>
                        </aside>
                    </div>
                </transition>

                <!-- Content -->
                <main class="mx-auto flex w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-10">
                    <RouterView v-slot="{ Component, route }" class="w-full">
                        <transition name="fade" mode="out-in" appear>
                            <component :is="Component" :key="route.fullPath" />
                        </transition>
                    </RouterView>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import {
    Squares2X2Icon,
    NewspaperIcon,
    BoltIcon,
    ChartBarIcon,
    PhotoIcon,
    UsersIcon,
    Cog6ToothIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    LifebuoyIcon,
} from '@heroicons/vue/24/outline';

const route = useRoute();
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(true);

const menu = computed(() => [
    { label: 'Dashboard', to: '/admin', match: 'admin.dashboard', icon: Squares2X2Icon },
    { label: 'Berita Desa', to: '/admin/berita', match: 'admin.news', icon: NewspaperIcon },
    { label: 'Layanan Digital', to: '/admin/layanan', match: 'admin.services', icon: BoltIcon },
    { label: 'Statistik Desa', to: '/admin/statistik', match: 'admin.statistics', icon: ChartBarIcon },
    { label: 'Galeri Desa', to: '/admin/galeri', match: 'admin.gallery', icon: PhotoIcon },
    { label: 'Perangkat Desa', to: '/admin/pejabat', match: 'admin.officials', icon: UsersIcon },
    { label: 'Pengaturan', to: '/admin/pengaturan', match: 'admin.settings', icon: Cog6ToothIcon },
]);

const titles = {
    'admin.dashboard': 'Ringkasan Desa Digital',
    'admin.news': 'Kelola Berita Desa',
    'admin.news.create': 'Tambah Berita',
    'admin.news.edit': 'Perbarui Berita',
    'admin.services': 'Kelola Layanan Digital',
    'admin.statistics': 'Kelola Statistik Desa',
    'admin.gallery': 'Kelola Galeri Desa',
    'admin.officials': 'Kelola Perangkat Desa',
    'admin.settings': 'Pengaturan Portal',
};

const currentTitle = computed(() => {
    return titles[route.name] || 'Panel Admin Desa';
});

const sidebarWrapperClass = computed(() => {
    const base = 'hidden lg:flex flex-col justify-between bg-slate-900/95 text-white shadow-xl transition-all duration-300';
    return sidebarCollapsed.value ? `${base} w-20 px-3 py-6` : `${base} w-72 px-6 py-8`;
});

const showSidebarLabels = computed(() => !sidebarCollapsed.value);

function toggleSidebarCollapsed() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
}

function openMail() {
    window.location.href = 'mailto:admin@sugihwaras.desa.id';
}

function activeMatch(match) {
    return route.name?.startsWith(match);
}
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
