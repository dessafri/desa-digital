<template>
    <div class="space-y-8 p-8">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1.5">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-primary/70">Pelayanan Desa</p>
                <h1 class="text-3xl font-semibold text-slate-900">Layanan Desa</h1>
                <p class="text-sm text-slate-500">Kelola katalog layanan yang tersedia bagi warga melalui portal desa.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-primary/90"
                    @click="goToCreate"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                    </svg>
                    Tambah Layanan
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-primary hover:text-primary"
                    @click="loadServices"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 4.5v6h6M19.5 19.5v-6h-6M5.876 18.124A8.25 8.25 0 0 1 18.124 5.876" />
                    </svg>
                    Muat ulang
                </button>
            </div>
        </header>

        <section class="space-y-6 rounded-3xl border border-slate-200 bg-white/95 p-6 shadow-sm backdrop-blur">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Layanan</h2>
                    <p class="text-xs text-slate-500">Ringkasan layanan aktif yang tampil untuk masyarakat.</p>
                </div>
            </div>

            <div v-if="loading" class="grid place-items-center py-20">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary/30 border-t-primary"></div>
            </div>

            <template v-else>
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
                    <span v-if="orderSaving" class="text-primary/70">Menyimpan urutan...</span>
                    <span v-else-if="orderMessage" class="text-emerald-600">{{ orderMessage }}</span>
                    <span v-else>&nbsp;</span>
                </div>
                <div v-if="displayServices.length" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="(service, index) in displayServices"
                        :key="service.id || service.title || index"
                        class="flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition"
                        :class="[
                            orderSaving ? 'cursor-not-allowed opacity-60' : 'cursor-grab hover:-translate-y-0.5 hover:shadow-lg',
                            draggingIndex === index ? 'ring-2 ring-primary/40 border-primary/50' : '',
                            dragOverIndex === index && draggingIndex !== null && draggingIndex !== index ? 'border-primary/30' : '',
                        ]"
                        :draggable="!orderSaving"
                        @dragstart="onDragStart(index, $event)"
                        @dragover.prevent="onDragOver($event, index)"
                        @dragenter.prevent="onDragOver($event, index)"
                        @dragleave="onDragLeave(index)"
                        @drop.prevent="onDrop($event, index)"
                        @dragend="onDragEnd"
                        title="Geser Untuk Mengurutkan"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex h-7 items-center rounded-full bg-primary/10 px-3 text-xs font-semibold text-primary">
                                    Urutan: {{ service.order ?? 0 }}
                                </span>
                                <span class="flex items-center gap-1 rounded-full border border-dashed border-primary/40 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.3em] text-primary/70">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8h16M4 12h16M4 16h16" />
                                    </svg>
                                    Geser
                                </span>
                            </div>
                            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400">{{ formatDate(service.updated_at) }}</p>
                            <h3 class="text-lg font-semibold text-slate-900 line-clamp-2">{{ service.title }}</h3>
                            <p v-if="service.subtitle" class="text-sm font-semibold text-primary/70">{{ service.subtitle }}</p>
                            <p class="text-sm text-slate-500 line-clamp-3">{{ service.description || 'Belum ada deskripsi rinci layanan.' }}</p>
                        </div>
                        <div class="mt-5 flex items-center justify-between text-xs font-semibold">
                            <p class="text-slate-400">{{ service.link ? 'Tautan layanan tersedia' : 'Tidak ada tautan' }}</p>
                            <div class="flex items-center gap-4">
                                <button type="button" class="text-primary hover:text-primary/80" @click="goToEdit(service.id)">Ubah</button>
                                <button type="button" class="text-rose-500 hover:text-rose-400" @click="deleteService(service)">Hapus</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 py-16 text-center">
                    <p class="text-sm font-semibold text-slate-600">Belum ada layanan desa yang terdaftar.</p>
                    <p class="mt-2 text-xs text-slate-400">Gunakan tombol "Tambah Layanan" untuk membuat data baru.</p>
                </div>
            </template>
        </section>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const orderSaving = ref(false);
const orderMessage = ref('');
const services = ref([]);
const draggingIndex = ref(null);
const dragOverIndex = ref(null);
let orderMessageTimer;

onBeforeUnmount(() => {
    if (orderMessageTimer) {
        clearTimeout(orderMessageTimer);
    }
});

