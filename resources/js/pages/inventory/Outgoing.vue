<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus, Search, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Barang Keluar', href: '/barang-keluar' },
        ],
    },
});

const { role, canManage } = useRole();
const showForm = ref(false);

const customers = [
    { id: '1', name: 'PT Nusra Net - Cab. Mataram (CID: C-001)' },
    { id: '2', name: 'PT Nusra Net - Cab. Praya (CID: C-002)' },
    { id: '3', name: 'CV Sinar Lombok (CID: C-003)' },
];

const itemOptions = [
    { id: '1', name: 'Router Mikrotik RB750', type: 'serial' as const, stock: 8 },
    { id: '2', name: 'ONU/ONT ZTE F609', type: 'serial' as const, stock: 12 },
    { id: '3', name: 'Patch Cord 3m', type: 'non-serial' as const, stock: 40 },
    { id: '4', name: 'Kabel UTP Cat6', type: 'non-serial' as const, stock: 45 },
];

const availableSerials: Record<string, string[]> = {
    '1': ['RB750-003', 'RB750-004', 'RB750-005', 'RB750-006', 'RB750-007', 'RB750-008', 'RB750-009', 'RB750-010'],
    '2': ['F609-A05', 'F609-A06', 'F609-A07', 'F609-A08', 'F609-A09', 'F609-A10', 'F609-A11', 'F609-A12'],
};

const activities = ['Instalasi', 'Maintenance', 'Peminjaman'] as const;

type LineItem = {
    itemId: string;
    itemType: 'serial' | 'non-serial';
    qty: number;
    selectedSerials: string[];
};

function createLine(): LineItem {
    return { itemId: '', itemType: 'serial', qty: 1, selectedSerials: [] };
}

const form = ref({
    customerId: '',
    date: '2026-04-10',
    activity: '' as '' | (typeof activities)[number],
    ref: '',
    note: '',
    items: [createLine()] as LineItem[],
});

const rows = [
    { date: '2026-04-10', ref: 'OUT-1104', item: 'Router Mikrotik RB750', type: 'Serial', qty: 2, serials: 'RB750-001, RB750-002', customer: 'PT Nusra Net - Cab. Mataram', activity: 'Instalasi', user: 'Teknisi Andi' },
    { date: '2026-04-09', ref: 'OUT-1103', item: 'Patch Cord 3m', type: 'Non-Serial', qty: 15, serials: '-', customer: 'CV Sinar Lombok', activity: 'Maintenance', user: 'Teknisi Budi' },
    { date: '2026-04-09', ref: 'OUT-1102', item: 'ONU/ONT ZTE F609', type: 'Serial', qty: 4, serials: 'F609-A01…A04', customer: 'PT Nusra Net - Cab. Praya', activity: 'Peminjaman', user: 'Admin' },
];

