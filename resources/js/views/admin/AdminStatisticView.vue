<template>
    <div class="p-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Statistik Desa</h1>
                <p class="text-sm text-slate-500">Kelola indikator statistik yang ditampilkan di beranda.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white" @click="addRow">
                Tambah Statistik
            </button>
        </header>

        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div v-if="loading" class="grid place-items-center py-16">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>
            <div v-else class="space-y-4">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span title="Seret kartu untuk mengubah urutan">Seret kartu untuk mengubah urutan</span>
                </div>
                <article
                    v-for="(item, idx) in statistics"
                    :key="item._key"
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4 transition"
                    draggable="true"
                    @dragstart="onDragStart(idx)"
                    @dragenter.prevent="onDragEnter(idx)"
                    @dragover.prevent
                    @drop.prevent="onDrop(idx)"
                    @dragend="onDragEnd"
                    :class="idx === dragOverIndex ? 'ring-2 ring-primary/40' : ''"
                >
                    <div class="grid gap-4 md:grid-cols-4">
                        <label class="text-xs font-semibold text-slate-500 space-y-1 md:col-span-2">
                            Label
                            <input v-model="item.label" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Nilai
                            <input v-model.number="item.value" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <div class="text-xs font-semibold text-slate-500 space-y-1">
                            <div class="flex items-center justify-between">
                                <span>Ikon</span>
                                <span v-if="item.icon" class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                    <component :is="Icons[item.icon]" class="h-4 w-4" />
                                </span>
                            </div>
                            <IconPicker v-model="item.icon" />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between text-xs font-semibold">
                        <span class="text-slate-400">{{ item.id ? 'Statistik tersimpan' : 'Statistik baru' }}</span>
                        <div class="space-x-3">
                            <button class="text-primary" @click="saveItem(item)">Simpan</button>
                            <button class="text-rose-500" @click="removeItem(item)">Hapus</button>
                        </div>
                    </div>
                </article>
                <p v-if="!statistics.length" class="text-center text-xs text-slate-400">Belum ada data statistik.</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import IconPicker from '../../components/IconPicker.vue';
import * as Icons from '@heroicons/vue/24/outline';
import notify from '../../utils/notify';

const loading = ref(false);
const statistics = reactive([]);

function decorate(item = {}) {
    return {
        _key: `${item.id ?? 'new'}-${Math.random().toString(36).slice(2, 9)}`,
        id: item.id ?? null,
        label: item.label ?? '',
        value: Number(item.value ?? 0),
        icon: item.icon ?? '',
        order: Number(item.order ?? 0),
    };
}

async function loadStats() {
    loading.value = true;
    try {
        const { data } = await axios.get('/statistics');
        statistics.splice(0, statistics.length, ...data.map(decorate));
    } finally {
        loading.value = false;
    }
}

function addRow() {
    statistics.unshift(decorate());
}

async function saveItem(item) {
    try {
        const payload = {
            label: item.label,
            value: Number(item.value) || 0,
            icon: item.icon || null,
        };
        if (item.id) {
            const { data } = await axios.put(`/statistics/${item.id}`, payload);
            Object.assign(item, decorate(data));
            notify.success('Statistik diperbarui');
        } else {
            const { data } = await axios.post('/statistics', payload);
            Object.assign(item, decorate(data));
            notify.success('Statistik ditambahkan');
        }
    } catch (e) {
        console.error(e);
        notify.error('Gagal menyimpan statistik');
    }
}

async function removeItem(item) {
    if (item.id) {
        if (!confirm(`Hapus statistik "${item.label || 'statistik'}"?`)) {
            return;
        }
        try {
            await axios.delete(`/statistics/${item.id}`);
            notify.success('Statistik dihapus');
        } catch (e) {
            console.error(e);
            notify.error('Gagal menghapus statistik');
            return;
        }
    }
    const index = statistics.findIndex((stat) => stat._key === item._key);
    if (index !== -1) {
        statistics.splice(index, 1);
    }
}

onMounted(loadStats);

const dragIndex = ref(null);
const dragOverIndex = ref(null);

function onDragStart(index) { dragIndex.value = index; }
function onDragEnter(index) { if (dragIndex.value === null) return; dragOverIndex.value = index; }
async function onDrop(index) {
    if (dragIndex.value === null) return;
    const from = dragIndex.value;
    const to = index;
    if (from !== to) {
        const [moved] = statistics.splice(from, 1);
        statistics.splice(to, 0, moved);
        await persistOrder();
    }
    dragIndex.value = null;
    dragOverIndex.value = null;
}
function onDragEnd() { dragIndex.value = null; dragOverIndex.value = null; }

async function persistOrder() {
    try {
        const updates = statistics
            .filter((it) => it.id)
            .map((it, i) => axios.put(`/statistics/${it.id}`, { order: i }));
        if (updates.length) {
            await Promise.all(updates);
            notify.success('Urutan statistik tersimpan');
        }
    } catch (e) {
        console.error('Gagal menyimpan urutan statistik:', e);
        notify.error('Gagal menyimpan urutan');
    }
}
</script>

<style scoped>
article[draggable="true"] { cursor: move; }
</style>
