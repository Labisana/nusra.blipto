<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Filter } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';
import type { CustomerOption, ReportRow, SelectOption, StockReportRow } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Laporan', href: '/laporan' },
        ],
    },
});

const props = defineProps<{
    incoming: ReportRow[];
    outgoing: ReportRow[];
    stockRows: StockReportRow[];
    customers: CustomerOption[];
    itemTypes: SelectOption[];
    filters: {
        from: string | null;
        to: string | null;
        item_type: string | null;
        customer_id: number | null;
    };
}>();

const { roleLabel, isTeknisi } = useRole();
const tab = ref<'masuk' | 'keluar' | 'stok'>('masuk');

const from = ref(props.filters.from ?? '');
const to = ref(props.filters.to ?? '');
const itemType = ref(props.filters.item_type ?? '');
const customerId = ref<number | ''>(props.filters.customer_id ?? '');

function applyFilters() {
    router.get(
        '/laporan',
        {
            from: from.value || undefined,
            to: to.value || undefined,
            item_type: itemType.value || undefined,
            customer_id: customerId.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

const exportUrl = computed(() => {
    const params = new URLSearchParams({ tab: tab.value });

    if (from.value) params.set('from', from.value);
    if (to.value) params.set('to', to.value);
    if (itemType.value) params.set('item_type', itemType.value);
    if (customerId.value) params.set('customer_id', String(customerId.value));

    return `/laporan/export?${params.toString()}`;
});
</script>

<template>
    <div>
        <Head title="Laporan Gudang" />

        <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
            <div v-if="isTeknisi" class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-6 text-amber-900 dark:text-amber-200">
                <h1 class="text-base font-bold">Akses Terbatas</h1>
                <p class="mt-1 text-xs">Modul Laporan hanya dapat diakses oleh peran Admin dan SPV.</p>
                <Link href="/dashboard" class="mt-4 inline-flex rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm">Kembali ke Dashboard</Link>
            </div>
            <template v-else>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Inventaris</h1>
                            <span class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]">
                                Rekapitulasi
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted-foreground">Laporan komprehensif mutasi barang masuk, barang keluar, dan kalkulasi saldo akhir stok.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="rounded-xl border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-2xs">
                            Akses: <span class="font-semibold text-foreground">{{ roleLabel }}</span>
                        </span>
                        <a
                            :href="exportUrl"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-3.5 py-2 text-xs font-semibold text-foreground shadow-2xs transition-all hover:border-[#fb4f00]/40 hover:text-[#fb4f00] active:scale-95"
                        >
                            <Download class="size-3.5 text-[#fb4f00]" /> Ekspor CSV
                        </a>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="rounded-2xl border border-border/80 bg-card p-4.5 shadow-2xs">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Dari Tanggal</label>
                                <input v-model="from" type="date" class="rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Sampai Tanggal</label>
                                <input v-model="to" type="date" class="rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Jenis Barang</label>
                                <select v-model="itemType" class="rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden">
                                    <option value="">Semua Jenis</option>
                                    <option v-for="option in itemTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Pelanggan</label>
                                <select v-model="customerId" class="rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden">
                                    <option value="">Semua Pelanggan</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.nama_pelanggan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95" @click="applyFilters">
                            <Filter class="size-3.5" /> Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Tabbed Data Presentation Card -->
                <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
                    <div class="flex flex-wrap items-center gap-1.5 border-b border-border/70 p-3 bg-muted/20">
                        <button
                            class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                            :class="tab === 'masuk' ? 'bg-[#fb4f00] text-white shadow-xs shadow-[#fb4f00]/30' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                            @click="tab = 'masuk'"
                        >Barang Masuk ({{ incoming.length }})</button>
                        <button
                            class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                            :class="tab === 'keluar' ? 'bg-[#fb4f00] text-white shadow-xs shadow-[#fb4f00]/30' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                            @click="tab = 'keluar'"
                        >Barang Keluar ({{ outgoing.length }})</button>
                        <button
                            class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                            :class="tab === 'stok' ? 'bg-[#fb4f00] text-white shadow-xs shadow-[#fb4f00]/30' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                            @click="tab = 'stok'"
                        >Saldo Stok Akhir ({{ stockRows.length }})</button>
                    </div>

                    <div v-if="tab === 'masuk'" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">No. Ref</th>
                                    <th class="px-4 py-3">Activity</th>
                                    <th class="px-4 py-3">Pelanggan</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3 text-right">Qty</th>
                                    <th class="px-4 py-3">Serial Number</th>
                                    <th class="px-4 py-3">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/50 text-xs">
                                <tr v-for="r in incoming" :key="r.id" class="transition-colors hover:bg-emerald-500/[0.02] dark:hover:bg-emerald-500/[0.05]">
                                    <td class="px-4 py-3 font-mono text-muted-foreground whitespace-nowrap">{{ r.date }}</td>
                                    <td class="px-4 py-3 font-mono font-semibold text-foreground whitespace-nowrap">{{ r.ref }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-bold">{{ r.activity }}</span></td>
                                    <td class="px-4 py-3 min-w-[180px] font-medium text-foreground">{{ r.customer }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">{{ r.tipe_pelanggan }}</td>
                                    <td class="px-4 py-3 min-w-[180px] font-semibold text-foreground">{{ r.item }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex rounded-full border border-border px-2 py-0.5 text-[10px] font-medium bg-background">{{ r.type }}</span></td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">+{{ r.qty }}</td>
                                    <td class="px-4 py-3 max-w-[200px] truncate font-mono text-[11px] text-muted-foreground" :title="r.serials">{{ r.serials }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground">
                                            {{ r.user }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!incoming.length">
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">Tidak ada data barang masuk pada filter ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else-if="tab === 'keluar'" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">No. Ref</th>
                                    <th class="px-4 py-3">Activity</th>
                                    <th class="px-4 py-3">Pelanggan</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3 text-right">Qty</th>
                                    <th class="px-4 py-3">Serial Number</th>
                                    <th class="px-4 py-3">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/50 text-xs">
                                <tr v-for="r in outgoing" :key="r.id" class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]">
                                    <td class="px-4 py-3 font-mono text-muted-foreground whitespace-nowrap">{{ r.date }}</td>
                                    <td class="px-4 py-3 font-mono font-semibold text-foreground whitespace-nowrap">{{ r.ref }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-bold">{{ r.activity }}</span></td>
                                    <td class="px-4 py-3 min-w-[200px] font-medium text-foreground">{{ r.customer }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">{{ r.tipe_pelanggan }}</td>
                                    <td class="px-4 py-3 min-w-[180px] font-semibold text-foreground">{{ r.item }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex rounded-full border border-border px-2 py-0.5 text-[10px] font-medium bg-background">{{ r.type }}</span></td>
                                    <td class="px-4 py-3 text-right font-bold text-[#fb4f00] whitespace-nowrap">-{{ r.qty }}</td>
                                    <td class="px-4 py-3 max-w-[200px] truncate font-mono text-[11px] text-muted-foreground" :title="r.serials">{{ r.serials }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground">
                                            {{ r.user }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!outgoing.length">
                                    <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">Tidak ada data barang keluar pada filter ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3 text-right">Total Masuk</th>
                                    <th class="px-4 py-3 text-right">Total Keluar</th>
                                    <th class="px-4 py-3 text-right">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/50 text-xs">
                                <tr v-for="r in stockRows" :key="r.id" class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]">
                                    <td class="px-4 py-3 font-mono font-medium text-muted-foreground whitespace-nowrap">{{ r.sku }}</td>
                                    <td class="px-4 py-3 min-w-[200px] font-bold text-foreground">{{ r.item }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap"><span class="inline-flex rounded-full border border-border px-2 py-0.5 text-[10px] font-medium bg-background">{{ r.type_label }}</span></td>
                                    <td class="px-4 py-3 text-right font-medium text-emerald-600 dark:text-emerald-400 whitespace-nowrap">+{{ r.in }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-[#fb4f00] whitespace-nowrap">-{{ r.out }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-foreground whitespace-nowrap text-sm">{{ r.remaining }}</td>
                                </tr>
                                <tr v-if="!stockRows.length">
                                    <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Tidak ada data stock pada periode ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-border/70 px-4 py-2.5 text-xs text-muted-foreground">
                        Laporan mencakup relasi barang masuk/keluar dengan pelanggan serta perhitungan sisa stok per periode.
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
