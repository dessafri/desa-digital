<template>
    <div class="bg-slate-50 min-h-screen">
        <div class="bg-gradient-to-b from-secondary/15 to-white py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs uppercase tracking-wide text-secondary">Galeri Desa</p>
                <h1 class="mt-4 text-4xl font-bold text-slate-900">{{ album?.title }}</h1>
                <p class="mt-3 text-sm text-slate-600 max-w-3xl mx-auto">{{ album?.description }}</p>
            </div>
        </div>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20">
            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-secondary/30 border-t-secondary"></div>
            </div>
            <div v-else-if="album" class="rounded-3xl bg-white p-6 md:p-10 shadow-lg border border-slate-100">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <figure
                        v-for="item in album.items"
                        :key="item.id"
                        class="group relative overflow-hidden rounded-2xl"
                    >
                        <img :src="item.image_url" :alt="item.title" class="h-60 w-full object-cover transition duration-500 group-hover:scale-110" />
                        <figcaption class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent p-4 text-white flex flex-col justify-end">
                            <p class="text-sm font-semibold">{{ item.title }}</p>
                            <p class="text-xs text-white/70">{{ item.caption }}</p>
                        </figcaption>
                    </figure>
                </div>
            </div>
            <div v-else class="rounded-3xl border border-slate-100 bg-white p-10 text-center text-sm text-slate-500">
                Album tidak ditemukan.
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const album = ref(null);
const loading = ref(false);

async function loadAlbum(slug) {
    if (!slug) {
        return;
    }
    loading.value = true;
    try {
        const { data } = await axios.get(`/gallery-albums/slug/${slug}`);
        album.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(() => loadAlbum(route.params.slug));

watch(
    () => route.params.slug,
    (slug) => {
        if (slug) {
            loadAlbum(slug);
        }
    }
);
</script>
