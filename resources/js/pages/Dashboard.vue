<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowRight,
    ArrowUpFromLine,
    Boxes,
    Clock,
    FileSpreadsheet,
    Package,
    Plus,
    RadioTower,
    Search,
    ShieldAlert,
    ShieldCheck,
    TriangleAlert,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import { useRole } from '@/composables/useRole';
import { dashboard } from '@/routes';
import type { ReportRow } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard Gudang',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps<{
    stats: {
        incoming_this_month: number;
        outgoing_this_month: number;
        total_remaining: number;
        active_customers: number;
        pending_approvals?: number;
    };
    recentIncoming: ReportRow[];
    recentOutgoing: ReportRow[];
    lowStock: { item: string; stock: number; min: number; type: string }[];
}>();

const { roleLabel, role, canViewReports, canManage } = useRole();

const currentDate = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(new Date());

const stats = computed(() => [
    {
        label: 'Barang Masuk (Bulan ini)',
        value: props.stats.incoming_this_month.toLocaleString('id-ID'),
        icon: ArrowDownToLine,
        color: 'emerald',
        badge: 'Inflow',
        href: '/barang-masuk',
        tone: 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
        gradient: 'from-emerald-500/10 to-transparent',
    },
    {
        label: 'Barang Keluar (Bulan ini)',
        value: props.stats.outgoing_this_month.toLocaleString('id-ID'),
        icon: ArrowUpFromLine,
        color: 'orange',
        badge: 'Outflow',
        href: '/barang-keluar',
        tone: 'text-[#fb4f00] bg-[#fb4f00]/10 border-[#fb4f00]/20',
        gradient: 'from-[#fb4f00]/10 to-transparent',
    },
    {
        label: 'Total Stock Tersedia',
        value: props.stats.total_remaining.toLocaleString('id-ID'),
        icon: Boxes,
        color: 'blue',
        badge: 'Gudang',
        href: '/stok',
        tone: 'text-sky-500 bg-sky-500/10 border-sky-500/20',
        gradient: 'from-sky-500/10 to-transparent',
    },
    {
        label: 'Pelanggan Aktif',
        value: props.stats.active_customers.toLocaleString('id-ID'),
        icon: Users,
        color: 'violet',
        badge: 'Relasi',
        href: '/pelanggan',
        tone: 'text-purple-500 bg-purple-500/10 border-purple-500/20',
        gradient: 'from-purple-500/10 to-transparent',
    },
]);
</script>

