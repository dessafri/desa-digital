<template>
    <footer class="bg-slate-900 text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <h3 class="text-xl font-semibold text-white">{{ siteName }}</h3>
                    <p class="mt-3 text-sm text-slate-400">
                        Portal informasi dan layanan resmi Desa Sugihwaras berbasis digital.
                    </p>
                </div>
                <div>
                    <h4 class="text-sm uppercase tracking-wide text-slate-400">Kontak</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li v-if="contact?.address">{{ contact.address }}</li>
                        <li v-if="contact?.phone">Telp: {{ contact.phone }}</li>
                        <li v-if="contact?.email">Email: {{ contact.email }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm uppercase tracking-wide text-slate-400">Ikuti Kami</h4>
                    <div class="mt-4 flex gap-3">
                        <a
                            v-if="contact?.social?.facebook"
                            :href="contact.social.facebook"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20"
                        >
                            <span class="sr-only">Facebook</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path
                                    d="M9.197 21V12.804H6V9.433h3.197V6.865C9.197 3.77 11.143 2 13.815 2c1.325 0 2.463.099 2.793.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.37h-3.12V21"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="contact?.social?.instagram"
                            :href="contact.social.instagram"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20"
                        >
                            <span class="sr-only">Instagram</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path
                                    d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm0 2h10c1.654 0 3 1.346 3 3v10c0 1.654-1.346 3-3 3H7c-1.654 0-3-1.346-3-3V7c0-1.654 1.346-3 3-3zm11 1a1 1 0 100 2 1 1 0 000-2zm-6 2a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-white/10 pt-6 text-sm text-slate-500">
                <p>&copy; {{ new Date().getFullYear() }} {{ siteName }}. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { useSiteStore } from '../../stores/site';

const siteStore = useSiteStore();
const siteName = computed(() => siteStore.siteName);
const contact = computed(() => siteStore.contact);
</script>
