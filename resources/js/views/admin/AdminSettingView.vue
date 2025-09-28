<template>
    <div class="p-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Pengaturan Situs</h1>
                <p class="text-sm text-slate-500">Atur identitas visual dan konten statis portal desa.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white" @click="saveAll">
                Simpan Semua
            </button>
        </header>

        <section class="mt-8 grid gap-6 lg:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Identitas Desa</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Nama Situs
                        <input v-model="form['site.name']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Tagline
                        <input v-model="form['site.tagline']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Warna Primer
                        <input v-model="form['site.primary_color']" type="color" class="h-10 w-20 rounded border border-slate-200" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Warna Sekunder
                        <input v-model="form['site.secondary_color']" type="color" class="h-10 w-20 rounded border border-slate-200" />
                    </label>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Hero & Sambutan</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Judul Hero
                        <input v-model="form['hero.title']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Subjudul Hero
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="form['hero.subtitle']"
                            :config="smallEditorConfig"
                        />
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Teks Tombol
                            <input v-model="form['hero.cta_text']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Link Tombol
                            <input v-model="form['hero.cta_link']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                    </div>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Sambutan Kepala Desa
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="form['welcome.message']"
                            :config="mediumEditorConfig"
                        />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Penutup Sambutan
                        <input v-model="form['welcome.author']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <div class="grid gap-2 text-xs font-semibold text-slate-500">
                        <span>Foto Kepala Desa</span>
                        <div class="flex items-center gap-3">
                            <div class="h-16 w-16 overflow-hidden rounded-full border bg-slate-100">
                                <img v-if="form['welcome.photo']" :src="form['welcome.photo']" alt="foto" class="h-full w-full object-cover" />
                            </div>
                            <input v-model="form['welcome.photo']" type="text" placeholder="https://" class="flex-1 rounded-lg border border-slate-200 px-3 py-2" />
                            <label class="cursor-pointer rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-primary/40 hover:text-primary">
                                Upload Foto
                                <input type="file" accept="image/*" class="hidden" @change="uploadWelcomePhoto" />
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 border-t pt-4">
                    <h3 class="text-sm font-semibold text-slate-900">Gambar Jumbotron (Carousel)</h3>
                    <p class="text-xs text-slate-500">Tambah beberapa gambar untuk ditampilkan sebagai slide di beranda. Seret untuk mengubah urutan.</p>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <div
                            v-for="(url, idx) in heroImages"
                            :key="url + '-' + idx"
                            class="relative h-24 w-40 overflow-hidden rounded-lg border bg-slate-100"
                            draggable="true"
                            @dragstart="onHeroDragStart(idx)"
                            @dragenter.prevent="onHeroDragEnter(idx)"
                            @dragover.prevent
                            @drop.prevent="onHeroDrop(idx)"
                            @dragend="onHeroDragEnd"
                        >
                            <img :src="url" class="h-full w-full object-cover" />
                            <button type="button" class="absolute right-1 top-1 rounded bg-white/90 px-2 py-1 text-[10px] font-semibold text-rose-600 shadow" @click="removeHeroImage(idx)">Hapus</button>
                        </div>
                        <label class="grid h-24 w-40 cursor-pointer place-items-center rounded-lg border-2 border-dashed border-slate-300 text-xs text-slate-500 hover:border-primary/60 hover:text-primary">
                            <span>Tambah Gambar</span>
                            <input type="file" accept="image/*" multiple class="hidden" @change="onHeroFilesSelected" />
                        </label>
                    </div>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Visi & Misi</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Visi
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="form['vision.body']"
                            :config="mediumEditorConfig"
                        />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Daftar Misi (pisahkan baris)
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="missionText"
                            :config="listEditorConfig"
                        />
                    </label>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Kontak Desa</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Alamat Kantor
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="form['contact.address']"
                            :config="smallEditorConfig"
                        />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Email Resmi
                        <input v-model="form['contact.email']" type="email" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Nomor Telepon
                        <input v-model="form['contact.phone']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Facebook
                        <input v-model="form['social.facebook']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Instagram
                        <input v-model="form['social.instagram']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                </div>
            </article>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import notify from '../../utils/notify';
import { JODIT_JS_CDN, JODIT_CSS_CDN, createJoditConfig } from '../../utils/jodit';

const joditJsUrl = JODIT_JS_CDN;
const joditCssUrl = JODIT_CSS_CDN;
const smallEditorConfig = createJoditConfig({ minHeight: 180 });
const mediumEditorConfig = createJoditConfig({ minHeight: 240 });
const listEditorConfig = createJoditConfig({
    minHeight: 220,
});