<template>
    <Head title="Dashboard Gudang" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Hero Banner with BLiP Accent -->
        <div
            class="relative overflow-hidden rounded-2xl border border-border/70 bg-card p-6 shadow-sm"
        >
            <!-- Decorative BLiP Ambient Glow -->
            <div
                class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full bg-gradient-to-br from-[#fb4f00]/20 to-transparent blur-3xl dark:from-[#fb4f00]/25"
            ></div>
            <div
                class="pointer-events-none absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-[#fb4f00] via-orange-400 to-transparent"
            ></div>

            <div
                class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-[#fb4f00]/30 bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]"
                        >
                            <span
                                class="size-1.5 animate-pulse rounded-full bg-[#fb4f00]"
                            ></span>
                            BLiP Nusra Warehouse
                        </span>
                        <span class="text-xs text-muted-foreground">{{
                            currentDate
                        }}</span>
                    </div>
                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-foreground md:text-3xl"
                    >
                        Dashboard Inventaris & Logistik
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Kelola lalu lintas perangkat jaringan, stock gudang,
                        pelanggan, dan site BTS BLiP.
                    </p>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <Link
                        href="/barang-masuk"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:brightness-105 active:scale-95"
                    >
                        <ArrowDownToLine class="size-4" />
                        <span>Barang Masuk</span>
                    </Link>
                    <Link
                        href="/barang-keluar"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/20 transition-all hover:shadow-[#fb4f00]/30 hover:brightness-105 active:scale-95"
                    >
                        <ArrowUpFromLine class="size-4" />
                        <span>Barang Keluar</span>
                    </Link>
                    <Link
                        href="/stok"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3.5 py-2 text-xs font-semibold text-foreground transition-all hover:border-[#fb4f00]/40 hover:bg-[#fb4f00]/5 active:scale-95"
                    >
                        <Boxes class="size-4 text-[#fb4f00]" />
                        <span>Stok Barang</span>
                    </Link>
                    <Link
                        v-if="canViewReports"
                        href="/laporan"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3.5 py-2 text-xs font-semibold text-muted-foreground transition-all hover:border-foreground/30 hover:text-foreground active:scale-95"
                    >
                        <FileSpreadsheet class="size-4" />
                        <span>Laporan</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pending Approval Alert for SPV / Admin -->
        <div
            v-if="canManage && (props.stats.pending_approvals || 0) > 0"
            class="flex flex-col items-start justify-between gap-3 rounded-2xl border border-amber-500/40 bg-amber-500/10 p-4 shadow-xs sm:flex-row sm:items-center"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400"
                >
                    <Clock class="size-5" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-foreground">
                        {{ props.stats.pending_approvals }} Permintaan Transaksi
                        Menunggu Persetujuan
                    </h3>
                    <p class="text-xs text-muted-foreground">
                        Inputan dari tim teknis memerlukan verifikasi &
                        persetujuan SPV sebelum memutasi stok gudang.
                    </p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2 self-end sm:self-auto">
                <Link
                    href="/persetujuan"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-500 via-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-bold text-white shadow-xs transition-all hover:brightness-105 active:scale-95"
                >
                    <ShieldCheck class="size-4" />
                    <span
                        >Pusat Persetujuan ({{
                            props.stats.pending_approvals
                        }})</span
                    >
                </Link>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="s in stats"
                :key="s.label"
                :href="s.href"
                class="group relative block overflow-hidden rounded-2xl border border-border/80 bg-card p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-[#fb4f00]/30 hover:shadow-md active:scale-95"
            >
                <div
                    :class="[
                        'pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r',
                        s.gradient,
                    ]"
                ></div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-medium text-muted-foreground"
                                >{{ s.label }}</span
                            >
                        </div>
                        <p
                            class="mt-2 text-3xl font-bold tracking-tight text-foreground"
                        >
                            {{ s.value }}
                        </p>
                    </div>
                    <div
                        :class="[
                            'rounded-xl border p-2.5 transition-transform duration-200 group-hover:scale-110',
                            s.tone,
                        ]"
                    >
                        <component :is="s.icon" class="size-5" />
                    </div>
                </div>
                <div
                    class="mt-4 flex items-center justify-between border-t border-border/40 pt-2.5 text-[11px] text-muted-foreground"
                >
                    <span class="rounded bg-muted px-1.5 py-0.5 font-medium">{{
                        s.badge
                    }}</span>
                    <span
                        class="text-xs font-medium text-[#fb4f00] opacity-100 transition-opacity sm:opacity-0 sm:group-hover:opacity-100"
                        >&rarr; Buka Halaman</span
                    >
                </div>
            </Link>
        </div>

        <!-- 1. Barang Masuk Terbaru (Full-Width Card) -->
        <div
            class="w-full min-w-0 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-xs transition-all hover:border-border"
        >
            <div
                class="flex items-center justify-between border-b border-border/70 px-4 py-3.5 sm:px-5 sm:py-4"
            >
                <div class="flex min-w-0 items-center gap-2.5">
                    <div
                        class="shrink-0 rounded-lg bg-emerald-500/10 p-1.5 text-emerald-600 dark:text-emerald-400"
                    >
                        <ArrowDownToLine class="size-4" />
                    </div>
                    <div class="min-w-0">
                        <h2
                            class="truncate text-sm font-semibold text-foreground"
                        >
                            Barang Masuk Terbaru
                        </h2>
                        <p class="truncate text-xs text-muted-foreground">
                            Penerimaan stok perangkat & komponen
                        </p>
                    </div>
                </div>
                <Link
                    href="/barang-masuk"
                    class="group ml-2 inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-[#fb4f00] hover:underline"
                >
                    <span>Lihat Semua</span>
                    <ArrowRight
                        class="size-3.5 transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">Tanggal</th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                Perangkat / Barang
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">Tipe</th>
                            <th class="px-5 py-3 text-right whitespace-nowrap">
                                Jumlah
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                No. Referensi
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="r in recentIncoming"
                            :key="r.id"
                            class="transition-colors hover:bg-[#fb4f00]/[0.03] dark:hover:bg-[#fb4f00]/[0.05]"
                        >
                            <td
                                class="px-5 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.date }}
                            </td>
                            <td
                                class="min-w-[200px] px-5 py-3 font-medium whitespace-nowrap text-foreground"
                            >
                                {{ r.item }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border/80 bg-background px-2 py-0.5 text-[10px] font-medium"
                                >
                                    {{ r.type }}
                                </span>
                            </td>
                            <td
                                class="px-5 py-3 text-right font-bold whitespace-nowrap text-emerald-600 dark:text-emerald-400"
                            >
                                +{{ r.qty }}
                            </td>
                            <td
                                class="px-5 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.ref }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground"
                                >
                                    {{ r.user }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!recentIncoming.length">
                            <td
                                colspan="6"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                <Package
                                    class="mx-auto mb-2 size-8 text-muted-foreground/40"
                                />
                                Belum ada transaksi barang masuk.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Interactive Cards View -->
            <div class="block divide-y divide-border/60 md:hidden">
                <div
                    v-for="r in recentIncoming"
                    :key="r.id"
                    class="space-y-2.5 p-4 transition-colors active:bg-muted/30"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-xs font-bold break-words text-foreground"
                            >
                                {{ r.item }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-muted-foreground">
                                {{ r.date }} · Ref:
                                <span class="font-mono font-medium">{{
                                    r.ref
                                }}</span>
                            </p>
                        </div>
                        <span
                            class="inline-flex shrink-0 rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-extrabold text-emerald-600 dark:text-emerald-400"
                        >
                            +{{ r.qty }}
                        </span>
                    </div>
                    <div
                        class="flex items-center justify-between pt-0.5 text-[11px] text-muted-foreground"
                    >
                        <span
                            class="inline-flex rounded-full border border-border/80 bg-background px-2 py-0.5 text-[10px] font-medium"
                        >
                            {{ r.type }}
                        </span>
                        <span>Petugas: {{ r.user }}</span>
                    </div>
                </div>
                <div
                    v-if="!recentIncoming.length"
                    class="p-6 text-center text-xs text-muted-foreground"
                >
                    <Package
                        class="mx-auto mb-2 size-7 text-muted-foreground/40"
                    />
                    Belum ada transaksi barang masuk.
                </div>
            </div>
        </div>

        <!-- 2. Peringatan Stok Menipis (Full-Width Card matching Barang Keluar) -->
        <div
            class="w-full min-w-0 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-xs transition-all hover:border-border"
        >
            <div
                class="flex items-center justify-between border-b border-border/70 px-4 py-3.5 sm:px-5 sm:py-4"
            >
                <div class="flex min-w-0 items-center gap-2.5">
                    <div
                        class="shrink-0 rounded-lg bg-amber-500/10 p-1.5 text-amber-500"
                    >
                        <TriangleAlert class="size-4" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h2
                                class="truncate text-sm font-semibold text-foreground"
                            >
                                Peringatan Stok Menipis
                            </h2>
                            <span
                                v-if="lowStock.length"
                                class="shrink-0 rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold text-amber-600 dark:text-amber-400"
                            >
                                {{ lowStock.length }} Item
                            </span>
                        </div>
                        <p class="truncate text-xs text-muted-foreground">
                            Item dengan kuota mendekati atau di bawah batas
                            minimum
                        </p>
                    </div>
                </div>
                <Link
                    href="/stok"
                    class="group ml-2 inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400"
                >
                    <span>Lihat Stok</span>
                    <ArrowRight
                        class="size-3.5 transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                Nama Barang / Perangkat
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">Jenis</th>
                            <th class="px-5 py-3 text-right whitespace-nowrap">
                                Sisa Stok
                            </th>
                            <th class="px-5 py-3 text-right whitespace-nowrap">
                                Batas Minimum
                            </th>
                            <th class="px-5 py-3 text-center whitespace-nowrap">
                                Status
                            </th>
                            <th class="px-5 py-3 text-right whitespace-nowrap">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="l in lowStock"
                            :key="l.item"
                            class="transition-colors hover:bg-amber-500/[0.04]"
                        >
                            <td
                                class="min-w-[200px] px-5 py-3 font-semibold whitespace-nowrap text-foreground"
                            >
                                {{ l.item }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                >
                                    {{ l.type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-lg bg-amber-500/20 px-2.5 py-1 text-xs font-extrabold text-amber-600 dark:text-amber-400"
                                >
                                    Sisa {{ l.stock }}
                                </span>
                            </td>
                            <td
                                class="px-5 py-3 text-right font-medium whitespace-nowrap text-muted-foreground"
                            >
                                {{ l.min }} unit
                            </td>
                            <td class="px-5 py-3 text-center whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold text-amber-600 dark:text-amber-400"
                                >
                                    Menipis
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <Link
                                    href="/barang-masuk"
                                    class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2.5 py-1 text-xs font-semibold text-[#fb4f00] transition-colors hover:border-[#fb4f00] hover:bg-[#fb4f00]/5 active:scale-95"
                                >
                                    + Restok
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!lowStock.length">
                            <td
                                colspan="6"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                <Package
                                    class="mx-auto mb-2 size-8 text-emerald-500/50"
                                />
                                Semua stok gudang aman (berada di atas kuota
                                minimum).
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Interactive Cards View -->
            <div class="block divide-y divide-border/60 md:hidden">
                <div
                    v-for="l in lowStock"
                    :key="l.item"
                    class="space-y-3 p-4 transition-colors active:bg-amber-500/[0.04]"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="inline-flex rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-[10px] font-bold text-amber-600 dark:text-amber-400"
                                >
                                    Menipis
                                </span>
                                <span
                                    class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                >
                                    {{ l.type }}
                                </span>
                            </div>
                            <p
                                class="mt-1 text-xs font-bold break-words text-foreground"
                            >
                                {{ l.item }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span
                                class="inline-flex rounded-lg bg-amber-500/20 px-2.5 py-1 text-xs font-extrabold text-amber-600 dark:text-amber-400"
                            >
                                Sisa {{ l.stock }}
                            </span>
                            <p class="mt-0.5 text-[10px] text-muted-foreground">
                                Min: {{ l.min }} unit
                            </p>
                        </div>
                    </div>

                    <!-- Progress bar (Visual stock vs min) -->
                    <div class="space-y-1">
                        <div
                            class="flex justify-between text-[10px] font-medium text-muted-foreground"
                        >
                            <span>Ketersediaan Stok</span>
                            <span
                                class="font-semibold text-amber-600 dark:text-amber-400"
                                >{{ l.stock }} / {{ l.min }} unit</span
                            >
                        </div>
                        <div
                            class="h-1.5 w-full overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full rounded-full bg-amber-500 transition-all"
                                :style="{
                                    width: `${Math.min(100, Math.max(8, (l.stock / Math.max(1, l.min)) * 100))}%`,
                                }"
                            ></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-1">
                        <Link
                            href="/barang-masuk"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] py-2 text-xs font-bold text-white shadow-xs transition-all hover:brightness-105 active:scale-95"
                        >
                            <Plus class="size-3.5" />
                            <span>Restok Barang Ini</span>
                        </Link>
                    </div>
                </div>
                <div
                    v-if="!lowStock.length"
                    class="p-6 text-center text-xs text-muted-foreground"
                >
                    <Package class="mx-auto mb-2 size-7 text-emerald-500/50" />
                    Semua stok gudang aman (berada di atas kuota minimum).
                </div>
            </div>
        </div>

        <!-- 3. Barang Keluar Terbaru (Full-Width Card) -->
        <div
            class="w-full min-w-0 overflow-hidden rounded-2xl border border-border/80 bg-card shadow-xs transition-all hover:border-border"
        >
            <div
                class="flex items-center justify-between border-b border-border/70 px-4 py-3.5 sm:px-5 sm:py-4"
            >
                <div class="flex min-w-0 items-center gap-2.5">
                    <div
                        class="shrink-0 rounded-lg bg-[#fb4f00]/10 p-1.5 text-[#fb4f00]"
                    >
                        <ArrowUpFromLine class="size-4" />
                    </div>
                    <div class="min-w-0">
                        <h2
                            class="truncate text-sm font-semibold text-foreground"
                        >
                            Barang Keluar Terbaru
                        </h2>
                        <p class="truncate text-xs text-muted-foreground">
                            Pengeluaran perangkat untuk instalasi & pelanggan
                        </p>
                    </div>
                </div>
                <Link
                    href="/barang-keluar"
                    class="group ml-2 inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-[#fb4f00] hover:underline"
                >
                    <span>Lihat Semua</span>
                    <ArrowRight
                        class="size-3.5 transition-transform group-hover:translate-x-0.5"
                    />
                </Link>
            </div>
            <!-- Desktop Table View -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">Tanggal</th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                Perangkat / Barang
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">Tipe</th>
                            <th class="px-5 py-3 text-right whitespace-nowrap">
                                Jumlah
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                No. Referensi
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                Pelanggan / Tujuan
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="r in recentOutgoing"
                            :key="r.id"
                            class="transition-colors hover:bg-[#fb4f00]/[0.03] dark:hover:bg-[#fb4f00]/[0.05]"
                        >
                            <td
                                class="px-5 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.date }}
                            </td>
                            <td
                                class="min-w-[200px] px-5 py-3 font-medium whitespace-nowrap text-foreground"
                            >
                                {{ r.item }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border/80 bg-background px-2 py-0.5 text-[10px] font-medium"
                                >
                                    {{ r.type }}
                                </span>
                            </td>
                            <td
                                class="px-5 py-3 text-right font-bold whitespace-nowrap text-[#fb4f00]"
                            >
                                -{{ r.qty }}
                            </td>
                            <td
                                class="px-5 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.ref }}
                            </td>
                            <td
                                class="min-w-[220px] px-5 py-3 font-medium whitespace-nowrap text-foreground"
                            >
                                {{ r.customer }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground"
                                >
                                    {{ r.user }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!recentOutgoing.length">
                            <td
                                colspan="7"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                <Package
                                    class="mx-auto mb-2 size-8 text-muted-foreground/40"
                                />
                                Belum ada transaksi barang keluar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Interactive Cards View -->
            <div class="block divide-y divide-border/60 md:hidden">
                <div
                    v-for="r in recentOutgoing"
                    :key="r.id"
                    class="space-y-2.5 p-4 transition-colors active:bg-muted/30"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-xs font-bold break-words text-foreground"
                            >
                                {{ r.item }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-muted-foreground">
                                {{ r.date }} · Ref:
                                <span class="font-mono font-medium">{{
                                    r.ref
                                }}</span>
                            </p>
                        </div>
                        <span
                            class="inline-flex shrink-0 rounded-full bg-[#fb4f00]/15 px-2.5 py-1 text-xs font-extrabold text-[#fb4f00]"
                        >
                            -{{ r.qty }}
                        </span>
                    </div>
                    <div
                        class="flex items-center justify-between pt-0.5 text-[11px] text-muted-foreground"
                    >
                        <span
                            class="max-w-[65%] truncate font-medium text-foreground"
                            >🎯 {{ r.customer }}</span
                        >
                        <span>Petugas: {{ r.user }}</span>
                    </div>
                </div>
                <div
                    v-if="!recentOutgoing.length"
                    class="p-6 text-center text-xs text-muted-foreground"
                >
                    <Package
                        class="mx-auto mb-2 size-7 text-muted-foreground/40"
                    />
                    Belum ada transaksi barang keluar.
                </div>
            </div>
        </div>
    </div>
</template>
