<template>
    <div class="bg-slate-50 text-slate-900">
        <!-- Hero -->
        <section class="relative overflow-hidden isolate" :style="heroBackground">
            <div class="absolute inset-0 bg-slate-900/60"></div>
            <div class="pointer-events-none absolute -left-40 -top-32 h-80 w-80 rounded-full bg-primary/60 blur-3xl mix-blend-screen"></div>
            <div class="pointer-events-none absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-secondary/55 blur-3xl mix-blend-screen"></div>
            <div class="relative z-10 mx-auto flex min-h-[90vh] max-w-6xl flex-col gap-12 px-4 pb-24 pt-28 sm:px-6 lg:flex-row lg:items-center lg:gap-16 lg:px-10">
                <div class="flex-1 space-y-8 text-white" :ref="el => registerReveal(el)">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/40 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em]">
                        Desa Sugihwaras Digital
                    </span>
                    <h1 class="text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                        {{ hero.title || 'Wajah baru desa yang ramah, modern, dan transparan.' }}
                    </h1>
                    <p class="max-w-xl text-sm text-white/80 sm:text-base">
                        {{ hero.subtitle || 'Akses layanan publik, berita desa, dan dokumentasi kegiatan dengan tampilan yang bersih dan dekat dengan warga.' }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <RouterLink
                            v-if="hero.ctaLink"
                            :to="hero.ctaLink"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary to-secondary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:shadow-xl"
                        >
                            {{ hero.ctaText || 'Jelajahi Profil Desa' }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </RouterLink>
                        <a href="#profil" class="inline-flex items-center gap-2 rounded-full border border-white/40 px-6 py-3 text-sm font-semibold text-white/90 transition hover:border-white hover:bg-white/10 hover:text-white">
                            Lihat Ringkasan Desa
                        </a>
                    </div>
                    <div class="mt-12 grid gap-4 sm:grid-cols-3">
                        <div v-for="stat in topStats" :key="stat.id" class="rounded-2xl border border-white/20 bg-white/90 p-5 text-slate-900 shadow backdrop-blur">
                            <p class="text-xs uppercase tracking-widest text-primary/70">{{ stat.label }}</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ formatNumber(stat.value) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1" :ref="el => registerReveal(el, 150)">
                    <div class="space-y-6">
                        <article class="rounded-3xl border border-white/10 bg-white/85 p-6 text-slate-900 shadow backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.3em] text-primary/70">Berita terbaru</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900">{{ news[0]?.title || 'Berita desa akan tampil di sini' }}</p>
                            <p class="mt-3 text-xs text-slate-500 line-clamp-3">{{ news[0]?.excerpt || 'Segera bagikan kabar terbaru seputar desa Sugihwaras agar warga selalu terinformasi.' }}</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/85 p-6 text-slate-900 shadow backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.3em] text-primary/70">Layanan unggulan</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900">{{ services[0]?.title || 'Layanan Administrasi Online' }}</p>
                            <p class="mt-3 text-xs text-slate-500 line-clamp-3">{{ services[0]?.description || 'Permudah pengurusan dokumen dengan layanan administrasi daring yang transparan.' }}</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/85 p-6 text-slate-900 shadow backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.3em] text-primary/70">Kontak segera</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900">{{ contact?.phone || '+62 000 0000 0000' }}</p>
                            <p class="mt-3 text-xs text-slate-500">Kantor desa siap melayani pertanyaan dan aspirasi warga setiap hari kerja.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profil -->
        <section id="profil" class="mx-auto grid max-w-6xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-10">
            <article class="rounded-3xl bg-white p-8 shadow-sm" :ref="el => registerReveal(el)">
                <h2 class="text-xs uppercase tracking-[0.3em] text-primary">Profil Desa</h2>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ profileCopy.title }}</p>
                <p class="mt-5 text-sm leading-relaxed text-slate-600">{{ profileCopy.about }}</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-primary/5 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-primary">Sejarah</p>
                        <p class="mt-2 text-xs text-slate-600 line-clamp-4">{{ profileCopy.history }}</p>
                    </div>
                    <div class="rounded-2xl bg-secondary/5 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-secondary">Geografis</p>
                        <p class="mt-2 text-xs text-slate-600 line-clamp-4">{{ profileCopy.geography }}</p>
                    </div>
                </div>
            </article>
            <figure class="rounded-3xl bg-white p-8 shadow-sm" :ref="el => registerReveal(el, 150)">
                <h3 class="text-xs uppercase tracking-[0.3em] text-primary">Visi & Misi</h3>
                <p class="mt-4 text-lg font-semibold text-slate-900">{{ vision.title || 'Visi Desa Sugihwaras' }}</p>
                <p class="mt-3 text-sm text-slate-600">{{ vision.body || 'Mewujudkan desa yang adaptif dan berbasis digital.' }}</p>
                <div class="mt-6 space-y-3">
                    <div v-for="(item, index) in missionItems" :key="index" class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-primary"></span>
                        <p class="text-xs text-slate-600">{{ item }}</p>
                    </div>
                </div>
            </figure>
        </section>

        <!-- Layanan -->
        <section class="bg-white">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-10">
                <div class="flex flex-wrap items-end justify-between gap-4" :ref="el => registerReveal(el)">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">Layanan Digital Desa</h2>
                        <p class="text-xs text-slate-500">Kemudahan akses administrasi dan aspirasi warga</p>
                    </div>
                    <RouterLink to="/admin/layanan" class="text-xs font-semibold text-primary hover:underline">
                        Kelola Layanan
                    </RouterLink>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" :ref="el => registerReveal(el, 100)">
                    <article
                        v-for="service in services"
                        :key="service.id"
                        class="group rounded-3xl border border-slate-100 bg-gradient-to-br from-primary/5 via-white to-secondary/5 p-6 shadow-sm transition hover:-translate-y-1"
                    >
                        <p class="text-xs uppercase tracking-[0.25em] text-primary/70">{{ service.icon || 'Layanan' }}</p>
                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ service.title }}</h3>
                        <p class="mt-3 text-sm text-slate-600">{{ service.description }}</p>
                        <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-primary">
                            {{ service.link ? 'Pelajari layanan' : 'Informasi lengkap' }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </article>
                </div>
            </div>
        </section>

        <!-- Berita -->
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-10">
            <div class="flex flex-wrap items-end justify-between gap-4" :ref="el => registerReveal(el)">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Berita Desa</h2>
                    <p class="text-xs text-slate-500">Update aktivitas pemerintahan dan masyarakat</p>
                </div>
                <RouterLink to="/berita" class="text-xs font-semibold text-primary hover:underline">
                    Lihat Semua Berita
                </RouterLink>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" :ref="el => registerReveal(el, 120)">
                <article
                    v-for="post in news"
                    :key="post.id"
                    class="flex h-full flex-col justify-between rounded-3xl border border-slate-100 bg-white p-6 shadow-sm"
                >
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-primary/70">{{ formatDate(post.published_at) }}</p>
                        <h3 class="text-lg font-semibold text-slate-900 line-clamp-2">{{ post.title }}</h3>
                        <p class="text-sm text-slate-600 line-clamp-3">{{ post.excerpt }}</p>
                    </div>
                    <RouterLink :to="`/berita/${post.slug || post.id}`" class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-primary">
                        Baca selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </RouterLink>
                </article>
                <div v-if="!news.length" class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-400">
                    Belum ada berita terbaru.
                </div>
            </div>
        </section>

        <!-- CTA dan Galeri -->
        <section class="bg-white">
            <div class="mx-auto max-w-6xl px-4 pb-20 pt-16 sm:px-6 lg:px-10">
                <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                    <article class="rounded-3xl bg-gradient-to-br from-primary to-secondary text-white shadow-xl" :ref="el => registerReveal(el)">
                        <div class="space-y-6 p-8 sm:p-10">
                            <h2 class="text-2xl font-semibold">Punya pertanyaan atau masukan?</h2>
                            <p class="text-sm text-white/80">
                                Hubungi pemerintah Desa Sugihwaras melalui kanal resmi. Kami berkomitmen memberikan pelayanan publik yang cepat dan transparan.
                            </p>
                            <div class="flex flex-wrap gap-3 text-xs font-semibold">
                                <a v-if="contact?.phone" :href="`tel:${contact?.phone}`" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2 text-primary">
                                    Telepon Desa
                                </a>
                                <a v-if="contact?.email" :href="`mailto:${contact?.email}`" class="inline-flex items-center gap-2 rounded-full border border-white px-5 py-2 text-white">
                                    Email Sekretariat
                                </a>
                            </div>
                        </div>
                    </article>
                    <article class="rounded-3xl bg-slate-50 p-8 shadow-sm" :ref="el => registerReveal(el, 150)">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-primary">Galeri Desa</h2>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ album?.title || 'Kegiatan Desa Terbaru' }}</p>
                        <p class="mt-3 text-xs text-slate-500">{{ album?.description || 'Dokumentasi aktivitas warga dan perangkat desa.' }}</p>
                        <div v-if="album" class="mt-6 grid gap-3 sm:grid-cols-2">
                            <figure v-for="item in album.items.slice(0, 4)" :key="item.id" class="overflow-hidden rounded-2xl">
                                <img :src="item.image_url" :alt="item.title" class="h-32 w-full object-cover transition hover:scale-105" />
                            </figure>
                        </div>
                        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-xs text-slate-400">
                            Galeri belum tersedia.
                        </div>
                        <RouterLink to="/galeri" class="mt-6 inline-flex items-center gap-2 text-xs font-semibold text-primary">
                            Lihat Galeri
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </RouterLink>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount } from 'vue';
import { useSiteStore } from '../stores/site';

