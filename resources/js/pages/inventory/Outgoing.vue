<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Search, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';
import type { CustomerOption, ItemOption, SelectOption, StockTransaction } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Barang Keluar', href: '/barang-keluar' },
        ],
    },
});

const props = defineProps<{
    transactions: StockTransaction[];
    customers: CustomerOption[];
    items: ItemOption[];
    activities: SelectOption[];
}>();

const { roleLabel, canManage } = useRole();
const showForm = ref(false);
const editingId = ref<number | null>(null);
const search = ref('');

type Line = {
    item_id: number | '';
    quantity: number;
    serials: string[];
};

function createLine(): Line {
    return { item_id: '', quantity: 1, serials: [] };
}

const form = useForm({
    customer_id: '' as number | '',
    transaction_date: new Date().toISOString().slice(0, 10),
    activity: '',
    reference: '',
    note: '',
    items: [createLine()] as Line[],
});

function itemFor(itemId: number | ''): ItemOption | null {
    return props.items.find((item) => item.id === itemId) ?? null;
}

function serialChoices(line: Line): string[] {
    const available = itemFor(line.item_id)?.serials_available ?? [];

    return Array.from(new Set([...available, ...line.serials])).sort();
}

const rows = computed(() =>
    props.transactions.flatMap((transaction) =>
        transaction.items.map((line) => ({
            id: `${transaction.id}-${line.id}`,
            transaction_id: transaction.id,
            date: transaction.transaction_date,
            ref: transaction.reference,
            customer: transaction.customer?.nama_pelanggan ?? '-',
            activity: transaction.activity,
            item: line.item_name,
            type: line.item_type_label,
            qty: line.quantity,
            serials: line.serials.length ? line.serials.join(', ') : '-',
            user: transaction.user?.name ?? '-',
        })),
    ),
);

const filteredRows = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return rows.value;
    }

    return rows.value.filter((row) =>
        [row.ref, row.item, row.customer, row.activity]
            .some((value) => value.toLowerCase().includes(term)),
    );
});

function openForm() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.transaction_date = new Date().toISOString().slice(0, 10);
    form.items = [createLine()];
    showForm.value = true;
}

function openEdit(transaction: StockTransaction) {
    if (!canManage.value) return;

    editingId.value = transaction.id;
    form.clearErrors();
    form.customer_id = transaction.customer_id ?? '';
    form.transaction_date = transaction.transaction_date;
    form.activity = transaction.activity;
    form.reference = transaction.reference;
    form.note = transaction.note ?? '';
    form.items = transaction.items.map((line) => ({
        item_id: line.item_id,
        quantity: line.quantity,
        serials: [...line.serials],
    }));
    showForm.value = true;
}

function openEditById(transactionId: number) {
    const transaction = props.transactions.find((item) => item.id === transactionId);

    if (transaction) {
        openEdit(transaction);
    }
}

function closeForm() {
    showForm.value = false;
}

function addItem() {
    form.items.push(createLine());
}

function removeItem(index: number) {
    if (form.items.length === 1) return;
    form.items.splice(index, 1);
}

function onItemChange(line: Line) {
    const item = itemFor(line.item_id);
    line.serials = [];
    line.quantity = item?.type === 'serial' ? 0 : 1;
}

function toggleSerial(line: Line, serial: string, checked: boolean) {
    if (checked) {
        if (!line.serials.includes(serial)) {
            line.serials.push(serial);
        }
    } else {
        line.serials = line.serials.filter((value) => value !== serial);
    }

    line.quantity = line.serials.length;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.reset();
            form.items = [createLine()];
        },
    };

    if (editingId.value) {
        form.put(`/barang-keluar/${editingId.value}`, options);
        return;
    }

    form.post('/barang-keluar', options);
}

function destroy(transactionId: number) {
    if (!canManage.value) return;

    if (confirm('Hapus transaksi barang keluar ini?')) {
        router.delete(`/barang-keluar/${transactionId}`, { preserveScroll: true });
    }
}

