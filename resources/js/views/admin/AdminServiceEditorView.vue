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
                <p class="text-sm text-slate-500">Isi detail layanan desa agar masyarakat memahami prosedur dan tautan yang disediakan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-primary hover:text-primary"
                    @click="resetForm"
                >
                    Reset Form
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-70"
                    @click="submitForm"
                    :disabled="submitting || loadingService"
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
                <div v-if="loadingService" class="col-span-full grid place-items-center py-20">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
                </div>
                <template v-else>
                    <div class="space-y-6">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Informasi Layanan</h2>
                            <div class="mt-5 space-y-4 text-sm">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Nama layanan</label>
                                    <input v-model="form.title" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required />
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500">Subjudul / tagline</label>
                                        <input v-model="form.subtitle" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Opsional" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-slate-500">Ikon (Heroicons)</label>
                                        <input v-model="form.icon" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Misal: DocumentTextIcon" />
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Deskripsi layanan</label>
                                    <jodit-editor
                                        :jsUrl="joditJsUrl"
                                        :cssUrl="joditCssUrl"
                                        v-model="form.description"
                                        :config="editorConfig"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Tautan layanan</label>
                                    <input v-model="form.link" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="https:// atau mailto:" />
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="space-y-6">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-sm">
                            <h2 class="text-lg font-semibold text-slate-900">Pengaturan Tampilan</h2>
                            <div class="mt-5 space-y-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Urutan tampil</label>
                                    <input v-model.number="form.order" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                    <p class="text-[11px] text-slate-400">Angka lebih kecil tampil lebih dahulu.</p>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-500">Status tautan</label>
                                    <p class="rounded-lg border border-dashed border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-500">
                                        {{ form.link ? 'Tautan akan ditampilkan dengan tombol akses cepat.' : 'Jika kosong, layanan hanya menampilkan deskripsi.' }}
                                    </p>
                                </div>
                            </div>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-xs text-slate-500">
                            <h2 class="text-sm font-semibold text-slate-900">Tips Layanan</h2>
                            <ul class="mt-3 space-y-2 list-disc pl-4">
                                <li>Gunakan nama layanan yang singkat dan mudah dipahami warga.</li>
                                <li>Jika layanan tersedia daring, sertakan tautan resmi.</li>
                                <li>Isi deskripsi dengan alur ringkas atau syarat dokumen.</li>
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
import { JODIT_JS_CDN, JODIT_CSS_CDN, createJoditConfig } from '../../utils/jodit';

const route = useRoute();
const router = useRouter();

const joditJsUrl = JODIT_JS_CDN;
const joditCssUrl = JODIT_CSS_CDN;
const editorConfig = createJoditConfig({ minHeight: 320 });

const submitting = ref(false);
const loadingService = ref(false);

const form = reactive({
    id: null,
    title: '',
    subtitle: '',
    description: '',
    icon: '',
    link: '',
    order: 0,
});

const isEditMode = computed(() => Boolean(route.params.id));
const pageTitle = computed(() => (isEditMode.value ? 'Perbarui Layanan' : 'Tambah Layanan Baru'));
const submitLabel = computed(() => (isEditMode.value ? 'Perbarui Layanan' : 'Simpan Layanan'));

function goBack() {
    router.push({ name: 'admin.services' });
}

function setDefaults() {
    form.id = null;
    form.title = '';
    form.subtitle = '';
    form.description = '';
    form.icon = '';
    form.link = '';
    form.order = 0;
}

function resetForm() {
    if (isEditMode.value) {
        loadService();
    } else {
        setDefaults();
    }
}

async function loadService() {
    if (!isEditMode.value) {
        setDefaults();
        return;
    }
    loadingService.value = true;
    try {
        const { data } = await axios.get(`/services/${route.params.id}`);
        form.id = data.id;
        form.title = data.title || '';
        form.subtitle = data.subtitle || '';
        form.description = data.description || '';
        form.icon = data.icon || '';
        form.link = data.link || '';
        form.order = Number(data.order ?? 0);
    } finally {
        loadingService.value = false;
    }
}

async function submitForm() {
    if (submitting.value || loadingService.value) {
        return;
    }
    submitting.value = true;
    try {
        const payload = {
            title: form.title,
            subtitle: form.subtitle || null,
            description: form.description || null,
            icon: form.icon || null,
            link: form.link || null,
            order: Number(form.order) || 0,
        };

        if (isEditMode.value) {
            await axios.put(`/services/${form.id}`, payload);
        } else {
            await axios.post('/services', payload);
        }

        goBack();
    } finally {
        submitting.value = false;
    }
}

onMounted(async () => {
    await loadService();
});
</script>

<style scoped>
</style>
