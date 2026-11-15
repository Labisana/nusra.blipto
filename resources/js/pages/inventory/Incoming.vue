<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Search, Trash2, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { useRole } from '@/composables/useRole';
import type { CustomerOption, ItemOption, SelectOption, StockTransaction } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Barang Masuk', href: '/barang-masuk' },
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
    serials: string;
};

function createLine(): Line {
    return { item_id: '', quantity: 1, serials: '' };
}

const form = useForm({
    activity: '',
    customer_id: '' as number | '',
    transaction_date: new Date().toISOString().slice(0, 10),
    reference: '',
    note: '',
    items: [createLine()] as Line[],
});

const selectedCustomer = computed(
    () => props.customers.find((customer) => customer.id === form.customer_id) ?? null,
);
const isDismantle = computed(() => form.activity === 'Dismantle');

watch(
    () => form.activity,
    (value) => {
        if (value === 'FPB') {
            form.customer_id = '';
        }
    },
);

function itemFor(itemId: number | ''): ItemOption | null {
    return props.items.find((item) => item.id === itemId) ?? null;
}

const rows = computed(() =>
    props.transactions.flatMap((transaction) =>
        transaction.items.map((line) => ({
            id: `${transaction.id}-${line.id}`,
            transaction_id: transaction.id,
            date: transaction.transaction_date,
            ref: transaction.reference,
            activity: transaction.activity,
            customer: transaction.customer?.nama_pelanggan ?? '-',
            tipe: transaction.customer?.tipe_pelanggan ?? '-',
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
    form.activity = transaction.activity;
    form.customer_id = transaction.customer_id ?? '';
    form.transaction_date = transaction.transaction_date;
    form.reference = transaction.reference;
    form.note = transaction.note ?? '';
    form.items = transaction.items.map((line) => ({
        item_id: line.item_id,
        quantity: line.quantity,
        serials: line.serials.join('\n'),
    }));
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

function openEditById(transactionId: number) {
    const transaction = props.transactions.find((item) => item.id === transactionId);

    if (transaction) {
        openEdit(transaction);
    }
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
    line.quantity = 1;
    line.serials = '';
    if (item?.type === 'non-serial') {
        line.serials = '';
    }
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
        form.put(`/barang-masuk/${editingId.value}`, options);
        return;
    }

    form.post('/barang-masuk', options);
}

function destroy(transactionId: number) {
    if (!canManage.value) return;

    if (confirm('Hapus transaksi barang masuk ini?')) {
        router.delete(`/barang-masuk/${transactionId}`, { preserveScroll: true });
    }
}

function lineError(index: number, key: 'item_id' | 'quantity' | 'serials'): string | undefined {
    return (form.errors as Record<string, string>)[`items.${index}.${key}`];
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
                <span class="rounded-md border bg-card px-2.5 py-1.5 text-xs text-muted-foreground">
                    Akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90"
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
                    <input v-model="search" type="text" placeholder="Cari ref / barang..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
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
                        <tr v-for="r in filteredRows" :key="r.id" class="hover:bg-muted/30">
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
                                <button class="rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40" :disabled="!canManage" @click="openEditById(r.transaction_id)">Edit</button>
                                <button class="rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40" :disabled="!canManage" @click="destroy(r.transaction_id)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!filteredRows.length">
                            <td colspan="11" class="px-4 py-6 text-center text-muted-foreground">Belum ada transaksi barang masuk.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Menampilkan {{ filteredRows.length }} baris dari {{ transactions.length }} transaksi.
                <span v-if="!canManage">Aksi edit/hapus dikunci untuk peran Teknisi.</span>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-2xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ editingId ? 'Edit Barang Masuk' : 'Input Barang Masuk' }}</h2>
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
                                <option v-for="activity in activities" :key="activity.value" :value="activity.value">{{ activity.label }}</option>
                            </select>
                            <p v-if="form.errors.activity" class="text-xs text-destructive">{{ form.errors.activity }}</p>
                        </div>

                        <template v-if="isDismantle">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Nama Pelanggan <span class="text-destructive">*</span></label>
                                <select v-model="form.customer_id" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option value="">Pilih pelanggan...</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.nama_pelanggan }} (CID: {{ customer.cid ?? '-' }})
                                    </option>
                                </select>
                                <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Tipe Pelanggan</label>
                                <input
                                    :value="selectedCustomer?.tipe_pelanggan ?? '-'"
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
                                <input v-model="form.transaction_date" type="date" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                <p v-if="form.errors.transaction_date" class="text-xs text-destructive">{{ form.errors.transaction_date }}</p>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">No. Referensi (PO/FPB/Retur)</label>
                                <input v-model="form.reference" type="text" placeholder="Otomatis jika kosong" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                <p v-if="form.errors.reference" class="text-xs text-destructive">{{ form.errors.reference }}</p>
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
                                                {{ opt.name }} ({{ opt.type_label }})
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
                                            <input v-model.number="line.quantity" type="number" min="1" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                            <p v-if="lineError(idx, 'quantity')" class="text-xs text-destructive">{{ lineError(idx, 'quantity') }}</p>
                                        </div>
                                    </div>
                                    <div v-if="itemFor(line.item_id)?.type === 'serial'" class="grid gap-1">
                                        <label class="text-xs font-medium text-muted-foreground">Daftar Serial Number</label>
                                        <textarea v-model="line.serials" rows="2" placeholder="Satu SN per baris atau pisahkan koma" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                                        <p class="text-[11px] text-muted-foreground">Wajib untuk barang berserial. Jumlah SN harus sesuai Qty.</p>
                                        <p v-if="lineError(idx, 'serials')" class="text-xs text-destructive">{{ lineError(idx, 'serials') }}</p>
                                    </div>
                                    <div v-else-if="line.item_id" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                                        Barang non-serial: cukup isi Qty tanpa serial number.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">
                            <Plus class="size-4" /> {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Barang Masuk') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
