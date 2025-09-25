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
                <article
                    v-for="item in statistics"
                    :key="item._key"
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
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
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Ikon
                            <input v-model="item.icon" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
                        <label class="text-xs font-semibold text-slate-500 space-y-1">
                            Urutan
                            <input v-model.number="item.order" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                        </label>
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
    const payload = {
        label: item.label,
        value: Number(item.value) || 0,
        icon: item.icon || null,
        order: Number(item.order) || 0,
    };

    if (item.id) {
        const { data } = await axios.put(`/statistics/${item.id}`, payload);
        Object.assign(item, decorate(data));
    } else {
        const { data } = await axios.post('/statistics', payload);
        Object.assign(item, decorate(data));
    }
}

async function removeItem(item) {
    if (item.id) {
        if (!confirm(`Hapus statistik "${item.label || 'statistik'}"?`)) {
            return;
        }
        await axios.delete(`/statistics/${item.id}`);
    }
    const index = statistics.findIndex((stat) => stat._key === item._key);
    if (index !== -1) {
        statistics.splice(index, 1);
    }
}

onMounted(loadStats);
</script>
