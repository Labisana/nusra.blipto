<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, Search, X } from '@lucide/vue';
import { ref, computed } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Stock', href: '/stok' },
        ],
    },
});

type StockRow = {
    sku: string;
    name: string;
    type: 'Serial' | 'Non-Serial';
    in: number;
    out: number;
    remaining: number;
    unit: string;
    location: string;
};

const { role } = useRole();
const canManageStock = computed(() => role.value === 'spv' || role.value === 'admin');
const showForm = ref(false);
const formMode = ref<'add' | 'edit'>('add');

const stocks = ref<StockRow[]>([
    { sku: 'RT-MIK-RB750', name: 'Router Mikrotik RB750', type: 'Serial', in: 20, out: 12, remaining: 8, unit: 'unit', location: 'Rak A1' },
    { sku: 'ONT-ZTE-F609', name: 'ONU/ONT ZTE F609', type: 'Serial', in: 30, out: 18, remaining: 12, unit: 'unit', location: 'Rak A2' },
    { sku: 'CBL-UTP-C6', name: 'Kabel UTP Cat6', type: 'Non-Serial', in: 100, out: 55, remaining: 45, unit: 'box', location: 'Rak B1' },
    { sku: 'ACC-RJ45', name: 'Konektor RJ45', type: 'Non-Serial', in: 200, out: 182, remaining: 18, unit: 'pack', location: 'Rak B2' },
    { sku: 'SFP-1G', name: 'SFP Module 1G', type: 'Non-Serial', in: 15, out: 12, remaining: 3, unit: 'pcs', location: 'Rak C1' },
]);

const serials = [
    { sn: 'RB750-003', item: 'Router Mikrotik RB750', status: 'Tersedia', inDate: '2026-04-10', location: 'Rak A1' },
    { sn: 'RB750-004', item: 'Router Mikrotik RB750', status: 'Tersedia', inDate: '2026-04-10', location: 'Rak A1' },
    { sn: 'F609-A05', item: 'ONU/ONT ZTE F609', status: 'Tersedia', inDate: '2026-04-09', location: 'Rak A2' },
    { sn: 'F609-A01', item: 'ONU/ONT ZTE F609', status: 'Keluar', inDate: '2026-04-09', location: 'PT Nusra Net - Cab. Praya' },
];

const form = ref<StockRow>({
    sku: '',
    name: '',
    type: 'Non-Serial',
    in: 0,
    out: 0,
    remaining: 0,
    unit: 'unit',
    location: '',
});

function openAdd() {
    if (!canManageStock.value) return;
    formMode.value = 'add';
    form.value = { sku: '', name: '', type: 'Non-Serial', in: 0, out: 0, remaining: 0, unit: 'unit', location: '' };
    showForm.value = true;
}