const fields = [
    'site.name',
    'site.tagline',
    'site.primary_color',
    'site.secondary_color',
    'hero.title',
    'hero.subtitle',
    'hero.cta_text',
    'hero.cta_link',
    'hero.images',
    'welcome.message',
    'welcome.author',
    'welcome.photo',
    'vision.body',
    'mission.items',
    'contact.address',
    'contact.email',
    'contact.phone',
    'social.facebook',
    'social.instagram',
];

const form = reactive(Object.fromEntries(fields.map((key) => [key, ''])));
const settingsMap = reactive({});
const missionText = ref('');
const heroImages = ref([]);
const heroDragIndex = ref(null);
const heroDragOverIndex = ref(null);

function htmlToPlainLines(html) {
    if (!html) return [];
    const normalized = String(html)
        .replace(/<\s*br\s*\/?\s*>/gi, '\n')
        .replace(/<\s*\/p\s*>/gi, '\n')
        .replace(/<\s*li\s*>/gi, '')
        .replace(/<\s*\/li\s*>/gi, '\n')
        .replace(/<\s*\/div\s*>/gi, '\n')
        .replace(/<\s*\/h[1-6]\s*>/gi, '\n');
    const text = normalized.replace(/<[^>]*>/g, '');
    return text
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);
}

const missionItems = computed(() => htmlToPlainLines(missionText.value));

async function loadSettings() {
    const { data } = await axios.get('/settings', {
        params: {
            keys: fields.join(','),
        },
    });

    data.forEach((setting) => {
        settingsMap[setting.key] = setting;
        if (setting.type === 'json') {
            const parsed = safeParse(setting.value);
            if (Array.isArray(parsed) && setting.key === 'mission.items') {
                missionText.value = parsed.join('\n');
            }
            if (Array.isArray(parsed) && setting.key === 'hero.images') {
                heroImages.value = parsed;
            }
        } else {
            form[setting.key] = setting.value ?? '';
        }
    });

    if (!missionText.value) {
        missionText.value = (settingsMap['mission.items']?.value ?? '')
            .split('\n')
            .filter(Boolean)
            .join('\n');
    }

    if (!form['site.primary_color']) {
        form['site.primary_color'] = '#0EA5E9';
    }
    if (!form['site.secondary_color']) {
        form['site.secondary_color'] = '#10B981';
    }
}

function safeParse(value) {
    try {
        return JSON.parse(value);
    } catch (error) {
        return value;
    }
}

async function saveAll() {
    try {
        const jsonKeys = new Set(['mission.items', 'hero.images']);
        const updates = fields.map((key) => {
            const isJson = jsonKeys.has(key);
            let value = form[key];
            if (key === 'mission.items') value = missionItems.value;
            if (key === 'hero.images') value = heroImages.value;
            return axios.post('/settings', { key, value, type: isJson ? 'json' : 'text' });
        });
        await Promise.all(updates);
        await loadSettings();
        notify.success('Pengaturan tersimpan');
    } catch (e) {
        console.error(e);
        notify.error('Gagal menyimpan pengaturan');
    }
}

async function onHeroFilesSelected(event) {
    const files = Array.from(event.target.files || []);
    if (!files.length) return;
    try {
        const formData = new FormData();
        files.forEach((f) => formData.append('files[]', f));
        const { data } = await axios.post('/settings/upload-hero', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        const urls = Array.isArray(data) ? data : [];
        heroImages.value.push(...urls);
        notify.success('Gambar ditambahkan');
    } catch (e) {
        console.error(e);
        notify.error('Upload gambar gagal');
    } finally {
        event.target.value = '';
    }
}

function removeHeroImage(index) {
    heroImages.value.splice(index, 1);
}

function onHeroDragStart(index) { heroDragIndex.value = index; }
function onHeroDragEnter(index) { if (heroDragIndex.value === null) return; heroDragOverIndex.value = index; }
function onHeroDrop(index) {
    if (heroDragIndex.value === null) return;
    const from = heroDragIndex.value; const to = index;
    if (from !== to) {
        const [moved] = heroImages.value.splice(from, 1);
        heroImages.value.splice(to, 0, moved);
    }
    heroDragIndex.value = null; heroDragOverIndex.value = null;
}
function onHeroDragEnd() { heroDragIndex.value = null; heroDragOverIndex.value = null; }

onMounted(loadSettings);

async function uploadWelcomePhoto(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    try {
        const formData = new FormData();
        formData.append('file', file);
        const { data } = await axios.post('/settings/upload-image', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        if (data?.url) {
            form['welcome.photo'] = data.url;
        }
    } finally {
        event.target.value = '';
    }
}
</script>
