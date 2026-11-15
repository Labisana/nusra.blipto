<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowRight,
    ArrowUpFromLine,
    Boxes,
    FileSpreadsheet,
    Lock,
    RadioTower,
    ShieldCheck,
    Users,
} from '@lucide/vue';
import { dashboard, login } from '@/routes';

const features = [
    {
        icon: ArrowDownToLine,
        title: 'Barang Masuk (FPB & Dismantle)',
        description: 'Pencatatan mutasi masuk perangkat baru dan hasil bongkaran dari pelanggan secara terstruktur.',
        color: 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
    },
    {
        icon: ArrowUpFromLine,
        title: 'Barang Keluar & Distribusi',
        description: 'Alokasi perangkat ke pelanggan dan site dengan validasi stok riil serta pemilihan serial number.',
        color: 'text-[#fb4f00] bg-[#fb4f00]/10 border-[#fb4f00]/20',
    },
    {
        icon: Boxes,
        title: 'Pelacakan Serial Number',
        description: 'Traceability status unit berserial apakah tersedia di gudang atau terpasang aktif di site pelanggan.',
        color: 'text-sky-500 bg-sky-500/10 border-sky-500/20',
    },
    {
        icon: RadioTower,
        title: 'Koneksi BTS & Pelanggan',
        description: 'Sinkronisasi menyeluruh antara master pelanggan, perangkat ONT/Router, dan node transmisi BTS/BSC.',
        color: 'text-purple-500 bg-purple-500/10 border-purple-500/20',
    },
];
</script>

<template>
    <Head title="Sistem Inventaris BLiP Nusra" />

    <div class="relative min-h-screen overflow-hidden bg-background text-foreground antialiased">
        <!-- Ambient Glowing Elements -->
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-b from-[#fb4f00]/15 via-[#fb4f00]/5 to-transparent blur-3xl dark:from-[#fb4f00]/20"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(#fb4f00_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.03] dark:opacity-[0.06]"></div>

        <!-- Navigation Header -->
        <header class="sticky top-0 z-40 border-b border-border/80 bg-background/80 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#fb4f00] to-[#e04600] p-1.5 shadow-md shadow-[#fb4f00]/20">
                        <img src="/images/blip-mark.png" alt="BLiP" class="size-5 brightness-0 invert drop-shadow" />
                    </div>
                    <div>
                        <span class="text-base font-bold tracking-tight text-foreground">BLiP <span class="text-[#fb4f00]">Gudang</span></span>
                        <span class="ml-1.5 rounded-full bg-[#fb4f00]/10 px-2 py-0.5 text-[10px] font-bold text-[#fb4f00]">Nusra</span>
                    </div>
                </div>

                <nav class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105"
                    >
                        <span>Buka Dashboard</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95"
                    >
                        <Lock class="size-3.5" />
                        <span>Masuk ke Sistem</span>
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="flex flex-col items-center text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-[#fb4f00]/30 bg-[#fb4f00]/10 px-3 py-1 text-xs font-semibold text-[#fb4f00]">
                    <span class="size-2 rounded-full bg-[#fb4f00] animate-pulse"></span>
                    Sistem Manajemen Inventaris & Logistik Telekomunikasi
                </div>

                <h1 class="mt-6 max-w-4xl text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                    Kendali Presisi Stok & Perangkat Jaringan <span class="bg-gradient-to-r from-[#fb4f00] via-orange-500 to-amber-500 bg-clip-text text-transparent">BLiP Nusra</span>
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:text-base">
                    Platform terpadu untuk pencatatan transaksi barang masuk, distribusi perangkat ke pelanggan, pelacakan serial number perangkat BTS, dan audit laporan stok akurat.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-[#fb4f00]/30 transition-all hover:brightness-105 active:scale-95"
                    >
                        <span>Akses Dashboard Gudang</span>
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-[#fb4f00]/30 transition-all hover:brightness-105 active:scale-95"
                    >
                        <span>Masuk ke Portal</span>
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="mt-20 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="f in features"
                    :key="f.title"
                    class="group relative rounded-2xl border border-border/80 bg-card p-6 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:border-[#fb4f00]/40 hover:shadow-md"
                >
                    <div :class="['inline-flex rounded-xl border p-3 mb-4 transition-transform group-hover:scale-110', f.color]">
                        <component :is="f.icon" class="size-6" />
                    </div>
                    <h3 class="text-sm font-bold text-foreground">{{ f.title }}</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted-foreground">
                        {{ f.description }}
                    </p>
                </div>
            </div>

            <!-- Security & Role Badge Strip -->
            <div class="mt-16 rounded-2xl border border-border/70 bg-card/60 p-6 backdrop-blur-md sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-500">
                        <ShieldCheck class="size-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-foreground">Multi-Level Role Access Control</h4>
                        <p class="text-[11px] text-muted-foreground">Tersedia 3 tingkat akses wewenang: Supervisor (SPV), Admin Gudang, dan Teknisi Lapangan.</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 sm:mt-0">
                    <span class="rounded-full bg-purple-500/15 px-2.5 py-1 text-[10px] font-bold text-purple-600 dark:text-purple-300">SPV</span>
                    <span class="rounded-full bg-[#fb4f00]/15 px-2.5 py-1 text-[10px] font-bold text-[#fb4f00]">Admin Gudang</span>
                    <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-300">Teknisi</span>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/80 py-8 text-center text-xs text-muted-foreground">
            <p>&copy; {{ new Date().getFullYear() }} BLiP Nusra · PT Bali Lintas Prestasi. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </div>
</template>