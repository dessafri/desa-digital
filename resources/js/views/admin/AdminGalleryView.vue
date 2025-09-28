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
                        <jodit-editor
                            :jsUrl="joditJsUrl"
                            :cssUrl="joditCssUrl"
                            v-model="selected.description"
                            :config="descEditorConfig"
                        />
                    </label>
                    
                </div>

                <div class="mt-8 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-700">Upload Kegiatan (Multiple)</h3>
                    <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
                        <div class="grid gap-2 text-xs font-semibold text-slate-500">
                            <label class="space-y-1">
                                Album Tujuan
                                <select v-model="uploadTargetAlbumId" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                    <option v-for="album in albums" :key="album._key" :value="album.id">{{ album.title || '(Album Baru)' }}</option>
                                </select>
                            </label>
                            <label class="space-y-1">
                                Pilih Gambar (bisa banyak)
                                <input type="file" accept="image/*" multiple @change="onFilesSelected" class="block w-full text-sm" />
                            </label>
                        </div>
                        <div class="flex items-end gap-2">
                            <button class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="!pendingFiles.length || uploading" @click="uploadAll">
                                {{ uploading ? 'Mengunggah...' : 'Upload Semua' }}
                            </button>
                            <button class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 disabled:opacity-60" :disabled="!pendingFiles.length || uploading" @click="clearPending">
                                Bersihkan
                            </button>
                        </div>
                    </div>

                    <div v-if="pendingFiles.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        <div v-for="(item, idx) in pendingPreviews" :key="idx" class="group cursor-pointer overflow-hidden rounded-lg border bg-white shadow-sm" @click="openPreview(item.url)">
                            <img :src="item.url" :alt="item.name" class="h-28 w-full object-cover transition group-hover:scale-105" />
                            <div class="p-2 text-[11px] font-semibold text-slate-500 truncate">{{ item.name }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-between">
                    <h3 class="text-md font-semibold text-slate-900">Foto dalam Album</h3>
                    <span class="text-xs text-slate-400">Seret kartu untuk mengubah urutan</span>
                </div>

                <div class="mt-4 space-y-4">
                    <article
                        v-for="(item, idx) in galleryItems"
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
                        <div class="grid gap-4 md:grid-cols-4 text-xs font-semibold text-slate-500">
                            <div class="space-y-1">
                                Pratinjau
                                <div class="overflow-hidden rounded-md border bg-white" v-if="item.image_url">
                                    <img :src="item.image_url" class="h-20 w-full object-cover cursor-pointer" @click="openPreview(item.image_url)" />
                                </div>
                                <div v-else class="h-20 w-full rounded-md border bg-white grid place-items-center text-slate-400">Tidak ada</div>
                            </div>
                            <label class="space-y-1 md:col-span-3">
                                Caption
                                <input v-model="item.caption" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
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

        <!-- Modal Preview -->
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
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import notify from '../../utils/notify';
import { JODIT_JS_CDN, JODIT_CSS_CDN, createJoditConfig } from '../../utils/jodit';

const joditJsUrl = JODIT_JS_CDN;
const joditCssUrl = JODIT_CSS_CDN;
const descEditorConfig = createJoditConfig({ minHeight: 200 });

const albums = reactive([]);
const selected = ref(null);
const galleryItems = reactive([]);
const uploadTargetAlbumId = ref(null);
const pendingFiles = reactive([]);
const pendingPreviews = reactive([]);
const uploading = ref(false);
const previewUrl = ref('');
const dragIndex = ref(null);
const dragOverIndex = ref(null);

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
    uploadTargetAlbumId.value = album.id ?? null;
}

async function saveAlbum(album) {
    const payload = {
        title: album.title,
        slug: album.slug || null,
        description: album.description || null,
        cover_image: null,
    };

    try {
        if (album.id) {
            const { data } = await axios.put(`/gallery-albums/${album.id}`, payload);
            Object.assign(album, decorateAlbum({ ...data, items: album.items }));
            notify.success('Album diperbarui');
        } else {
            const { data } = await axios.post('/gallery-albums', payload);
            Object.assign(album, decorateAlbum({ ...data, items: [] }));
            notify.success('Album dibuat');
        }

        syncAlbumItems();
        if (!album.id) {
            selectAlbum(album);
        }
    } catch (e) {
        console.error(e);
        notify.error('Gagal menyimpan album');
    }
}

