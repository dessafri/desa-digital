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
                <p class="text-xs text-slate-400">Seret kartu untuk mengubah urutan</p>
                <article
                    v-for="(official, idx) in officials"
                    :key="official._key"
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4 transition"
                    draggable="true"
                    @dragstart="onDragStart(idx)"
                    @dragenter.prevent="onDragEnter(idx)"
                    @dragover.prevent
                    @drop.prevent="onDrop(idx)"
                    @dragend="onDragEnd"
                    :class="idx === dragOverIndex ? 'ring-2 ring-primary/40' : ''"
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
                        <div class="space-y-2">
                            <span>Foto</span>
                            <div class="flex flex-col items-start gap-2">
                                <div class="h-[100px] w-[100px] min-w-[100px] overflow-hidden rounded-md border bg-white shrink-0">
                                    <img v-if="official.photo" :src="official.photo" alt="foto" class="h-full w-full object-cover cursor-zoom-in" @click="openPreview(official.photo)" />
                                </div>
                                <input type="file" accept="image/*" @change="(e) => uploadPhoto(official, e)" class="text-xs" />
                            </div>
                        </div>
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

        <div v-if="previewUrl" class="fixed inset-0 z-50 grid place-items-center bg-black/70 p-4" @click.self="closePreview">
            <div class="max-h-[90vh] max-w-5xl overflow-auto rounded-xl bg-white p-3">
                <img :src="previewUrl" alt="Preview" class="h-auto w-full" />
                <div class="mt-3 text-right">
                    <button class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white" @click="closePreview">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import notify from '../../utils/notify';

const officials = reactive([]);
const loading = ref(false);
const dragIndex = ref(null);
const dragOverIndex = ref(null);
const previewUrl = ref('');

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
    try {
        const payload = {
            name: official.name,
            position: official.position,
            photo: official.photo || null,
            phone: official.phone || null,
            email: official.email || null,
        };

        if (official.id) {
            const { data } = await axios.put(`/village-officials/${official.id}`, payload);
            Object.assign(official, decorate(data));
            notify.success('Perangkat Desa diperbarui');
        } else {
            const { data } = await axios.post('/village-officials', payload);
            Object.assign(official, decorate(data));
            notify.success('Perangkat Desa ditambahkan');
        }
    } catch (e) {
        console.error(e);
        notify.error('Gagal menyimpan perangkat');
    }
}

async function removeOfficial(official) {
    if (official.id) {
        if (!confirm(`Hapus ${official.name || 'perangkat desa'}?`)) {
            return;
        }
        try {
            await axios.delete(`/village-officials/${official.id}`);
            notify.success('Perangkat Desa dihapus');
        } catch (e) {
            console.error(e);
            notify.error('Gagal menghapus perangkat');
            return;
        }
    }
    const index = officials.findIndex((item) => item._key === official._key);
    if (index !== -1) {
        officials.splice(index, 1);
    }
}

onMounted(loadOfficials);

async function uploadPhoto(official, event) {
    const file = event.target.files?.[0];
    if (!file) return;
    const form = new FormData();
    form.append('file', file);
    try {
        const { data } = await axios.post('/village-officials/upload-photo', form, { headers: { 'Content-Type': 'multipart/form-data' } });
        if (data?.url) {
            official.photo = data.url;
            notify.success('Foto terunggah');
        }
    } catch (e) {
        console.error(e);
        notify.error('Upload foto gagal');
    } finally {
        event.target.value = '';
    }
}

function onDragStart(index) {
    dragIndex.value = index;
}

function onDragEnter(index) {
    if (dragIndex.value === null) return;
    dragOverIndex.value = index;
}

async function onDrop(index) {
    if (dragIndex.value === null) return;
    const from = dragIndex.value;
    const to = index;
    if (from !== to) {
        const [moved] = officials.splice(from, 1);
        officials.splice(to, 0, moved);
        // Reorder and persist
        await persistOrder();
    }
    dragIndex.value = null;
    dragOverIndex.value = null;
}

function onDragEnd() {
    dragIndex.value = null;
    dragOverIndex.value = null;
}

async function persistOrder() {
    try {
        const updates = officials
            .filter((it) => it.id)
            .map((it, i) => axios.put(`/village-officials/${it.id}`, { order: i }));
        if (updates.length) {
            await Promise.all(updates);
            notify.success('Urutan tersimpan');
        }
    } catch (e) {
        console.error('Gagal menyimpan urutan perangkat desa:', e);
        notify.error('Gagal menyimpan urutan');
    }
}

function openPreview(url) {
    previewUrl.value = url;
}

function closePreview() {
    previewUrl.value = '';
}
</script>

<style scoped>
article[draggable="true"] { cursor: move; }
</style>