function openForm() {
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

function addItem() {
    form.value.items.push(createLine());
}

function removeItem(index: number) {
    if (form.value.items.length === 1) return;
    form.value.items.splice(index, 1);
}

function serialsFor(itemId: string): string[] {
    return availableSerials[itemId] ?? [];
}

function onItemChange(line: LineItem) {
    const opt = itemOptions.find((o) => o.id === line.itemId);
    line.itemType = opt?.type ?? 'serial';
    line.selectedSerials = [];
    line.qty = line.itemType === 'serial' ? 0 : 1;
}

function toggleSerial(line: LineItem, sn: string, checked: boolean) {
    if (checked) {
        if (!line.selectedSerials.includes(sn)) line.selectedSerials.push(sn);
    } else {
        line.selectedSerials = line.selectedSerials.filter((s) => s !== sn);
    }
    line.qty = line.selectedSerials.length;
}

function submit() {
    showForm.value = false;
}
</script>

<template>
    <Head title="Barang Keluar" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Barang Keluar</h1>
                <p class="text-sm text-muted-foreground">Catat pengeluaran barang ke pelanggan. Satu transaksi dapat berisi lebih dari satu barang.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <label class="flex items-center gap-2 text-sm">
                    <span class="text-muted-foreground">Simulasi peran</span>
                    <select v-model="role" class="rounded-md border bg-background px-2 py-1.5 text-sm">
                        <option value="spv">SPV (Full)</option>
                        <option value="admin">Admin (Add/Edit/Delete)</option>
                        <option value="teknisi">Teknisi (Input saja)</option>
                    </select>
                </label>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90"
                    @click="openForm"
                >
                    <Plus class="size-4" /> Input Barang Keluar
                </button>
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium">Riwayat Barang Keluar</h2>
                <div class="relative w-full sm:w-64">
                    <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                    <input type="text" placeholder="Cari ref / barang / pelanggan..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Ref</th>
                            <th class="px-4 py-2 font-medium">Pelanggan</th>
                            <th class="px-4 py-2 font-medium">Activity</th>
                            <th class="px-4 py-2 font-medium">Barang</th>
                            <th class="px-4 py-2 font-medium">Jenis</th>
                            <th class="px-4 py-2 font-medium">Qty</th>
                            <th class="px-4 py-2 font-medium">Serial</th>
                            <th class="px-4 py-2 font-medium">User</th>
                            <th class="px-4 py-2 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="r in rows" :key="r.ref" class="hover:bg-muted/30">
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.date }}</td>
                            <td class="px-4 py-2 whitespace-nowrap font-medium">{{ r.ref }}</td>
                            <td class="px-4 py-2 min-w-[200px]">{{ r.customer }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span>
                            </td>
                            <td class="px-4 py-2 min-w-[160px]">{{ r.item }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                            <td class="px-4 py-2 text-amber-600 font-medium">-{{ r.qty }}</td>
                            <td class="px-4 py-2 max-w-[160px] truncate" :title="r.serials">{{ r.serials }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ r.user }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button class="rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40" :disabled="!canManage">Edit</button>
                                <button class="rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40" :disabled="!canManage">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Menampilkan 3 dari 94 transaksi. Barang keluar terhubung ke pelanggan dan activity.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-2xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">Input Barang Keluar</h2>
                        <p class="text-xs text-muted-foreground">Pelanggan wajib dipilih. Pilih barang untuk memunculkan serial number yang tersedia.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama Pelanggan <span class="text-destructive">*</span></label>
                            <select v-model="form.customerId" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option value="">Pilih pelanggan...</option>
                                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Tanggal <span class="text-destructive">*</span></label>
                                <input v-model="form.date" type="date" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Activity <span class="text-destructive">*</span></label>
                                <select v-model="form.activity" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Pilih activity...</option>
                                    <option v-for="a in activities" :key="a" :value="a">{{ a }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid gap-1 sm:grid-cols-2 sm:gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">No. Referensi (DO)</label>
                                <input v-model="form.ref" type="text" placeholder="OUT-xxxx" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Catatan</label>
                                <input v-model="form.note" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-1 flex items-center justify-between border-t pt-3">
                            <div>
                                <h3 class="text-sm font-medium">Daftar Barang Keluar</h3>
                                <p class="text-[11px] text-muted-foreground">Pilih jenis/barang untuk memunculkan daftar SN yang tersedia (barang serial).</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-1 rounded-md border bg-background px-2.5 py-1.5 text-xs font-medium hover:bg-muted" @click="addItem">
                                <Plus class="size-3.5" /> Tambah Barang
                            </button>
                        </div>

                        <div class="grid gap-3">
                            <div v-for="(line, idx) in form.items" :key="idx" class="rounded-lg border bg-muted/20 p-3">
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-xs font-medium text-muted-foreground">Barang #{{ idx + 1 }}</span>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40"
                                        :disabled="form.items.length === 1"
                                        @click="removeItem(idx)"
                                    >
                                        <Trash2 class="size-3.5" /> Hapus
                                    </button>
                                </div>
                                <div class="grid gap-3">
                                    <div class="grid gap-1">
                                        <label class="text-xs font-medium text-muted-foreground">Barang <span class="text-destructive">*</span></label>
                                        <select v-model="line.itemId" required class="rounded-md border bg-background px-3 py-2 text-sm" @change="onItemChange(line)">
                                            <option value="">Pilih barang...</option>
                                            <option v-for="opt in itemOptions" :key="opt.id" :value="opt.id">
                                                {{ opt.name }} ({{ opt.type === 'serial' ? 'Serial' : 'Non-Serial' }}, stok {{ opt.stock }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Jenis</label>
                                            <input
                                                :value="line.itemId ? (line.itemType === 'serial' ? 'Serial Number' : 'Non-Serial') : '-'"
                                                type="text"
                                                readonly
                                                class="rounded-md border bg-muted/40 px-3 py-2 text-sm"
                                            />
                                        </div>
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Qty <span class="text-destructive">*</span></label>
                                            <input
                                                v-model.number="line.qty"
                                                type="number"
                                                min="1"
                                                :max="!line.itemId ? undefined : (line.itemType === 'serial' ? serialsFor(line.itemId).length : itemOptions.find(o=>o.id===line.itemId)?.stock)"
                                                :readonly="line.itemType === 'serial'"
                                                required
                                                class="rounded-md border bg-background px-3 py-2 text-sm read-only:bg-muted/40"
                                            />
                                            <p v-if="line.itemType === 'serial'" class="text-[11px] text-muted-foreground">Qty mengikuti jumlah SN yang dipilih.</p>
                                        </div>
                                    </div>

                                    <div v-if="line.itemId && line.itemType === 'serial'" class="grid gap-1">
                                        <label class="text-xs font-medium text-muted-foreground">Pilih Serial Number (tersedia)</label>
                                        <div v-if="serialsFor(line.itemId).length" class="grid max-h-40 grid-cols-1 gap-1 overflow-y-auto rounded-md border bg-background p-2 sm:grid-cols-2">
                                            <label v-for="sn in serialsFor(line.itemId)" :key="sn" class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-muted/50">
                                                <input
                                                    type="checkbox"
                                                    class="rounded border"
                                                    :checked="line.selectedSerials.includes(sn)"
                                                    @change="toggleSerial(line, sn, ($event.target as HTMLInputElement).checked)"
                                                />
                                                <span class="font-mono text-xs">{{ sn }}</span>
                                            </label>
                                        </div>
                                        <div v-else class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                                            Tidak ada serial number tersedia untuk barang ini.
                                        </div>
                                        <p class="text-[11px] text-muted-foreground">Centang SN yang akan dikeluarkan sesuai jenis/barang yang dipilih.</p>
                                    </div>

                                    <div v-else-if="line.itemId && line.itemType === 'non-serial'" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                                        Barang non-serial: masukkan Qty sesuai jumlah yang akan dikeluarkan (tanpa serial number).
                                    </div>
                                    <div v-else class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                                        Pilih barang terlebih dahulu untuk menampilkan opsi serial number (jika barang serial).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
                            <Plus class="size-4" /> Simpan Barang Keluar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
