<template>
    <textarea ref="el"></textarea>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    config: { type: Object, default: () => ({}) },
    editorUrl: { type: String, required: true },
});

const emit = defineEmits(['update:modelValue']);

const el = ref(null);
const instance = ref(null);
const elementId = 'ck4-' + Math.random().toString(36).slice(2, 10);

function loadCkeditor(src) {
    if (window.CKEDITOR) {
        return Promise.resolve(window.CKEDITOR);
    }
    if (window.__ck4LoadingPromise) {
        return window.__ck4LoadingPromise;
    }
    window.__ck4LoadingPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);
        const done = () => resolve(window.CKEDITOR);
        if (existing) {
            if (window.CKEDITOR) {
                done();
                return;
            }
            // If script already loaded, resolve; else wait for next load
            if (existing.readyState === 'loaded' || existing.readyState === 'complete') {
                done();
                return;
            }
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
    return window.__ck4LoadingPromise;
}

onMounted(async () => {
    try {
        await loadCkeditor(props.editorUrl);
        await nextTick();
        if (!el.value) return;
        el.value.id = elementId;
        const editor = window.CKEDITOR.replace(elementId, { ...(props.config || {}) });
        instance.value = editor;
        editor.on('instanceReady', () => {
            const value = props.modelValue || '';
            if (value) {
                editor.setData(value);
            }
        });
        editor.on('change', () => {
            const data = editor.getData();
            if (data !== props.modelValue) {
                emit('update:modelValue', data);
            }
        });
    } catch (e) {
        // eslint-disable-next-line no-console
        console.error('CKEditor 4 failed to load:', e);
    }
});

watch(
    () => props.modelValue,
    (val) => {
        const editor = instance.value;
        if (editor && typeof val === 'string' && val !== editor.getData()) {
            editor.setData(val);
        }
    }
);

onBeforeUnmount(() => {
    const editor = instance.value;
    if (editor) {
        try { editor.destroy(true); } catch (e) {}
        instance.value = null;
    }
});
</script>

<style scoped>
textarea { display: none; }
</style>
