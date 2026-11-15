<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
    Package,
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
    };
    recentIncoming: ReportRow[];
    recentOutgoing: ReportRow[];
    lowStock: { item: string; stock: number; min: number; type: string }[];
}>();

const { roleLabel } = useRole();

const stats = computed(() => [
    {
        label: 'Barang Masuk (Bulan ini)',
        value: props.stats.incoming_this_month.toLocaleString('id-ID'),
        icon: ArrowDownToLine,
        tone: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10',
    },
    {
        label: 'Barang Keluar (Bulan ini)',
        value: props.stats.outgoing_this_month.toLocaleString('id-ID'),
        icon: ArrowUpFromLine,
        tone: 'text-amber-600 bg-amber-50 dark:bg-amber-500/10',
    },
    {
        label: 'Total Item Stock',
        value: props.stats.total_remaining.toLocaleString('id-ID'),
        icon: Boxes,
        tone: 'text-sky-600 bg-sky-50 dark:bg-sky-500/10',
    },
    {
        label: 'Pelanggan Aktif',
        value: props.stats.active_customers.toLocaleString('id-ID'),
        icon: Users,
        tone: 'text-violet-600 bg-violet-50 dark:bg-violet-500/10',
    },
]);
</script>

<template>
    <Head title="Dashboard Gudang" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Dashboard Gudang</h1>
                <p class="text-sm text-muted-foreground">
                    Ringkasan pergerakan barang, stok, dan pelanggan.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-lg border bg-card px-3 py-2 text-xs text-muted-foreground">
                <Package class="size-4" />
                Mode akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="s in stats"
                :key="s.label"
                class="rounded-xl border bg-card p-4 shadow-sm"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">{{ s.label }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ s.value }}</p>
                    </div>
                    <div :class="['rounded-lg p-2', s.tone]">
                        <component :is="s.icon" class="size-5" />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <div class="rounded-xl border bg-card shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h2 class="font-medium">Barang Masuk Terbaru</h2>
                    <span class="text-xs text-muted-foreground">{{ recentIncoming.length }} baris terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                            <tr>
                                <th class="px-4 py-2 font-medium">Tanggal</th>
                                <th class="px-4 py-2 font-medium">Barang</th>
                                <th class="px-4 py-2 font-medium">Jenis</th>
                                <th class="px-4 py-2 font-medium">Qty</th>
                                <th class="px-4 py-2 font-medium">Ref</th>
                                <th class="px-4 py-2 font-medium">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="r in recentIncoming" :key="r.id" class="hover:bg-muted/30">
                                <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                                <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span>
                                </td>
                                <td class="px-4 py-2 font-medium text-emerald-600">+{{ r.qty }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ r.ref }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ r.user }}</td>
                            </tr>
                            <tr v-if="!recentIncoming.length">
                                <td colspan="6" class="px-4 py-6 text-center text-muted-foreground">Belum ada transaksi barang masuk.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center gap-2 border-b px-4 py-3">
                    <TriangleAlert class="size-4 text-amber-500" />
                    <h2 class="font-medium">Stok Menipis</h2>
                </div>
                <ul class="divide-y">
                    <li v-for="l in lowStock" :key="l.item" class="px-4 py-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium">{{ l.item }}</p>
                                <p class="text-xs text-muted-foreground">{{ l.type }} · Min {{ l.min }}</p>
                            </div>
                            <span class="rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                {{ l.stock }}
                            </span>
                        </div>
                    </li>
                    <li v-if="!lowStock.length" class="px-4 py-6 text-center text-sm text-muted-foreground">
                        Semua stok di atas batas minimum.
                    </li>
                </ul>
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h2 class="font-medium">Barang Keluar Terbaru</h2>
                <span class="text-xs text-muted-foreground">Relasi ke pelanggan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Barang</th>
                            <th class="px-4 py-2 font-medium">Jenis</th>
                            <th class="px-4 py-2 font-medium">Qty</th>
                            <th class="px-4 py-2 font-medium">Ref</th>
                            <th class="px-4 py-2 font-medium">Pelanggan</th>
                            <th class="px-4 py-2 font-medium">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="r in recentOutgoing" :key="r.id" class="hover:bg-muted/30">
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span>
                            </td>
                            <td class="px-4 py-2 font-medium text-amber-600">-{{ r.qty }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.ref }}</td>
                            <td class="px-4 py-2 min-w-[220px]">{{ r.customer }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.user }}</td>
                        </tr>
                        <tr v-if="!recentOutgoing.length">
                            <td colspan="7" class="px-4 py-6 text-center text-muted-foreground">Belum ada transaksi barang keluar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
