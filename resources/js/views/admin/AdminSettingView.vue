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
                        <textarea v-model="form['hero.subtitle']" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
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
                        <textarea v-model="form['welcome.message']" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Penutup Sambutan
                        <input v-model="form['welcome.author']" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Visi & Misi</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Visi
                        <textarea v-model="form['vision.body']" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Daftar Misi (pisahkan baris)
                        <textarea v-model="missionText" rows="5" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
                    </label>
                </div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Kontak Desa</h2>
                <div class="grid gap-4 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Alamat Kantor
                        <textarea v-model="form['contact.address']" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
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

const fields = [
    'site.name',
    'site.tagline',
    'site.primary_color',
    'site.secondary_color',
    'hero.title',
    'hero.subtitle',
    'hero.cta_text',
    'hero.cta_link',
    'welcome.message',
    'welcome.author',
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

const missionItems = computed(() => missionText.value.split('\n').map((line) => line.trim()).filter(Boolean));

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
    const updates = fields.map((key) => {
        const isJson = key === 'mission.items';
        const value = isJson ? missionItems.value : form[key];
        return axios.post('/settings', {
            key,
            value,
            type: isJson ? 'json' : 'text',
        });
    });

    await Promise.all(updates);
    await loadSettings();
    alert('Pengaturan tersimpan.');
}

onMounted(loadSettings);
</script>