async function removeAlbum(album) {
    if (album.id) {
        if (!confirm(`Hapus album "${album.title || 'album'}"?`)) {
            return;
        }
        try {
            await axios.delete(`/gallery-albums/${album.id}`);
            notify.success('Album dihapus');
        } catch (e) {
            console.error(e);
            notify.error('Gagal menghapus album');
            return;
        }
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

    try {
        if (item.id) {
            const { data } = await axios.put(`/gallery-items/${item.id}`, payload);
            Object.assign(item, decorateItem({ ...data }));
            notify.success('Foto diperbarui');
        } else {
            const { data } = await axios.post('/gallery-items', payload);
            Object.assign(item, decorateItem({ ...data }));
            notify.success('Foto ditambahkan');
        }
        syncAlbumItems();
    } catch (e) {
        console.error(e);
        notify.error('Gagal menyimpan foto');
    }
}

async function removeGalleryItem(item) {
    if (item.id) {
        if (!confirm('Hapus foto ini?')) {
            return;
        }
        try {
            await axios.delete(`/gallery-items/${item.id}`);
            notify.success('Foto dihapus');
        } catch (e) {
            console.error(e);
            notify.error('Gagal menghapus foto');
            return;
        }
    }
    const index = galleryItems.findIndex((photo) => photo._key === item._key);
    if (index !== -1) {
        galleryItems.splice(index, 1);
    }
    syncAlbumItems();
}

onMounted(loadAlbums);

function onFilesSelected(event) {
    const files = Array.from(event.target.files || []);
    pendingFiles.splice(0, pendingFiles.length, ...files);
    pendingPreviews.splice(0, pendingPreviews.length, ...files.map((f) => ({ name: f.name, url: URL.createObjectURL(f) })));
}

function clearPending() {
    pendingFiles.splice(0, pendingFiles.length);
    pendingPreviews.forEach((p) => URL.revokeObjectURL(p.url));
    pendingPreviews.splice(0, pendingPreviews.length);
}

async function uploadAll() {
    if (!pendingFiles.length || uploading.value) return;
    const albumId = uploadTargetAlbumId.value || selected.value?.id;
    if (!albumId) {
        alert('Pilih album tujuan terlebih dahulu.');
        return;
    }
    const form = new FormData();
    form.append('gallery_album_id', String(albumId));
    pendingFiles.forEach((file) => form.append('files[]', file));
    uploading.value = true;
    try {
        const { data } = await axios.post('/gallery-items/upload', form, { headers: { 'Content-Type': 'multipart/form-data' } });
        const created = Array.isArray(data) ? data : [];
        const items = created.map(decorateItem);
        const targetAlbum = albums.find((a) => a.id === albumId);
        if (targetAlbum) {
            targetAlbum.items = [...(targetAlbum.items || []), ...items.map((it) => plainItem(it))];
        }
        if (selected.value?.id === albumId) {
            galleryItems.splice(0, 0, ...items); // prepend
            syncAlbumItems();
        }
        clearPending();
        notify.success('Upload selesai');
    } catch (e) {
        console.error(e);
        notify.error('Upload gagal');
    } finally {
        uploading.value = false;
    }
}

function openPreview(url) {
    previewUrl.value = url;
}

function closePreview() {
    previewUrl.value = '';
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
        const [moved] = galleryItems.splice(from, 1);
        galleryItems.splice(to, 0, moved);
        // Recalculate orders
        galleryItems.forEach((it, i) => (it.order = i));
        await persistOrder();
        syncAlbumItems();
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
        const updates = galleryItems
            .filter((it) => it.id)
            .map((it, i) => axios.put(`/gallery-items/${it.id}`, { order: i }));
        if (updates.length) {
            await Promise.all(updates);
            notify.success('Urutan tersimpan');
        }
    } catch (e) {
        console.error('Gagal menyimpan urutan:', e);
        notify.error('Gagal menyimpan urutan');
    }
}
</script>

<style scoped>
article[draggable="true"] {
    cursor: move;
}
</style>
