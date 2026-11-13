<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download, Filter } from '@lucide/vue';
import { ref } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Laporan', href: '/laporan' },
        ],
    },
});

const { role, isTeknisi } = useRole();
const tab = ref<'masuk' | 'keluar' | 'stok'>('masuk');

const incoming = [
    { date: '2026-04-10', ref: 'IN-2401', activity: 'FPB', item: 'Router Mikrotik RB750', type: 'Serial', qty: 5, customer: '-', tipePelanggan: '-', user: 'Admin' },
    { date: '2026-04-10', ref: 'IN-2402', activity: 'FPB', item: 'Kabel UTP Cat6', type: 'Non-Serial', qty: 20, customer: '-', tipePelanggan: '-', user: 'Teknisi Budi' },
    { date: '2026-04-09', ref: 'IN-2398', activity: 'Dismantle', item: 'ONU/ONT ZTE F609', type: 'Serial', qty: 12, customer: 'PT Nusra Net - Cab. Praya', tipePelanggan: 'Broadband', user: 'Admin' },
];
const outgoing = [
    { date: '2026-04-10', ref: 'OUT-1104', activity: 'Instalasi', item: 'Router Mikrotik RB750', type: 'Serial', qty: 2, customer: 'PT Nusra Net - Cab. Mataram', tipePelanggan: 'Busol', user: 'Teknisi Andi' },
    { date: '2026-04-09', ref: 'OUT-1103', activity: 'Maintenance', item: 'Patch Cord 3m', type: 'Non-Serial', qty: 15, customer: 'CV Sinar Lombok', tipePelanggan: 'Busol', user: 'Teknisi Budi' },
    { date: '2026-04-09', ref: 'OUT-1102', activity: 'Peminjaman', item: 'ONU/ONT ZTE F609', type: 'Serial', qty: 4, customer: 'PT Nusra Net - Cab. Praya', tipePelanggan: 'Broadband', user: 'Admin' },
];
const stock = [
    { sku: 'RT-MIK-RB750', item: 'Router Mikrotik RB750', type: 'Serial', in: 20, out: 12, remaining: 8 },
    { sku: 'ONT-ZTE-F609', item: 'ONU/ONT ZTE F609', type: 'Serial', in: 30, out: 18, remaining: 12 },
    { sku: 'CBL-UTP-C6', item: 'Kabel UTP Cat6', type: 'Non-Serial', in: 100, out: 55, remaining: 45 },
];
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
                <label class="flex items-center gap-2 text-sm">
                    <span class="text-muted-foreground">Simulasi peran</span>
                    <select v-model="role" class="rounded-md border bg-background px-2 py-1.5 text-sm">
                        <option value="spv">SPV (Full)</option>
                        <option value="admin">Admin (Akses laporan)</option>
                    </select>
                </label>
                <button class="inline-flex items-center justify-center gap-2 rounded-md border bg-background px-3 py-2 text-sm font-medium hover:bg-muted">
                    <Download class="size-4" /> Ekspor (CSV/PDF)
                </button>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="grid gap-1">
                        <label class="text-xs font-medium text-muted-foreground">Dari Tanggal</label>
                        <input type="date" value="2026-04-01" class="rounded-md border bg-background px-3 py-2 text-sm" />
                    </div>
                    <div class="grid gap-1">
                        <label class="text-xs font-medium text-muted-foreground">Sampai Tanggal</label>
                        <input type="date" value="2026-04-10" class="rounded-md border bg-background px-3 py-2 text-sm" />
                    </div>
                    <div class="grid gap-1">
                        <label class="text-xs font-medium text-muted-foreground">Jenis Barang</label>
                        <select class="rounded-md border bg-background px-3 py-2 text-sm">
                            <option>Semua</option>
                            <option>Serial</option>
                            <option>Non-Serial</option>
                        </select>
                    </div>
                    <div class="grid gap-1">
                        <label class="text-xs font-medium text-muted-foreground">Pelanggan</label>
                        <select class="rounded-md border bg-background px-3 py-2 text-sm">
                            <option>Semua</option>
                            <option>PT Nusra Net - Cab. Mataram</option>
                            <option>PT Nusra Net - Cab. Praya</option>
                            <option>CV Sinar Lombok</option>
                        </select>
                    </div>
                </div>
                <button class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
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
                            <th class="px-4 py-2">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="r in incoming" :key="r.ref" class="hover:bg-muted/30">
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                            <td class="px-4 py-2 font-medium">{{ r.ref }}</td>
                            <td class="px-4 py-2 whitespace-nowrap"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span></td>
                            <td class="px-4 py-2 min-w-[180px]">{{ r.customer }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.tipePelanggan }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                            <td class="px-4 py-2 text-right font-medium text-emerald-600">+{{ r.qty }}</td>
                            <td class="px-4 py-2">{{ r.user }}</td>
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
                            <th class="px-4 py-2">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="r in outgoing" :key="r.ref" class="hover:bg-muted/30">
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                            <td class="px-4 py-2 font-medium">{{ r.ref }}</td>
                            <td class="px-4 py-2 whitespace-nowrap"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span></td>
                            <td class="px-4 py-2 min-w-[200px]">{{ r.customer }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.tipePelanggan }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                            <td class="px-4 py-2 text-right font-medium text-amber-600">-{{ r.qty }}</td>
                            <td class="px-4 py-2">{{ r.user }}</td>
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
                        <tr v-for="r in stock" :key="r.sku" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs">{{ r.sku }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ r.item }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                            <td class="px-4 py-2 text-right text-emerald-600">{{ r.in }}</td>
                            <td class="px-4 py-2 text-right text-amber-600">{{ r.out }}</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ r.remaining }}</td>
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
