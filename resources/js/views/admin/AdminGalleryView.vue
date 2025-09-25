<template>
    <div class="p-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Galeri Desa</h1>
                <p class="text-sm text-slate-500">Kelola album galeri dan foto dokumentasi kegiatan.</p>
            </div>
            <div class="flex gap-3 text-sm font-semibold">
                <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-slate-600" @click="loadAlbums">
                    Muat Ulang
                </button>
                <button class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-white" @click="createAlbum">
                    Album Baru
                </button>
            </div>
        </header>

        <section class="mt-8 grid gap-6 lg:grid-cols-[340px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700">Album</h2>
                <ul class="mt-4 space-y-2 text-sm">
                    <li
                        v-for="album in albums"
                        :key="album._key"
                        class="cursor-pointer rounded-lg border px-3 py-2 transition"
                        :class="album._key === activeKey ? 'border-primary bg-primary/10 text-primary' : 'border-slate-200 text-slate-600 hover:border-primary/40'"
                        @click="selectAlbum(album)"
                    >
                        <p class="font-semibold">{{ album.title || 'Album Baru' }}</p>
                        <p class="text-xs">{{ album.items?.length ?? 0 }} foto</p>
                    </li>
                </ul>
                <p v-if="!albums.length" class="text-xs text-slate-400">Belum ada album. Tambahkan album baru untuk memulai.</p>
            </aside>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" v-if="selected">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Detail Album</h2>
                    <div class="flex gap-2 text-xs font-semibold">
                        <button class="rounded-full border border-slate-200 px-4 py-1 text-slate-600" @click="saveAlbum(selected)">
                            Simpan Album
                        </button>
                        <button class="rounded-full border border-rose-200 px-4 py-1 text-rose-500" @click="removeAlbum(selected)">
                            Hapus Album
                        </button>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 text-sm">
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Judul Album
                        <input v-model="selected.title" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                    <label class="text-xs font-semibold text-slate-500 space-y-1">
                        Slug (opsional)
                        <input v-model="selected.slug" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="otomatis dari judul" />
                    </label>
                    <label class="md:col-span-2 text-xs font-semibold text-slate-500 space-y-1">
                        Deskripsi
                        <textarea v-model="selected.description" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2"></textarea>
                    </label>
                    <label class="md:col-span-2 text-xs font-semibold text-slate-500 space-y-1">
                        Gambar Sampul (URL)
                        <input v-model="selected.cover_image" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2" />
                    </label>
                </div>

                <div class="mt-10 flex items-center justify-between">
                    <h3 class="text-md font-semibold text-slate-900">Foto dalam Album</h3>
                    <button class="text-xs font-semibold text-primary" @click="addGalleryItem">Tambah Foto</button>
                </div>

                <div class="mt-4 space-y-4">
                    <article
                        v-for="item in galleryItems"
                        :key="item._key"
                        class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                    >
                        <div class="grid gap-4 md:grid-cols-4 text-xs font-semibold text-slate-500">
                            <label class="space-y-1 md:col-span-2">
                                Judul
                                <input v-model="item.title" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </label>
                            <label class="space-y-1 md:col-span-2">
                                Caption
                                <input v-model="item.caption" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </label>
                            <label class="space-y-1 md:col-span-3">
                                URL Gambar
                                <input v-model="item.image_url" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </label>
                            <label class="space-y-1">
                                Urutan
                                <input v-model.number="item.order" type="number" min="0" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                            </label>
                        </div>
                        <div class="mt-3 flex justify-between text-xs font-semibold">
                            <span class="text-slate-400">{{ item.id ? 'Foto tersimpan' : 'Foto baru' }}</span>
                            <div class="space-x-3">
                                <button class="text-primary" @click="saveGalleryItem(item)">Simpan</button>
                                <button class="text-rose-500" @click="removeGalleryItem(item)">Hapus</button>
                            </div>
                        </div>
                    </article>
                    <p v-if="!galleryItems.length" class="text-center text-xs text-slate-400">Belum ada foto pada album ini.</p>
                </div>
            </div>
            <div v-else class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
                Pilih album untuk melihat detail.
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const albums = reactive([]);
const selected = ref(null);
const galleryItems = reactive([]);

const activeKey = computed(() => selected.value?._key ?? null);

