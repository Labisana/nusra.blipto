<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-2 border-b border-border/80 bg-background/85 px-4 backdrop-blur-md transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6"
    >
        <div class="flex items-center gap-2.5">
            <SidebarTrigger
                class="-ml-1 rounded-lg transition-colors hover:bg-[#fb4f00]/10 hover:text-[#fb4f00]"
            />
            <div class="mx-1 hidden h-4 w-px bg-border/60 sm:block"></div>
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="ml-auto flex items-center gap-3">
            <div
                class="hidden items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-medium text-emerald-600 sm:flex dark:text-emerald-400"
            >
                <span
                    class="size-1.5 animate-pulse rounded-full bg-emerald-500"
                ></span>
                <span>BLiP Online</span>
            </div>
            <ThemeToggle />
        </div>
    </header>
</template>
