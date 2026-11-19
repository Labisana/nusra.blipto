<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance, updateAppearance } = useAppearance();

// The server cannot know the visitor's saved theme, so render a neutral
// state until the component is mounted and the client preference is known.
const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});

const isDark = computed(() => mounted.value && resolvedAppearance.value === 'dark');
const isLight = computed(() => mounted.value && resolvedAppearance.value !== 'dark');

const activeClasses =
    'bg-background text-foreground shadow-xs dark:bg-neutral-700 dark:text-white';
const inactiveClasses =
    'text-muted-foreground hover:bg-background/60 hover:text-foreground dark:hover:bg-neutral-700/60';
</script>

<template>
    <div
        class="inline-flex items-center gap-0.5 rounded-md bg-muted p-0.5"
        role="group"
        aria-label="Pilih tema tampilan"
    >
        <button
            type="button"
            class="inline-flex size-7 items-center justify-center rounded-sm transition-colors"
            :class="isLight ? activeClasses : inactiveClasses"
            :aria-pressed="isLight"
            title="Mode terang"
            aria-label="Mode terang"
            @click="updateAppearance('light')"
        >
            <Sun class="size-4" />
        </button>
        <button
            type="button"
            class="inline-flex size-7 items-center justify-center rounded-sm transition-colors"
            :class="isDark ? activeClasses : inactiveClasses"
            :aria-pressed="isDark"
            title="Mode gelap"
            aria-label="Mode gelap"
            @click="updateAppearance('dark')"
        >
            <Moon class="size-4" />
        </button>
    </div>
</template>
