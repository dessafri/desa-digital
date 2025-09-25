<template>
    <div class="bg-white">
        <section class="bg-gradient-to-r from-primary/20 via-white to-secondary/20 py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-bold text-slate-900">Profil Desa Sugihwaras</h1>
                <p class="mt-4 text-base text-slate-600">
                    Mengenal karakteristik, sejarah, dan potensi Desa Sugihwaras sebagai desa digital yang berdaya saing.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            <article class="grid gap-6 md:grid-cols-[2fr_3fr] items-start">
                <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-semibold text-slate-900">Tentang Desa</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ profile.about }}
                    </p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Sejarah Singkat</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ profile.history }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Kondisi Geografis</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ profile.geography }}</p>
                    </div>
                </div>
            </article>

            <article id="struktur" class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">Struktur Pemerintahan Desa</h2>
                        <p class="mt-3 text-sm text-slate-600 max-w-2xl">
                            Pemerintah Desa Sugihwaras dipimpin oleh Kepala Desa dan didukung perangkat desa profesional dalam memberikan pelayanan optimal.
                        </p>
                    </div>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="official in officials"
                        :key="official.id"
                        class="rounded-2xl border border-slate-100 bg-gradient-to-br from-white via-white to-primary/5 p-6 shadow-sm"
                    >
                        <div class="flex items-center gap-4">
                            <img
                                :src="official.photo"
                                :alt="official.name"
                                class="h-16 w-16 rounded-full object-cover"
                            />
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ official.name }}</p>
                                <p class="text-xs text-primary font-medium">{{ official.position }}</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-1 text-xs text-slate-500">
                            <p v-if="official.phone">Telp: {{ official.phone }}</p>
                            <p v-if="official.email">Email: {{ official.email }}</p>
                        </div>
                    </div>
                </div>
            </article>

            <article id="visi" class="grid gap-6 md:grid-cols-2">
                <div class="rounded-3xl border border-secondary/20 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-semibold text-secondary">Visi</h2>
                    <p class="mt-4 text-sm text-slate-600 leading-relaxed">{{ profile.vision }}</p>
                </div>
                <div class="rounded-3xl border border-primary/20 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-semibold text-primary">Misi</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li v-for="(item, index) in mission" :key="index" class="flex gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-primary"></span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </article>

            <article v-if="profile.mapEmbed" class="rounded-3xl overflow-hidden border border-slate-100 shadow-sm">
                <iframe
                    :src="profile.mapEmbed"
                    width="100%"
                    height="420"
                    style="border:0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </article>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useSiteStore } from '../stores/site';

const siteStore = useSiteStore();

const profile = computed(() => siteStore.profile ?? {});
const mission = computed(() => siteStore.missionList);
const officials = computed(() => siteStore.profile?.officials ?? []);

onMounted(async () => {
    await siteStore.fetchProfile();
});
</script>
