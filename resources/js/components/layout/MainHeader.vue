<template>
    <header :class="headerClass">
        <div class="mx-auto flex h-20 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-10">
            <RouterLink to="/" class="flex items-center gap-2 transition" :class="brandTextClass">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border text-sm font-semibold" :class="brandBadgeClass">
                    DD
                </span>
                <div class="flex flex-col">
                    <span class="text-lg font-semibold" :class="brandNameClass">{{ siteName }}</span>
                    <span class="text-xs" :class="brandSubtitleClass">Portal Desa Digital</span>
                </div>
            </RouterLink>

            <div class="hidden items-center gap-10 md:flex">
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <RouterLink
                        v-for="item in menu"
                        :key="item.to"
                        :to="item.to"
                        class="transition"
                        :class="navLinkClass(item)"
                    >
                        {{ item.name }}
                    </RouterLink>
                </nav>
                <a
                    href="/admin"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold shadow transition" :class="adminButtonClass"
                >
                    Panel Admin
                </a>
            </div>

            <button
                class="inline-flex items-center justify-center rounded-full border p-2 transition md:hidden" :class="mobileToggleClass"
                @click="mobileOpen = !mobileOpen"
                aria-label="Toggle menu"
            >
                <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <transition name="fade">
            <div v-if="mobileOpen" class="md:hidden border-t bg-white/95 backdrop-blur shadow">
                <nav class="flex flex-col p-4 text-sm">
                    <RouterLink
                        v-for="item in menu"
                        :key="item.to"
                        :to="item.to"
                        class="rounded-lg px-3 py-2 transition"
                        :class="mobileLinkClass(item)"
                        @click="mobileOpen = false"
                    >
                        {{ item.name }}
                    </RouterLink>
                    <a
                        href="/admin"
                        target="_blank"
                        rel="noopener"
                        class="mt-2 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-primary to-secondary px-4 py-2 text-sm font-semibold text-white"
                        @click="mobileOpen = false"
                    >
                        Panel Admin
                    </a>
                </nav>
            </div>
        </transition>
    </header>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useSiteStore } from '../../stores/site';

const siteStore = useSiteStore();
const route = useRoute();

const mobileOpen = ref(false);
const isScrolled = ref(false);

const updateScrollState = () => {
    if (typeof window === 'undefined') return;
    isScrolled.value = window.scrollY > 40;
};

onMounted(() => {
    updateScrollState();
    window.addEventListener('scroll', updateScrollState, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScrollState);
});

watch(
    () => route.fullPath,
    () => {
        mobileOpen.value = false;
        nextTick(updateScrollState);
    }
);

const menu = computed(() => siteStore.menu);
const siteName = computed(() => siteStore.siteName);

const baseHeaderClass = 'fixed top-0 z-50 w-full transition-all duration-300';
const showSolidBackground = computed(() => route.path !== '/' || isScrolled.value);
const headerClass = computed(() => {
    const color = showSolidBackground.value ? 'text-slate-900' : 'text-white';
    const surface = showSolidBackground.value ? 'bg-white/90 backdrop-blur shadow' : 'bg-transparent';
    return `${baseHeaderClass} ${color} ${surface}`;
});

const brandTextClass = computed(() => (showSolidBackground.value ? 'text-slate-900' : 'text-white'));
const brandBadgeClass = computed(() => (showSolidBackground.value ? 'border-primary/40 text-primary' : 'border-white/60 text-white'));
const brandNameClass = computed(() => (showSolidBackground.value ? 'text-slate-900' : 'text-white'));
const brandSubtitleClass = computed(() => (showSolidBackground.value ? 'text-slate-500' : 'text-white/70'));
const adminButtonClass = computed(() =>
    showSolidBackground.value
        ? 'border border-primary/40 text-primary hover:bg-primary hover:text-white'
        : 'border border-white/60 text-white hover:bg-white/10'
);
const mobileToggleClass = computed(() =>
    showSolidBackground.value
        ? 'border-slate-200 text-slate-600 hover:border-primary hover:text-primary'
        : 'border-white/60 text-white hover:border-white hover:text-white'
);

const navLinkClass = (item) => {
    const active = (route.path.startsWith(item.to) && item.to !== '/') || (route.path === '/' && item.to === '/');
    if (showSolidBackground.value) {
        return active ? 'text-primary font-semibold' : 'text-slate-600 hover:text-primary';
    }
    return active ? 'text-white font-semibold' : 'text-white/80 hover:text-white';
};

const mobileLinkClass = (item) => {
    const active = (route.path.startsWith(item.to) && item.to !== '/') || (route.path === '/' && item.to === '/');
    return active ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100';
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
