<template>
    <div class="space-y-8 p-8">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1.5">
                <button
                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.35em] text-primary/70"
                    @click="goBack"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </button>
                <h1 class="text-3xl font-semibold text-slate-900">{{ pageTitle }}</h1>
                <p class="text-sm text-slate-500">Lengkapi konten berita dan publikasikan ketika sudah siap.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-primary hover:text-primary"
                    type="button"
                    @click="resetForm"
                >
                    Reset Form
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-70"
                    type="button"
                    @click="submitForm"
                    :disabled="submitting || loadingPost"
                >
                    <span v-if="submitting" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364 6.364-2.121-2.121M8.757 8.757 6.636 6.636m12.728 0-2.121 2.121M8.757 15.243 6.636 17.364" />
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else>{{ submitLabel }}</span>
                </button>
            </div>
        </header>

        <section class="space-y-6 rounded-3xl border border-slate-200 bg-white/95 p-6 shadow-sm backdrop-blur">
            <form class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_1fr]" @submit.prevent="submitForm">
                <div v-if="loadingPost" class="col-span-full grid place-items-center py-20">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
                </div>
                <template v-else>
                    <div class="space-y-6">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Informasi Utama</h2>
                            <div class="mt-5 space-y-4 text-sm">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Judul berita</label>
                                    <input v-model="form.title" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Slug (opsional)</label>
                                    <input v-model="form.slug" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="otomatis mengikuti judul" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Ringkasan singkat</label>
                                    <textarea v-model="form.excerpt" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ringkasan maksimal 2-3 kalimat"></textarea>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-900">Isi Berita</h2>
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-400">CKEditor</span>
                            </div>
                            <div class="mt-4 space-y-4 text-sm">
                                <Ckeditor
                                    :editor="editor"
                                    v-model="form.body"
                                    :config="editorConfig"
                                />
                            </div>
                        </article>
                    </div>

                    <div class="space-y-6">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Pengaturan Rilis</h2>
                            <div class="mt-5 space-y-4 text-sm">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Kategori</label>
                                    <select v-model="form.news_category_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                        <option value="">Tanpa kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Status</label>
                                    <select v-model="form.status" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                <div class="space-y-1" v-if="form.status === 'published'">
                                    <label class="text-xs font-semibold text-slate-500">Tanggal publikasi</label>
                                    <input v-model="form.published_at" type="datetime-local" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Gambar sampul (URL)</label>
                                    <input v-model="form.cover_image" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="https://" />
                                </div>
                            </div>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-xs text-slate-500">
                            <h2 class="text-sm font-semibold text-slate-900">Tips Penulisan</h2>
                            <ul class="mt-3 space-y-2 list-disc pl-4">
                                <li>Pastikan judul singkat dan jelas.</li>
                                <li>Sertakan ringkasan agar tampil rapi di kartu beranda.</li>
                                <li>Gunakan gambar sampul beresolusi minimal 1200x675px.</li>
                            </ul>
                        </article>
                    </div>
                </template>

                <button type="submit" class="hidden">Simpan</button>
            </form>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import CKEditor from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const { component: ckeditor } = CKEditor;
const Ckeditor = ckeditor;
const editor = ClassicEditor;
const editorConfig = {
    toolbar: [
        'heading',
        '|',
        'bold',
        'italic',
        'link',
        'bulletedList',
        'numberedList',
        'blockQuote',
        'insertTable',
        'undo',
        'redo',
    ],
    language: 'id',
};

const route = useRoute();
const router = useRouter();

const categories = ref([]);
const submitting = ref(false);
const loadingPost = ref(false);

const form = reactive({
    id: null,
    news_category_id: '',
    title: '',
    slug: '',
    excerpt: '',
    body: '',
    cover_image: '',
    status: 'draft',
    published_at: '',
});

const isEditMode = computed(() => Boolean(route.params.id));
const pageTitle = computed(() => (isEditMode.value ? 'Perbarui Berita' : 'Tambah Berita Baru'));
const submitLabel = computed(() => (isEditMode.value ? 'Perbarui Berita' : 'Simpan Berita'));

function setDefaults() {
    form.id = null;
    form.news_category_id = '';
    form.title = '';
    form.slug = '';
    form.excerpt = '';
    form.body = '';
    form.cover_image = '';
    form.status = 'draft';
    form.published_at = '';
}

function goBack() {
    router.push({ name: 'admin.news' });
}

function resetForm() {
    if (isEditMode.value) {
        loadPost();
    } else {
        setDefaults();
    }
}

async function loadCategories() {
    const { data } = await axios.get('/news-categories');
    categories.value = data;
}

async function loadPost() {
    if (!isEditMode.value) {
        setDefaults();
        return;
    }
    loadingPost.value = true;
    try {
        const { data } = await axios.get(`/news/${route.params.id}`);
        form.id = data.id;
        form.news_category_id = data.news_category_id ?? '';
        form.title = data.title || '';
        form.slug = data.slug || '';
        form.excerpt = data.excerpt || '';
        form.body = data.body || '';
        form.cover_image = data.cover_image || '';
        form.status = data.status || 'draft';
        form.published_at = data.published_at ? data.published_at.substring(0, 16) : '';
    } finally {
        loadingPost.value = false;
    }
}

async function submitForm() {
    if (submitting.value || loadingPost.value) {
        return;
    }
    submitting.value = true;
    try {
        const payload = {
            news_category_id: form.news_category_id || null,
            title: form.title,
            slug: form.slug || null,
            excerpt: form.excerpt || null,
            body: form.body,
            cover_image: form.cover_image || null,
            status: form.status,
            published_at: form.status === 'published' && form.published_at ? form.published_at : null,
        };

        if (isEditMode.value) {
            await axios.put(`/news/${form.id}`, payload);
        } else {
            await axios.post('/news', payload);
        }

        goBack();
    } finally {
        submitting.value = false;
    }
}

onMounted(async () => {
    await loadCategories();
    await loadPost();
});
</script>
