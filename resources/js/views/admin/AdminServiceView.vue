<template>
    <div class="p-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Layanan Digital Desa</h1>
                <p class="text-sm text-slate-500">Atur daftar layanan publik yang tampil pada portal.</p>
            </div>
            <button
                class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white"
                @click="addRow"
            >
                Tambah Layanan
            </button>
        </header>

        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div v-if="loading" class="grid place-items-center py-16">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
            <div v-else class="space-y-4">
                <article
                    v-for="service in services"
                    :key="service._key"
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Judul
                            <input v-model="service.title" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Subjudul
                            <input v-model="service.subtitle" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Deskripsi
                            <textarea v-model="service.description" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Ikon / Penanda
                            <input v-model="service.icon" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Misal: DocumentTextIcon" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Tautan
                            <input v-model="service.link" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Urutan
                            <input v-model.number="service.order" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                    </div>
                    <div class="mt-4 flex justify-between text-xs font-semibold">
                        <span class="text-slate-400">{{ service.id ? 'Layanan tersimpan' : 'Layanan baru' }}</span>
                        <div class="space-x-3">
                            <button class="text-primary" @click="saveService(service)">Simpan</button>
                            <button class="text-rose-500" @click="removeService(service)">Hapus</button>
                        </div>
                    </div>
                </article>
                <p v-if="!services.length" class="text-center text-xs text-slate-400">Belum ada layanan terdaftar.</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const loading = ref(false);
const services = reactive([]);

function decorate(item = {}) {
    return {
        _key: `${item.id ?? 'new'}-${Math.random().toString(36).slice(2, 9)}`,
        id: item.id ?? null,
        title: item.title ?? '',
        subtitle: item.subtitle ?? '',
        description: item.description ?? '',
        icon: item.icon ?? '',
        link: item.link ?? '',
        order: item.order ?? 0,
    };
}

async function loadServices() {
    loading.value = true;
    try {
        const { data } = await axios.get('/services');
        services.splice(0, services.length, ...data.map(decorate));
    } finally {
        loading.value = false;
    }
}

function addRow() {
    services.unshift(decorate());
}

async function saveService(service) {
    const payload = {
        title: service.title,
        subtitle: service.subtitle || null,
        description: service.description || null,
        icon: service.icon || null,
        link: service.link || null,
        order: Number(service.order) || 0,
    };

    if (service.id) {
        const { data } = await axios.put(`/services/${service.id}`, payload);
        Object.assign(service, decorate(data));
    } else {
        const { data } = await axios.post('/services', payload);
        Object.assign(service, decorate(data));
    }
}

async function removeService(service) {
    if (service.id) {
        if (!confirm(`Hapus layanan "${service.title || 'tanpa judul'}"?`)) {
            return;
        }
        await axios.delete(`/services/${service.id}`);
    }
    const idx = services.findIndex((item) => item._key === service._key);
    if (idx !== -1) {
        services.splice(idx, 1);
    }
}

onMounted(loadServices);
</script>