const displayServices = computed(() => services.value);

function setOrderMessage(message) {
    if (orderMessageTimer) {
        clearTimeout(orderMessageTimer);
    }
    orderMessage.value = message;
    if (message) {
        orderMessageTimer = setTimeout(() => {
            orderMessage.value = '';
        }, 3000);
    }
}

function formatDate(value) {
    if (!value) {
        return 'Baru';
    }
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value));
}

function normalizeServices(list) {
    const sorted = [...list].sort((a, b) => {
        const orderA = Number(a.order ?? Number.MAX_SAFE_INTEGER);
        const orderB = Number(b.order ?? Number.MAX_SAFE_INTEGER);
        if (orderA !== orderB) return orderA - orderB;
        const dateA = a.updated_at ? new Date(a.updated_at).getTime() : 0;
        const dateB = b.updated_at ? new Date(b.updated_at).getTime() : 0;
        return dateB - dateA;
    });
    let changed = false;
    const normalized = sorted.map((item, idx) => {
        const newOrder = idx + 1;
        if (Number(item.order ?? newOrder) !== newOrder) {
            changed = true;
        }
        return { ...item, order: newOrder };
    });
    return { normalized, changed };
}

async function loadServices() {
    loading.value = true;
    try {
        const { data } = await axios.get('/services');
        const { normalized, changed } = normalizeServices(data ?? []);
        services.value = normalized;
        if (changed) {
            await persistOrder(normalized, { silent: true });
        }
    } finally {
        loading.value = false;
    }
}

function assignOrders(list) {
    list.forEach((item, idx) => {
        item.order = idx + 1;
    });
}

async function reorderServices(from, to) {
    const list = [...services.value];
    if (from < 0 || to < 0 || from === to) {
        return;
    }
    const [moved] = list.splice(from, 1);
    list.splice(to, 0, moved);
    assignOrders(list);
    services.value = list;
    await persistOrder(list);
}

async function persistOrder(list, { silent = false } = {}) {
    const updates = list.filter((item) => item.id);
    if (!updates.length) {
        return;
    }
    orderSaving.value = true;
    try {
        await Promise.all(
            updates.map(async (item) => {
                const payload = {
                    title: item.title,
                    subtitle: item.subtitle || null,
                    description: item.description || null,
                    icon: item.icon || null,
                    link: item.link || null,
                    order: item.order,
                };
                const { data } = await axios.put(`/services/${item.id}`, payload);
                Object.assign(item, data);
            })
        );
        const { normalized } = normalizeServices(services.value);
        services.value = normalized;
        if (!silent) {
            setOrderMessage('Urutan layanan diperbarui.');
        }
    } catch (error) {
        console.error(error);
        setOrderMessage('Gagal memperbarui urutan.');
    } finally {
        orderSaving.value = false;
    }
}

function goToCreate() {
    router.push('/admin/layanan/tambah');
}

function goToEdit(id) {
    if (!id) return;
    router.push(`/admin/layanan/${id}/edit`);
}

async function deleteService(service) {
    if (!service?.id) {
        services.value = services.value.filter((item) => item !== service);
        assignOrders(services.value);
        setOrderMessage('Layanan dihapus dari daftar sementara.');
        return;
    }
    if (!confirm(`Hapus layanan "${service.title || 'tanpa judul'}"?`)) {
        return;
    }
    await axios.delete(`/services/${service.id}`);
    await loadServices();
    setOrderMessage('Layanan berhasil dihapus.');
}

function onDragStart(index, event) {
    if (orderSaving.value) return;
    draggingIndex.value = index;
    dragOverIndex.value = index;
    event.dataTransfer.effectAllowed = 'move';
}

function onDragOver(event, index) {
    if (orderSaving.value) return;
    event.preventDefault();
    dragOverIndex.value = index;
}

function onDragLeave(index) {
    if (dragOverIndex.value === index) {
        dragOverIndex.value = null;
    }
}

async function onDrop(event, index) {
    event.preventDefault();
    if (orderSaving.value) {
        onDragEnd();
        return;
    }
    const from = draggingIndex.value;
    const to = index;
    onDragEnd();
    if (from === null || to === null || from === to) {
        return;
    }
    await reorderServices(from, to);
}

function onDragEnd() {
    draggingIndex.value = null;
    dragOverIndex.value = null;
}

onMounted(loadServices);
</script>
