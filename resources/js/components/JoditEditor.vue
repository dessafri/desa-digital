<template>
    <div ref="el"></div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    config: { type: Object, default: () => ({}) },
    jsUrl: { type: String, default: 'https://cdn.jsdelivr.net/npm/jodit@3.24.7/build/jodit.min.js' },
    cssUrl: { type: String, default: 'https://cdn.jsdelivr.net/npm/jodit@3.24.7/build/jodit.min.css' },
});

const emit = defineEmits(['update:modelValue']);

const el = ref(null);
let editor = null;

function ensureCss(href) {
    if (document.querySelector(`link[href="${href}"]`)) return;
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
}

function loadJodit(src) {
    if (window.Jodit) return Promise.resolve(window.Jodit);
    if (window.__joditLoadingPromise) return window.__joditLoadingPromise;
    window.__joditLoadingPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);
        const done = () => resolve(window.Jodit);
        if (existing) {
            if (window.Jodit) return done();
            existing.addEventListener('load', done, { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }
        const s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onload = done;
        s.onerror = reject;
        document.head.appendChild(s);
    });
    return window.__joditLoadingPromise;
}

onMounted(async () => {
    try {
        ensureCss(props.cssUrl);
        await loadJodit(props.jsUrl);
        await nextTick();
        if (!el.value) return;
        editor = new window.Jodit(el.value, { ...props.config });
        editor.value = props.modelValue || '';
        editor.events.on('change', () => {
            const val = editor.value || '';
            if (val !== props.modelValue) emit('update:modelValue', val);
        });
    } catch (e) {
        // eslint-disable-next-line no-console
        console.error('Failed to load Jodit:', e);
    }
});

watch(
    () => props.modelValue,
    (val) => {
        if (editor && typeof val === 'string' && val !== editor.value) {
            editor.value = val;
        }
    }
);

onBeforeUnmount(() => {
    if (editor && editor.destruct) {
        try { editor.destruct(); } catch (e) {}
        editor = null;
    }
});
</script>

<style scoped>
/* Jodit injects its own UI; the container is empty by default */
</style>

