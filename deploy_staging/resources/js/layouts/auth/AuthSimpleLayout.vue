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
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-background p-6 md:p-10"
    >
        <!-- Background Ambient Glow & Grid Pattern -->
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[500px] w-[600px] -translate-x-1/2 rounded-full bg-gradient-to-b from-[#fb4f00]/15 via-[#fb4f00]/5 to-transparent blur-3xl dark:from-[#fb4f00]/20 dark:via-[#fb4f00]/5"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(#fb4f00_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.03] dark:opacity-[0.06]"></div>

        <div class="w-full max-w-md">
            <div class="relative rounded-2xl border border-border/80 bg-card/90 p-8 shadow-xl shadow-black/5 backdrop-blur-xl transition-all dark:border-border/60 dark:bg-card/75 dark:shadow-black/20">
                <!-- Top Brand Accent Bar -->
                <div class="absolute inset-x-8 top-0 h-1 rounded-b-full bg-gradient-to-r from-transparent via-[#fb4f00] to-transparent"></div>

                <div class="flex flex-col gap-6">
                    <div class="flex flex-col items-center gap-3">
                        <Link
                            :href="home()"
                            class="group flex flex-col items-center gap-2 transition-transform duration-200 hover:scale-[1.02]"
                        >
                            <div class="relative flex h-16 items-center justify-center">
                                <img
                                    src="/images/blip-logo.png"
                                    alt="BLiP"
                                    class="h-14 w-auto dark:hidden"
                                />
                                <img
                                    src="/images/blip-logo-white.png"
                                    alt="BLiP"
                                    class="hidden h-14 w-auto dark:block"
                                />
                            </div>
                            <span class="sr-only">{{ title }}</span>
                        </Link>
                        <div class="space-y-1.5 text-center">
                            <h1 class="text-xl font-bold tracking-tight text-foreground">{{ title }}</h1>
                            <p class="text-xs text-muted-foreground">
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>

            <!-- Footer note -->
            <p class="mt-6 text-center text-xs text-muted-foreground">
                &copy; {{ new Date().getFullYear() }} BLiP Nusra · PT Bali Lintas Prestasi
            </p>
        </div>
    </div>
</template>
