<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
        <!-- Ambient Glow -->
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[500px] w-[600px] -translate-x-1/2 rounded-full bg-gradient-to-b from-[#fb4f00]/15 via-[#fb4f00]/5 to-transparent blur-3xl dark:from-[#fb4f00]/20"></div>

        <div class="flex w-full max-w-md flex-col gap-6">
            <Link
                :href="home()"
                class="group flex items-center gap-2 self-center font-medium transition-transform duration-200 hover:scale-[1.02]"
            >
                <div class="flex h-16 items-center justify-center">
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
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="relative overflow-hidden rounded-2xl border border-border/80 bg-card/90 shadow-xl shadow-black/5 backdrop-blur-xl">
                    <div class="absolute inset-x-8 top-0 h-1 rounded-b-full bg-gradient-to-r from-transparent via-[#fb4f00] to-transparent"></div>
                    <CardHeader class="px-8 pt-8 pb-0 text-center">
                        <CardTitle class="text-xl font-bold tracking-tight text-foreground">{{ title }}</CardTitle>
                        <CardDescription class="text-xs text-muted-foreground mt-1">
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-8 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>

            <p class="text-center text-xs text-muted-foreground">
                &copy; {{ new Date().getFullYear() }} BLiP Nusra · PT Bali Lintas Prestasi
            </p>
        </div>
    </div>
</template>
