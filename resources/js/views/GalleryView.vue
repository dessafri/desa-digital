<template>
    <div class="bg-slate-50 min-h-screen">
        <section class="bg-white shadow-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
                <h1 class="text-3xl font-bold text-slate-900">Galeri Desa Sugihwaras</h1>
                <p class="mt-3 text-sm text-slate-600">Dokumentasi kegiatan, program, dan kebersamaan warga Desa Sugihwaras.</p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
            <div v-else class="grid gap-6 md:grid-cols-2">
                <RouterLink
                    v-for="album in albums"
                    :key="album.id"
                    :to="/galeri/"
                    class="group overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-md transition hover:-translate-y-2"
                >
                    <div class="relative h-64 overflow-hidden">
                        <img :src="album.cover_image || album.items?.[0]?.image_url" :alt="album.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent"></div>
                        <div class="absolute bottom-0 p-6 text-white">
                            <h2 class="text-xl font-semibold">{{ album.title }}</h2>
                            <p class="mt-2 text-xs text-white/80 line-clamp-2">{{ album.description }}</p>
                        </div>
                    </div>
                    <div class="p-6 border-t border-slate-100">
                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ album.items?.length ?? 0 }} Dokumentasi</p>
                    </div>
                </RouterLink>
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const albums = ref([]);
const loading = ref(false);

async function loadAlbums() {
    loading.value = true;
    try {
        const { data } = await axios.get('/gallery-albums', {
            params: {
                with: 'items',
            },
        });
        albums.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(loadAlbums);
</script>
