<template>
    <div class="relative" ref="wrapper" @keydown.escape.prevent.stop="open=false">
        <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm" @click="toggle">
            <span class="flex items-center gap-2">
                <component v-if="CurrentIcon" :is="CurrentIcon" class="h-5 w-5 text-slate-600" />
                <span v-else class="text-slate-500">{{ placeholder }}</span>
            </span>
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
        </button>
        <div v-if="open" class="absolute z-40 mt-1 w-[360px] max-w-[84vw] rounded-xl border border-slate-200 bg-white p-3 shadow-lg">
            <input v-model="query" type="text" placeholder="Cari ikon..." class="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
            <div class="max-h-64 overflow-auto">
                <div class="grid grid-cols-6 gap-2">
                    <button
                        v-for="opt in filtered"
                        :key="opt.name"
                        type="button"
                        class="group flex items-center justify-center rounded-lg border border-slate-200 p-2 hover:border-primary/50 hover:bg-primary/5"
                        :title="opt.name.replace('Icon','')"
                        @click="select(opt.name)"
                    >
                        <component :is="opt.component" class="h-5 w-5 text-slate-700 group-hover:text-primary" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import * as Outline from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Pilih ikon' },
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const query = ref('');
const wrapper = ref(null);

const options = computed(() => Object.entries(Outline)
    .filter(([name]) => name.endsWith('Icon'))
    .map(([name, component]) => ({ name, component }))
);

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return options.value;
    return options.value.filter((opt) => opt.name.toLowerCase().includes(q));
});

const CurrentIcon = computed(() => {
    const name = props.modelValue;
    return name && Outline[name] ? Outline[name] : null;
});

function toggle() { open.value = !open.value; }
function select(name) { emit('update:modelValue', name); open.value = false; }

onMounted(() => document.addEventListener('click', onDocClick, true));
onBeforeUnmount(() => document.removeEventListener('click', onDocClick, true));

function onDocClick(e) {
    if (!open.value) return;
    const el = wrapper.value;
    if (el && !el.contains(e.target)) {
        open.value = false;
    }
}
</script>
