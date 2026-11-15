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
        <Head title="Laporan" />

        <div class="flex flex-1 flex-col gap-4 p-4">
            <div v-if="isTeknisi" class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                <h1 class="text-lg font-semibold">Akses ditolak</h1>
                <p class="mt-2 text-sm">Menu Laporan tidak tersedia untuk peran Teknisi.</p>
                <Link href="/dashboard" class="mt-4 inline-flex rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground">Kembali ke Dashboard</Link>
            </div>
            <template v-else>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight">Laporan Gudang</h1>
                        <p class="text-sm text-muted-foreground">Laporan barang masuk, barang keluar, dan sisa stok. Dapat difilter & diekspor.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-md border bg-card px-2.5 py-1.5 text-xs text-muted-foreground">
                            Akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
                        </span>
                        <a
                            :href="exportUrl"
                            class="inline-flex items-center justify-center gap-2 rounded-md border bg-background px-3 py-2 text-sm font-medium hover:bg-muted"
                        >
                            <Download class="size-4" /> Ekspor (CSV)
                        </a>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Dari Tanggal</label>
                                <input v-model="from" type="date" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Sampai Tanggal</label>
                                <input v-model="to" type="date" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Jenis Barang</label>
                                <select v-model="itemType" class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Semua</option>
                                    <option v-for="option in itemTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Pelanggan</label>
                                <select v-model="customerId" class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Semua</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.nama_pelanggan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90" @click="applyFilters">
                            <Filter class="size-4" /> Terapkan Filter
                        </button>
                    </div>
                </div>

                <div class="rounded-xl border bg-card shadow-sm">
                    <div class="flex flex-wrap gap-1 border-b p-2">
                        <button
                            class="rounded-md px-3 py-1.5 text-sm font-medium"
                            :class="tab === 'masuk' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                            @click="tab = 'masuk'"
                        >Barang Masuk</button>
                        <button
                            class="rounded-md px-3 py-1.5 text-sm font-medium"
                            :class="tab === 'keluar' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                            @click="tab = 'keluar'"
                        >Barang Keluar</button>
                        <button
                            class="rounded-md px-3 py-1.5 text-sm font-medium"
                            :class="tab === 'stok' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                            @click="tab = 'stok'"
                        >Stock</button>
                    </div>

                    <div v-if="tab === 'masuk'" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Ref</th>
                                    <th class="px-4 py-2">Jenis Transaksi</th>
                                    <th class="px-4 py-2">Pelanggan</th>
                                    <th class="px-4 py-2">Tipe Pelanggan</th>
                                    <th class="px-4 py-2">Barang</th>
                                    <th class="px-4 py-2">Jenis Barang</th>
                                    <th class="px-4 py-2 text-right">Qty</th>
                                    <th class="px-4 py-2">Serial</th>
                                    <th class="px-4 py-2">User</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="r in incoming" :key="r.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                                    <td class="px-4 py-2 font-medium">{{ r.ref }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span></td>
                                    <td class="px-4 py-2 min-w-[180px]">{{ r.customer }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ r.tipe_pelanggan }}</td>
                                    <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                                    <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                                    <td class="px-4 py-2 text-right font-medium text-emerald-600">+{{ r.qty }}</td>
                                    <td class="px-4 py-2 max-w-[200px] truncate" :title="r.serials">{{ r.serials }}</td>
                                    <td class="px-4 py-2">{{ r.user }}</td>
                                </tr>
                                <tr v-if="!incoming.length">
                                    <td colspan="10" class="px-4 py-6 text-center text-muted-foreground">Tidak ada data barang masuk pada filter ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else-if="tab === 'keluar'" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Ref</th>
                                    <th class="px-4 py-2">Jenis Transaksi</th>
                                    <th class="px-4 py-2">Pelanggan</th>
                                    <th class="px-4 py-2">Tipe Pelanggan</th>
                                    <th class="px-4 py-2">Barang</th>
                                    <th class="px-4 py-2">Jenis Barang</th>
                                    <th class="px-4 py-2 text-right">Qty</th>
                                    <th class="px-4 py-2">Serial</th>
                                    <th class="px-4 py-2">User</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="r in outgoing" :key="r.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                                    <td class="px-4 py-2 font-medium">{{ r.ref }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span></td>
                                    <td class="px-4 py-2 min-w-[200px]">{{ r.customer }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ r.tipe_pelanggan }}</td>
                                    <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                                    <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                                    <td class="px-4 py-2 text-right font-medium text-amber-600">-{{ r.qty }}</td>
                                    <td class="px-4 py-2 max-w-[200px] truncate" :title="r.serials">{{ r.serials }}</td>
                                    <td class="px-4 py-2">{{ r.user }}</td>
                                </tr>
                                <tr v-if="!outgoing.length">
                                    <td colspan="10" class="px-4 py-6 text-center text-muted-foreground">Tidak ada data barang keluar pada filter ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-2">SKU</th>
                                    <th class="px-4 py-2">Barang</th>
                                    <th class="px-4 py-2">Jenis</th>
                                    <th class="px-4 py-2 text-right">Total Masuk</th>
                                    <th class="px-4 py-2 text-right">Total Keluar</th>
                                    <th class="px-4 py-2 text-right">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="r in stockRows" :key="r.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-2 font-mono text-xs">{{ r.sku }}</td>
                                    <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                                    <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type_label }}</span></td>
                                    <td class="px-4 py-2 text-right text-emerald-600">{{ r.in }}</td>
                                    <td class="px-4 py-2 text-right text-amber-600">{{ r.out }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">{{ r.remaining }}</td>
                                </tr>
                                <tr v-if="!stockRows.length">
                                    <td colspan="6" class="px-4 py-6 text-center text-muted-foreground">Tidak ada data stock.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                        Laporan mencakup relasi barang masuk/keluar dengan pelanggan serta perhitungan sisa stok per periode.
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
