<template>
    <div class="min-h-screen flex flex-col bg-slate-50">
        <MainHeader v-if="!isAdminRoute" />
        <main class="flex-1">
            <RouterView v-slot="{ Component, route }">
                <Transition name="fade" mode="out-in" appear>
                    <component :is="Component" :key="route.fullPath" />
                </Transition>
            </RouterView>
        </main>
        <MainFooter v-if="!isAdminRoute" />
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import MainHeader from './components/layout/MainHeader.vue';
import MainFooter from './components/layout/MainFooter.vue';
import { useSiteStore } from './stores/site';

const route = useRoute();
const siteStore = useSiteStore();

const isAdminRoute = computed(() => route.path.startsWith('/admin'));

onMounted(() => {
    siteStore.bootstrap();
});
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
