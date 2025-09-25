<template>
    <div class="bg-white min-h-screen">
        <div class="bg-gradient-to-b from-primary/15 to-white py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs uppercase tracking-wide text-primary">{{ post?.category?.name ?? 'Berita Desa' }}</p>
                <h1 class="mt-4 text-4xl font-bold text-slate-900">{{ post?.title }}</h1>
                <p class="mt-3 text-sm text-slate-500">{{ formatDate(post?.published_at) }}</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20">
            <article class="rounded-3xl bg-white p-6 md:p-12 shadow-xl border border-slate-100">
                <img v-if="post?.cover_image" :src="post.cover_image" :alt="post.title" class="w-full rounded-2xl object-cover" />
                <div class="prose prose-slate max-w-none mt-8" v-html="post?.body"></div>
            </article>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const post = ref(null);

async function fetchPost(slug) {
    const { data } = await axios.get(`/news/slug/${slug}`);
    post.value = data;
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

onMounted(() => fetchPost(route.params.slug));

watch(
    () => route.params.slug,
    (slug) => {
        if (slug) {
            fetchPost(slug);
        }
    }
);
</script>