const siteStore = useSiteStore();

const hero = computed(() => siteStore.home?.hero ?? {});
const heroImage = computed(() => hero.value.image ?? 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=80');
const heroBackground = computed(() => {
    const image = heroImage.value;
    return {
        backgroundImage: `linear-gradient(120deg, rgba(8, 116, 167, 0.22) 0%, rgba(62, 180, 137, 0.3) 45%, rgba(255, 255, 255, 0.95) 100%), url('${image}')`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
    };
});

const vision = computed(() => siteStore.home?.vision ?? {});
const missionItems = computed(() => siteStore.missionList);
const statistics = computed(() => siteStore.displayedStatistics);
const topStats = computed(() => statistics.value.slice(0, 3));
const services = computed(() => siteStore.displayedServices);
const news = computed(() => siteStore.home?.news ?? []);
const album = computed(() => siteStore.featuredAlbum);
const contact = computed(() => siteStore.contact);
const profile = computed(() => siteStore.profile ?? {});

const profileCopy = computed(() => ({
    title: 'Gambaran singkat Desa Sugihwaras',
    about: profile.value.about || 'Sugihwaras merupakan desa dengan semangat kolaborasi warga yang tinggi serta fokus pada pelayanan publik berbasis teknologi.',
    history: profile.value.history || 'Sejak digitalisasi dimulai, Sugihwaras terus berinovasi menghadirkan layanan publik yang transparan.',
    geography: profile.value.geography || 'Wilayah desa terdiri dari area permukiman, pertanian, dan UMKM yang tersebar di 5 dusun.',
}));

let observer;
const registered = [];

const registerReveal = (el, delay = 0) => {
    if (!el) return;
    el.style.opacity = '0';
    el.style.transform = 'translateY(16px)';
    el.dataset.delay = String(delay);
    registered.push(el);
    if (observer) observer.observe(el);
};

onMounted(async () => {
    if (!siteStore.home) await siteStore.fetchHome();
    if (!siteStore.contact) await siteStore.fetchContact();
    if (!siteStore.profile) await siteStore.fetchProfile();

    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const delay = Number(entry.target.dataset.delay || 0);
                    setTimeout(() => entry.target.classList.add('animate-fade-up'), delay);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15 }
    );

    registered.forEach((el) => observer.observe(el));
});

onBeforeUnmount(() => {
    if (observer) observer.disconnect();
});

function formatNumber(value) {
    if (!value && value !== 0) return '-';
    if (value > 1000) return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(value);
    return new Intl.NumberFormat('id-ID').format(value);
}

function formatDate(date) {
    if (!date) return 'Draft';
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(date));
}
</script>