function lineError(index: number, key: 'item_id' | 'quantity' | 'serials'): string | undefined {
    return (form.errors as Record<string, string>)[`items.${index}.${key}`];
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
                <span class="rounded-md border bg-card px-2.5 py-1.5 text-xs text-muted-foreground">
                    Akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
                </span>
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
                    <input v-model="search" type="text" placeholder="Cari ref / barang / pelanggan..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
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
                        <tr v-for="r in filteredRows" :key="r.id" class="hover:bg-muted/30">
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
                                <button class="rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40" :disabled="!canManage" @click="openEditById(r.transaction_id)">Edit</button>
                                <button class="rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40" :disabled="!canManage" @click="destroy(r.transaction_id)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!filteredRows.length">
                            <td colspan="10" class="px-4 py-6 text-center text-muted-foreground">Belum ada transaksi barang keluar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Menampilkan {{ filteredRows.length }} baris dari {{ transactions.length }} transaksi. Barang keluar terhubung ke pelanggan dan activity.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-2xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ editingId ? 'Edit Barang Keluar' : 'Input Barang Keluar' }}</h2>
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
                            <select v-model="form.customer_id" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option value="">Pilih pelanggan...</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.nama_pelanggan }} ({{ customer.tipe_pelanggan }})
                                </option>
                            </select>
                            <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Tanggal <span class="text-destructive">*</span></label>
                                <input v-model="form.transaction_date" type="date" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                <p v-if="form.errors.transaction_date" class="text-xs text-destructive">{{ form.errors.transaction_date }}</p>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Activity <span class="text-destructive">*</span></label>
                                <select v-model="form.activity" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Pilih activity...</option>
                                    <option v-for="activity in activities" :key="activity.value" :value="activity.value">{{ activity.label }}</option>
                                </select>
                                <p v-if="form.errors.activity" class="text-xs text-destructive">{{ form.errors.activity }}</p>
                            </div>
                        </div>
                        <div class="grid gap-1 sm:grid-cols-2 sm:gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">No. Referensi (DO)</label>
                                <input v-model="form.reference" type="text" placeholder="Otomatis jika kosong" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                <p v-if="form.errors.reference" class="text-xs text-destructive">{{ form.errors.reference }}</p>
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

                        <p v-if="form.errors.items" class="text-xs text-destructive">{{ form.errors.items }}</p>

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
                                        <select v-model="line.item_id" required class="rounded-md border bg-background px-3 py-2 text-sm" @change="onItemChange(line)">
                                            <option value="">Pilih barang...</option>
                                            <option v-for="opt in items" :key="opt.id" :value="opt.id">
                                                {{ opt.name }} ({{ opt.type_label }}, sisa {{ opt.remaining }})
                                            </option>
                                        </select>
                                        <p v-if="lineError(idx, 'item_id')" class="text-xs text-destructive">{{ lineError(idx, 'item_id') }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Jenis</label>
                                            <input
                                                :value="line.item_id ? (itemFor(line.item_id)?.type_label ?? '-') : '-'"
                                                type="text"
                                                readonly
                                                class="rounded-md border bg-muted/40 px-3 py-2 text-sm"
                                            />
                                        </div>
                                        <div class="grid gap-1">
                                            <label class="text-xs font-medium text-muted-foreground">Qty <span class="text-destructive">*</span></label>
                                            <input
                                                v-model.number="line.quantity"
                                                type="number"
                                                min="1"
                                                :max="itemFor(line.item_id)?.type === 'serial' ? undefined : itemFor(line.item_id)?.remaining"
                                                :readonly="itemFor(line.item_id)?.type === 'serial'"
                                                required
                                                class="rounded-md border bg-background px-3 py-2 text-sm read-only:bg-muted/40"
                                            />
                                            <p v-if="itemFor(line.item_id)?.type === 'serial'" class="text-[11px] text-muted-foreground">Qty mengikuti jumlah SN yang dipilih.</p>
                                            <p v-if="lineError(idx, 'quantity')" class="text-xs text-destructive">{{ lineError(idx, 'quantity') }}</p>
                                        </div>
                                    </div>

                                    <div v-if="line.item_id && itemFor(line.item_id)?.type === 'serial'" class="grid gap-1">
                                        <label class="text-xs font-medium text-muted-foreground">Pilih Serial Number (tersedia)</label>
                                        <div v-if="serialChoices(line).length" class="grid max-h-40 grid-cols-1 gap-1 overflow-y-auto rounded-md border bg-background p-2 sm:grid-cols-2">
                                            <label v-for="sn in serialChoices(line)" :key="sn" class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-muted/50">
                                                <input
                                                    type="checkbox"
                                                    class="rounded border"
                                                    :checked="line.serials.includes(sn)"
                                                    @change="toggleSerial(line, sn, ($event.target as HTMLInputElement).checked)"
                                                />
                                                <span class="font-mono text-xs">{{ sn }}</span>
                                            </label>
                                        </div>
                                        <div v-else class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                                            Tidak ada serial number tersedia untuk barang ini.
                                        </div>
                                        <p class="text-[11px] text-muted-foreground">Centang SN yang akan dikeluarkan sesuai jenis/barang yang dipilih.</p>
                                        <p v-if="lineError(idx, 'serials')" class="text-xs text-destructive">{{ lineError(idx, 'serials') }}</p>
                                    </div>

                                    <div v-else-if="line.item_id" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
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
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">
                            <Plus class="size-4" /> {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Barang Keluar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
