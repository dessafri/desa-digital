<template>
    <div class="p-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Perangkat Desa</h1>
                <p class="text-sm text-slate-500">Kelola daftar perangkat desa yang tampil pada halaman profil.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white" @click="addOfficial">
                Tambah Perangkat
            </button>
        </header>

        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div v-if="loading" class="grid place-items-center py-16">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
            <div v-else class="space-y-4">
                <article
                    v-for="official in officials"
                    :key="official._key"
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >
                    <div class="grid gap-4 md:grid-cols-3 text-xs font-semibold text-slate-500">
                        <label class="space-y-1">
                            Nama
                            <input v-model="official.name" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                        <label class="space-y-1">
                            Jabatan
                            <input v-model="official.position" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                        <label class="space-y-1">
                            Urutan
                            <input v-model.number="official.order" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                        <label class="space-y-1 md:col-span-2">
                            Foto (URL)
                            <input v-model="official.photo" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                        <label class="space-y-1">
                            Telepon
                            <input v-model="official.phone" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                        <label class="space-y-1 md:col-span-2">
                            Email
                            <input v-model="official.email" type="email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                        </label>
                    </div>
                    <div class="mt-3 flex justify-between text-xs font-semibold">
                        <span class="text-slate-400">{{ official.id ? 'Data tersimpan' : 'Data baru' }}</span>
                        <div class="space-x-3">
                            <button class="text-primary" @click="saveOfficial(official)">Simpan</button>
                            <button class="text-rose-500" @click="removeOfficial(official)">Hapus</button>
                        </div>
                    </div>
                </article>
                <p v-if="!officials.length" class="text-center text-xs text-slate-400">Belum ada perangkat desa terdaftar.</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const officials = reactive([]);
const loading = ref(false);

function keyOf(item) {
    return `${item.id ?? 'new'}-${Math.random().toString(36).slice(2, 9)}`;
}

function decorate(item = {}) {
    return {
        _key: keyOf(item),
        id: item.id ?? null,
        name: item.name ?? '',
        position: item.position ?? '',
        photo: item.photo ?? '',
        phone: item.phone ?? '',
        email: item.email ?? '',
        order: Number(item.order ?? 0),
    };
}

async function loadOfficials() {
    loading.value = true;
    try {
        const { data } = await axios.get('/village-officials');
        officials.splice(0, officials.length, ...data.map(decorate));
    } finally {
        loading.value = false;
    }
}

function addOfficial() {
    officials.unshift(decorate());
}

async function saveOfficial(official) {
    const payload = {
        name: official.name,
        position: official.position,
        photo: official.photo || null,
        phone: official.phone || null,
        email: official.email || null,
        order: Number(official.order) || 0,
    };

    if (official.id) {
        const { data } = await axios.put(`/village-officials/${official.id}`, payload);
        Object.assign(official, decorate(data));
    } else {
        const { data } = await axios.post('/village-officials', payload);
        Object.assign(official, decorate(data));
    }
}

async function removeOfficial(official) {
    if (official.id) {
        if (!confirm(`Hapus ${official.name || 'perangkat desa'}?`)) {
            return;
        }
        await axios.delete(`/village-officials/${official.id}`);
    }
    const index = officials.findIndex((item) => item._key === official._key);
    if (index !== -1) {
        officials.splice(index, 1);
    }
}

onMounted(loadOfficials);
</script>
