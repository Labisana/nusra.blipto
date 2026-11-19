<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div
            class="relative hidden h-full flex-col border-border bg-zinc-950 p-10 text-white lg:flex dark:border-r"
        >
            <div
                class="absolute inset-0 bg-gradient-to-br from-[#fb4f00]/20 via-zinc-950 to-zinc-950"
            />
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(#fb4f00_1px,transparent_1px)] [background-size:24px_24px] opacity-10"
            ></div>
            <Link
                :href="home()"
                class="relative z-20 flex items-center text-lg font-bold"
            >
                <img
                    src="/images/blip-logo-white.png"
                    alt="BLiP"
                    class="h-12 w-auto"
                />
            </Link>
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-base text-zinc-300">
                        &ldquo;Platform manajemen logistik terpadu untuk
                        percepatan operasional jaringan telekomunikasi BLiP
                        Nusra.&rdquo;
                    </p>
                    <footer class="text-xs font-semibold text-[#fb4f00]">
                        PT Bali Lintas Prestasi
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[360px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                        v-if="title"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-xs text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