function openEdit(row: StockRow) {
    if (!canManageStock.value) return;
    formMode.value = 'edit';
    form.value = { ...row };
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

function submit() {
    showForm.value = false;
}
</script>

<template>
    <Head title="Stock" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Stock</h1>
                <p class="text-sm text-muted-foreground">Ringkasan stock per barang dan pelacakan serial number.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <label class="flex items-center gap-2 text-sm">
                    <span class="text-muted-foreground">Simulasi peran</span>
                    <select v-model="role" class="rounded-md border bg-background px-2 py-1.5 text-sm">
                        <option value="spv">SPV (Full)</option>
                        <option value="admin">Admin (Add/Edit)</option>
                        <option value="teknisi">Teknisi (Lihat saja)</option>
                    </select>
                </label>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    :disabled="!canManageStock"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Stock
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <div class="rounded-lg border bg-card px-3 py-2 text-xs"><span class="text-muted-foreground">Total SKU:</span> <span class="font-semibold">5</span></div>
            <div class="rounded-lg border bg-card px-3 py-2 text-xs"><span class="text-muted-foreground">Stock menipis:</span> <span class="font-semibold text-amber-600">2</span></div>
            <div v-if="!canManageStock" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                Peran Teknisi hanya dapat melihat stock. Penambahan & edit stock hanya untuk Admin dan SPV.
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium">Stock per Barang</h2>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <select class="rounded-md border bg-background px-3 py-2 text-sm">
                        <option>Semua jenis</option>
                        <option>Serial Number</option>
                        <option>Non-Serial</option>
                    </select>
                    <div class="relative sm:w-64">
                        <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                        <input type="text" placeholder="Cari SKU / nama barang..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">SKU</th>
                            <th class="px-4 py-2 font-medium">Nama Barang</th>
                            <th class="px-4 py-2 font-medium">Jenis</th>
                            <th class="px-4 py-2 font-medium text-right">Masuk</th>
                            <th class="px-4 py-2 font-medium text-right">Keluar</th>
                            <th class="px-4 py-2 font-medium text-right">Sisa</th>
                            <th class="px-4 py-2 font-medium">Satuan</th>
                            <th class="px-4 py-2 font-medium">Lokasi</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="s in stocks" :key="s.sku" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs whitespace-nowrap">{{ s.sku }}</td>
                            <td class="px-4 py-2 min-w-[180px] font-medium">{{ s.name }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ s.type }}</span></td>
                            <td class="px-4 py-2 text-right text-emerald-600">{{ s.in }}</td>
                            <td class="px-4 py-2 text-right text-amber-600">{{ s.out }}</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ s.remaining }}</td>
                            <td class="px-4 py-2">{{ s.unit }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ s.location }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="s.remaining <= 5 ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'"
                                >
                                    {{ s.remaining <= 5 ? 'Menipis' : 'Aman' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40"
                                    :disabled="!canManageStock"
                                    @click="openEdit(s)"
                                >
                                    <Pencil class="size-3.5" /> Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="border-b p-4">
                <h2 class="font-medium">Pelacakan Serial Number</h2>
                <p class="text-xs text-muted-foreground">Detail unit berserial: status tersedia/keluar dan lokasi/pelanggan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">Serial Number</th>
                            <th class="px-4 py-2 font-medium">Barang</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium">Tgl Masuk</th>
                            <th class="px-4 py-2 font-medium">Lokasi / Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="sn in serials" :key="sn.sn" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs font-medium">{{ sn.sn }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ sn.item }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="sn.status === 'Tersedia' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300'"
                                >
                                    {{ sn.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ sn.inDate }}</td>
                            <td class="px-4 py-2 min-w-[200px]">{{ sn.location }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ formMode === 'add' ? 'Tambah Stock' : 'Edit Stock' }}</h2>
                        <p class="text-xs text-muted-foreground">Hanya Admin dan SPV yang dapat menambah atau mengubah data stock.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">SKU <span class="text-destructive">*</span></label>
                                <input v-model="form.sku" type="text" required placeholder="Kode SKU" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Jenis <span class="text-destructive">*</span></label>
                                <select v-model="form.type" class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="Serial">Serial Number</option>
                                    <option value="Non-Serial">Non-Serial</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama Barang <span class="text-destructive">*</span></label>
                            <input v-model="form.name" type="text" required placeholder="Nama barang" class="rounded-md border bg-background px-3 py-2 text-sm" />
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Total Masuk</label>
                                <input v-model.number="form.in" type="number" min="0" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Total Keluar</label>
                                <input v-model.number="form.out" type="number" min="0" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Sisa <span class="text-destructive">*</span></label>
                                <input v-model.number="form.remaining" type="number" min="0" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Satuan</label>
                                <input v-model="form.unit" type="text" placeholder="unit / pcs / box" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Lokasi</label>
                                <input v-model="form.location" type="text" placeholder="Rak / Gudang" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div v-if="form.type === 'Serial'" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                            Barang berserial: pengelolaan detail serial number dapat dilakukan melalui transaksi barang masuk/keluar dan pelacakan SN.
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
                            {{ formMode === 'add' ? 'Simpan Stock' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
