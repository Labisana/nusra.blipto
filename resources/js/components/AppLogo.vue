<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name || 'BLiP Nusra';
</script>

<template>
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-lg bg-gradient-to-br from-[#fb4f00] to-[#e04600] p-1.5 text-white shadow-sm ring-1 ring-[#fb4f00]/30 transition-transform duration-200 group-hover/menu-button:scale-105"
    >
        <AppLogoIcon class="size-5 brightness-0 invert drop-shadow" />
    </div>
    <div class="ml-1.5 grid flex-1 text-left leading-tight">
        <div class="flex items-center gap-1.5">
            <span class="truncate text-sm font-bold tracking-tight text-foreground">
                BLiP <span class="text-[#fb4f00]">Gudang</span>
            </span>
            <span class="rounded bg-[#fb4f00]/10 px-1 py-0.2 text-[10px] font-semibold text-[#fb4f00] dark:bg-[#fb4f00]/20">
                PRO
            </span>
        </div>
        <span class="truncate text-[11px] text-muted-foreground font-medium">
            {{ name }}
        </span>
    </div>
</template>