function uniqueKey(prefix) {
    return `${prefix}-${Math.random().toString(36).slice(2, 9)}`;
}

function decorateAlbum(album = {}, fallbackItems = []) {
    return {
        _key: uniqueKey(album.id ?? 'new'),
        id: album.id ?? null,
        title: album.title ?? '',
        slug: album.slug ?? '',
        description: album.description ?? '',
        cover_image: album.cover_image ?? '',
        items: album.items ?? fallbackItems ?? [],
    };
}

function decorateItem(item = {}) {
    return {
        _key: uniqueKey(item.id ?? 'new'),
        id: item.id ?? null,
        gallery_album_id: item.gallery_album_id ?? selected.value?.id ?? null,
        title: item.title ?? '',
        caption: item.caption ?? '',
        image_url: item.image_url ?? '',
        order: Number(item.order ?? 0),
    };
}

function plainItem(item) {
    return {
        id: item.id ?? null,
        gallery_album_id: selected.value?.id ?? item.gallery_album_id ?? null,
        title: item.title ?? '',
        caption: item.caption ?? '',
        image_url: item.image_url ?? '',
        order: Number(item.order ?? 0),
    };
}

function syncAlbumItems() {
    if (!selected.value) {
        return;
    }
    selected.value.items = galleryItems.map((item) => plainItem(item));
}

async function loadAlbums() {
    const { data } = await axios.get('/gallery-albums');
    albums.splice(0, albums.length, ...data.map((album) => decorateAlbum(album, album.items ?? [])));
    if (selected.value) {
        const current = albums.find((album) => album.id === selected.value.id);
        if (current) {
            selectAlbum(current);
        } else {
            selected.value = null;
            galleryItems.splice(0, galleryItems.length);
        }
    }
}

function createAlbum() {
    const album = decorateAlbum();
    albums.unshift(album);
    selectAlbum(album);
}

function selectAlbum(album) {
    selected.value = album;
    galleryItems.splice(0, galleryItems.length, ...(album.items ?? []).map(decorateItem));
}

async function saveAlbum(album) {
    const payload = {
        title: album.title,
        slug: album.slug || null,
        description: album.description || null,
        cover_image: album.cover_image || null,
    };

    if (album.id) {
        const { data } = await axios.put(`/gallery-albums/${album.id}`, payload);
        Object.assign(album, decorateAlbum({ ...data, items: album.items }));
    } else {
        const { data } = await axios.post('/gallery-albums', payload);
        Object.assign(album, decorateAlbum({ ...data, items: [] }));
    }

    syncAlbumItems();
    if (!album.id) {
        selectAlbum(album);
    }
}

async function removeAlbum(album) {
    if (album.id) {
        if (!confirm(`Hapus album "${album.title || 'album'}"?`)) {
            return;
        }
        await axios.delete(`/gallery-albums/${album.id}`);
    }
    const index = albums.findIndex((item) => item._key === album._key);
    if (index !== -1) {
        albums.splice(index, 1);
    }
    if (selected.value?._key === album._key) {
        selected.value = null;
        galleryItems.splice(0, galleryItems.length);
    }
}

function addGalleryItem() {
    const item = decorateItem();
    galleryItems.unshift(item);
    syncAlbumItems();
}

async function saveGalleryItem(item) {
    if (!selected.value?.id) {
        await saveAlbum(selected.value);
    }

    const payload = {
        gallery_album_id: selected.value.id,
        title: item.title || null,
        caption: item.caption || null,
        image_url: item.image_url,
        order: Number(item.order) || 0,
    };

    if (item.id) {
        const { data } = await axios.put(`/gallery-items/${item.id}`, payload);
        Object.assign(item, decorateItem({ ...data }));
    } else {
        const { data } = await axios.post('/gallery-items', payload);
        Object.assign(item, decorateItem({ ...data }));
    }

    syncAlbumItems();
}

async function removeGalleryItem(item) {
    if (item.id) {
        if (!confirm('Hapus foto ini?')) {
            return;
        }
        await axios.delete(`/gallery-items/${item.id}`);
    }
    const index = galleryItems.findIndex((photo) => photo._key === item._key);
    if (index !== -1) {
        galleryItems.splice(index, 1);
    }
    syncAlbumItems();
}

onMounted(loadAlbums);
</script>
