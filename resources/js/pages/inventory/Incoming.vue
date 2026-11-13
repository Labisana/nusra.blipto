<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus, Search, Trash2, X } from '@lucide/vue';
import { ref, computed, watch } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Barang Masuk', href: '/barang-masuk' },
        ],
    },
});

const { role, canManage } = useRole();
const canCreate = computed(() => true); // teknisi boleh input
const showForm = ref(false);

const activities = ['Dismantle', 'FPB'] as const;

const customers = [
    { id: '1', name: 'PT Nusra Net - Cab. Mataram', tipe: 'Busol', label: 'PT Nusra Net - Cab. Mataram (CID: C-001)' },
    { id: '2', name: 'PT Nusra Net - Cab. Praya', tipe: 'Broadband', label: 'PT Nusra Net - Cab. Praya (CID: C-002)' },
    { id: '3', name: 'CV Sinar Lombok', tipe: 'Busol', label: 'CV Sinar Lombok (CID: C-003)' },
];

const itemOptions = [
    { id: '1', name: 'Router Mikrotik RB750 (Serial)', type: 'serial' },
    { id: '2', name: 'ONU/ONT ZTE F609 (Serial)', type: 'serial' },
    { id: '3', name: 'Kabel UTP Cat6 (Non-Serial)', type: 'non-serial' },
    { id: '4', name: 'Konektor RJ45 (Non-Serial)', type: 'non-serial' },
];

type LineItem = {
    itemId: string;
    itemType: 'serial' | 'non-serial';
    qty: number;
    serials: string;
};

function createLine(): LineItem {
    return { itemId: '', itemType: 'serial', qty: 1, serials: '' };
}

const form = ref({
    activity: '' as '' | (typeof activities)[number],
    customerId: '',
    date: '2026-04-10',
    ref: '',
    note: '',
    items: [createLine()] as LineItem[],
});

const selectedCustomer = computed(() => customers.find((c) => c.id === form.value.customerId) || null);
const isDismantle = computed(() => form.value.activity === 'Dismantle');

watch(
    () => form.value.activity,
    (val) => {
        if (val === 'FPB') {
            form.value.customerId = '';
        }
    },
);

const rows = [
    { date: '2026-04-10', ref: 'IN-2401', activity: 'FPB', item: 'Router Mikrotik RB750', type: 'Serial', qty: 5, serials: 'RB750-001…005', customer: '-', tipe: '-', user: 'Admin' },
    { date: '2026-04-10', ref: 'IN-2402', activity: 'FPB', item: 'Kabel UTP Cat6', type: 'Non-Serial', qty: 20, serials: '-', customer: '-', tipe: '-', user: 'Teknisi Budi' },
    { date: '2026-04-09', ref: 'IN-2398', activity: 'Dismantle', item: 'ONU/ONT ZTE F609', type: 'Serial', qty: 12, serials: 'F609-A01…A12', customer: 'PT Nusra Net - Cab. Praya', tipe: 'Broadband', user: 'Admin' },
];

function openForm() {
    if (!canCreate.value) return;
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

function submit() {
    showForm.value = false;
}
</script>

<template>
    <Head title="Barang Masuk" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Barang Masuk</h1>
                <p class="text-sm text-muted-foreground">Catat penerimaan barang (serial & non-serial). Satu transaksi dapat berisi lebih dari satu barang.</p>
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
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    :disabled="!canCreate"
                    @click="openForm"
                >
                    <Plus class="size-4" /> Input Barang Masuk
                </button>
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium">Riwayat Barang Masuk</h2>
                <div class="relative w-full sm:w-64">
                    <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                    <input type="text" placeholder="Cari ref / barang..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">Tanggal</th>
                            <th class="px-4 py-2 font-medium">Ref</th>
                            <th class="px-4 py-2 font-medium">Activity</th>
                            <th class="px-4 py-2 font-medium">Pelanggan</th>
                            <th class="px-4 py-2 font-medium">Tipe</th>
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
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.activity }}</span></td>
                            <td class="px-4 py-2 min-w-[160px]">{{ r.customer }}</td>
                            <td class="px-4 py-2">{{ r.tipe }}</td>
                            <td class="px-4 py-2 min-w-[160px]">{{ r.item }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ r.type }}</span></td>
                            <td class="px-4 py-2 text-emerald-600 font-medium">+{{ r.qty }}</td>
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
                Menampilkan 3 dari 128 transaksi. <span v-if="!canManage">Aksi edit/hapus dikunci untuk peran Teknisi.</span>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-2xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">Input Barang Masuk</h2>
                        <p class="text-xs text-muted-foreground">Pilih activity. Dismantle wajib pilih pelanggan; FPB tanpa pelanggan.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Activity <span class="text-destructive">*</span></label>
                            <select v-model="form.activity" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option value="">Pilih activity...</option>
                                <option v-for="a in activities" :key="a" :value="a">{{ a }}</option>
                            </select>
                        </div>

                        <template v-if="isDismantle">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Nama Pelanggan <span class="text-destructive">*</span></label>
                                <select v-model="form.customerId" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Pilih pelanggan...</option>
                                    <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.label }}</option>
                                </select>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Tipe Pelanggan</label>
                                <input
                                    :value="selectedCustomer?.tipe ?? '-'"
                                    type="text"
                                    readonly
                                    class="rounded-md border bg-muted/40 px-3 py-2 text-sm text-foreground"
                                    placeholder="Otomatis dari data pelanggan"
                                />
                                <p class="text-[11px] text-muted-foreground">Tipe pelanggan (Busol/Broadband) mengikuti pelanggan yang dipilih.</p>
                            </div>
                        </template>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Tanggal <span class="text-destructive">*</span></label>
                                <input v-model="form.date" type="date" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">No. Referensi (PO/FPB/Retur)</label>
                                <input v-model="form.ref" type="text" placeholder="PO-xxxx / FPB-xxxx / RET-xxxx" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Catatan</label>
                            <input v-model="form.note" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-1 flex items-center justify-between border-t pt-3">
                            <div>
                                <h3 class="text-sm font-medium">Daftar Barang Masuk</h3>
                                <p class="text-[11px] text-muted-foreground">Tambahkan lebih dari 1 barang dalam satu transaksi.</p>
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
                                        <select v-model="line.itemId" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                            <option value="">Pilih barang...</option>
                                            <option v-for="opt in itemOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Jenis</label>
                                            <select v-model="line.itemType" class="rounded-md border bg-background px-3 py-2 text-sm">
                                                <option value="serial">Serial Number</option>
                                                <option value="non-serial">Non-Serial</option>
                                            </select>
                                        </div>
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Qty <span class="text-destructive">*</span></label>
                                            <input v-model.number="line.qty" type="number" min="1" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                        </div>
                                    </div>
                                    <div v-if="line.itemType === 'serial'" class="grid gap-1">
                                        <label class="text-xs font-medium text-muted-foreground">Daftar Serial Number</label>
                                        <textarea v-model="line.serials" rows="2" placeholder="Satu SN per baris atau pisahkan koma" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                                        <p class="text-[11px] text-muted-foreground">Wajib untuk barang berserial. Jumlah SN harus sesuai Qty.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
                            <Plus class="size-4" /> Simpan Barang Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
