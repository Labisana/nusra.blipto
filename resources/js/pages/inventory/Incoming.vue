<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Search, Trash2, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { useRole } from '@/composables/useRole';
import type {
    CustomerOption,
    ItemOption,
    SelectOption,
    StockTransaction,
} from '@/types/inventory';

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
    () =>
        props.customers.find((customer) => customer.id === form.customer_id) ??
        null,
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
        [row.ref, row.item, row.customer, row.activity].some((value) =>
            value.toLowerCase().includes(term),
        ),
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
    const transaction = props.transactions.find(
        (item) => item.id === transactionId,
    );

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
        router.delete(`/barang-masuk/${transactionId}`, {
            preserveScroll: true,
        });
    }
}

function lineError(
    index: number,
    key: 'item_id' | 'quantity' | 'serials',
): string | undefined {
    return (form.errors as Record<string, string>)[`items.${index}.${key}`];
}
</script>

<template>
    <Head title="Barang Masuk" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <div class="flex items-center gap-2">
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        Barang Masuk
                    </h1>
                    <span
                        class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                    >
                        {{ filteredRows.length }} Baris
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Pencatatan penerimaan unit baru (FPB) maupun perangkat
                    bongkaran (Dismantle).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <span
                    class="rounded-xl border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-2xs"
                >
                    Peran:
                    <span class="font-semibold text-foreground">{{
                        roleLabel
                    }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-emerald-600/25 transition-all hover:brightness-105 active:scale-95"
                    @click="openForm"
                >
                    <Plus class="size-4" /> Input Barang Masuk
                </button>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div
                class="flex flex-col gap-3 border-b border-border/70 p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-sm font-semibold text-foreground">
                        Riwayat Barang Masuk
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Daftar transaksi penerimaan barang dan log mutasi serial
                    </p>
                </div>
                <div class="relative w-full sm:w-72">
                    <Search
                        class="pointer-events-none absolute top-2 left-3 size-3.5 text-muted-foreground"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari ref, barang, atau pelanggan..."
                        class="w-full rounded-xl border border-border bg-background py-1.5 pr-8 pl-8.5 text-xs outline-hidden transition-colors hover:border-[#fb4f00]/40 focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                    />
                    <button
                        v-if="search"
                        class="absolute top-2 right-2.5 text-muted-foreground hover:text-foreground"
                        @click="search = ''"
                    >
                        <X class="size-3.5" />
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
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
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="r in filteredRows"
                            :key="r.id"
                            class="transition-colors hover:bg-emerald-500/[0.02] dark:hover:bg-emerald-500/[0.05]"
                        >
                            <td
                                class="px-4 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.date }}
                            </td>
                            <td
                                class="px-4 py-3 font-mono font-semibold whitespace-nowrap text-foreground"
                            >
                                {{ r.ref }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-bold text-foreground"
                                >
                                    {{ r.activity }}
                                </span>
                            </td>
                            <td
                                class="min-w-[160px] px-4 py-3 font-medium text-foreground"
                            >
                                {{ r.customer }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.tipe }}
                            </td>
                            <td
                                class="min-w-[180px] px-4 py-3 font-semibold text-foreground"
                            >
                                {{ r.item }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium"
                                >
                                    {{ r.type }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 text-right font-bold whitespace-nowrap text-emerald-600 dark:text-emerald-400"
                            >
                                +{{ r.qty }}
                            </td>
                            <td
                                class="max-w-[160px] truncate px-4 py-3 font-mono text-[11px] text-muted-foreground"
                                :title="r.serials"
                            >
                                {{ r.serials }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground"
                                >
                                    {{ r.user }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button
                                        class="rounded-lg border border-border bg-background px-2 py-1 text-xs font-medium transition-colors hover:border-[#fb4f00]/40 hover:text-[#fb4f00] disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="openEditById(r.transaction_id)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="rounded-lg border border-destructive/20 bg-background px-2 py-1 text-xs font-medium text-destructive transition-colors hover:bg-destructive/10 disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="destroy(r.transaction_id)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filteredRows.length">
                            <td
                                colspan="11"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada transaksi barang masuk yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="flex items-center justify-between border-t border-border/70 px-4 py-2.5 text-xs text-muted-foreground"
            >
                <span
                    >Menampilkan {{ filteredRows.length }} baris dari
                    {{ transactions.length }} transaksi tercatat.</span
                >
                <span
                    v-if="!canManage"
                    class="font-medium text-amber-600 dark:text-amber-400"
                    >Akses edit/hapus dikunci untuk Teknisi</span
                >
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4"
        >
            <div
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"
                @click="closeForm"
            ></div>
            <div
                class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border border-border bg-card shadow-2xl sm:max-w-2xl sm:rounded-2xl"
            >
                <div
                    class="relative flex items-center justify-between border-b border-border/80 px-5 py-4"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500"
                    ></div>
                    <div>
                        <h2 class="text-base font-bold text-foreground">
                            {{
                                editingId
                                    ? 'Edit Transaksi Barang Masuk'
                                    : 'Input Barang Masuk Baru'
                            }}
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Pilih jenis activity (FPB penerimaan baru atau
                            Dismantle eks-pelanggan).
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        @click="closeForm"
                        aria-label="Tutup"
                    >
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-5" @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground"
                                >Jenis Activity
                                <span class="text-destructive">*</span></label
                            >
                            <select
                                v-model="form.activity"
                                required
                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                            >
                                <option value="">Pilih activity...</option>
                                <option
                                    v-for="activity in activities"
                                    :key="activity.value"
                                    :value="activity.value"
                                >
                                    {{ activity.label }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.activity"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.activity }}
                            </p>
                        </div>

                        <template v-if="isDismantle">
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Pelanggan Asal (Dismantle)
                                    <span class="text-destructive"
                                        >*</span
                                    ></label
                                >
                                <select
                                    v-model="form.customer_id"
                                    required
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                >
                                    <option value="">Pilih pelanggan...</option>
                                    <option
                                        v-for="customer in customers"
                                        :key="customer.id"
                                        :value="customer.id"
                                    >
                                        {{ customer.nama_pelanggan }} (CID:
                                        {{ customer.cid ?? '-' }})
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.customer_id"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.customer_id }}
                                </p>
                            </div>
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-muted-foreground"
                                    >Tipe Pelanggan</label
                                >
                                <input
                                    :value="
                                        selectedCustomer?.tipe_pelanggan ?? '-'
                                    "
                                    type="text"
                                    readonly
                                    class="rounded-xl border border-border bg-muted/40 px-3 py-2 text-xs font-medium text-foreground"
                                    placeholder="Otomatis dari data pelanggan"
                                />
                                <p class="text-[11px] text-muted-foreground">
                                    Tipe pelanggan (Busol/Broadband) sinkron
                                    otomatis.
                                </p>
                            </div>
                        </template>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Tanggal Masuk
                                    <span class="text-destructive"
                                        >*</span
                                    ></label
                                >
                                <input
                                    v-model="form.transaction_date"
                                    type="date"
                                    required
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                                <p
                                    v-if="form.errors.transaction_date"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.transaction_date }}
                                </p>
                            </div>
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >No. Referensi (PO / FPB / Retur)</label
                                >
                                <input
                                    v-model="form.reference"
                                    type="text"
                                    placeholder="Otomatis jika dikosongkan"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                                <p
                                    v-if="form.errors.reference"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.reference }}
                                </p>
                            </div>
                        </div>
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground"
                                >Catatan Tambahan</label
                            >
                            <input
                                v-model="form.note"
                                type="text"
                                placeholder="Keterangan kondisi / lokasi barang"
                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                            />
                        </div>

                        <div
                            class="mt-2 flex items-center justify-between border-t border-border/70 pt-3"
                        >
                            <div>
                                <h3
                                    class="text-xs font-bold tracking-wider text-foreground uppercase"
                                >
                                    Daftar Barang Masuk
                                </h3>
                                <p class="text-[11px] text-muted-foreground">
                                    Dapat menambahkan beberapa barang sekaligus.
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1.5 text-xs font-semibold text-emerald-600 transition-colors hover:bg-emerald-500/20 dark:text-emerald-400"
                                @click="addItem"
                            >
                                <Plus class="size-3.5" /> Tambah Baris
                            </button>
                        </div>

                        <p
                            v-if="form.errors.items"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.items }}
                        </p>

                        <div class="grid gap-3">
                            <div
                                v-for="(line, idx) in form.items"
                                :key="idx"
                                class="rounded-xl border border-border/80 bg-muted/20 p-3.5 transition-all"
                            >
                                <div
                                    class="mb-2 flex items-center justify-between border-b border-border/60 pb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-foreground"
                                        >Barang #{{ idx + 1 }}</span
                                    >
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-destructive transition-colors hover:bg-destructive/10 disabled:opacity-40"
                                        :disabled="form.items.length === 1"
                                        @click="removeItem(idx)"
                                    >
                                        <Trash2 class="size-3" /> Hapus
                                    </button>
                                </div>
                                <div class="grid gap-3">
                                    <div class="grid gap-1.5">
                                        <label
                                            class="text-xs font-semibold text-foreground"
                                            >Pilih Barang
                                            <span class="text-destructive"
                                                >*</span
                                            ></label
                                        >
                                        <select
                                            v-model="line.item_id"
                                            required
                                            class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                            @change="onItemChange(line)"
                                        >
                                            <option value="">
                                                Pilih barang...
                                            </option>
                                            <option
                                                v-for="opt in items"
                                                :key="opt.id"
                                                :value="opt.id"
                                            >
                                                {{ opt.name }} ({{
                                                    opt.type_label
                                                }})
                                            </option>
                                        </select>
                                        <p
                                            v-if="lineError(idx, 'item_id')"
                                            class="text-xs text-destructive"
                                        >
                                            {{ lineError(idx, 'item_id') }}
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="grid gap-1.5">
                                            <label
                                                class="text-xs font-semibold text-muted-foreground"
                                                >Jenis Item</label
                                            >
                                            <input
                                                :value="
                                                    line.item_id
                                                        ? (itemFor(line.item_id)
                                                              ?.type_label ??
                                                          '-')
                                                        : '-'
                                                "
                                                type="text"
                                                readonly
                                                class="rounded-xl border border-border bg-muted/40 px-3 py-2 text-xs font-medium text-foreground"
                                            />
                                        </div>
                                        <div class="grid gap-1.5">
                                            <label
                                                class="text-xs font-semibold text-foreground"
                                                >Jumlah (Qty)
                                                <span class="text-destructive"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                v-model.number="line.quantity"
                                                type="number"
                                                min="1"
                                                required
                                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                            />
                                            <p
                                                v-if="
                                                    lineError(idx, 'quantity')
                                                "
                                                class="text-xs text-destructive"
                                            >
                                                {{ lineError(idx, 'quantity') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        v-if="
                                            itemFor(line.item_id)?.type ===
                                            'serial'
                                        "
                                        class="grid gap-1.5"
                                    >
                                        <label
                                            class="text-xs font-semibold text-foreground"
                                            >Daftar Serial Number</label
                                        >
                                        <textarea
                                            v-model="line.serials"
                                            rows="2"
                                            placeholder="Masukkan 1 SN per baris atau pisahkan dengan koma"
                                            class="rounded-xl border border-border bg-background px-3 py-2 font-mono text-xs text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                        ></textarea>
                                        <p
                                            class="text-[11px] text-muted-foreground"
                                        >
                                            Jumlah serial number harus persis
                                            sama dengan Qty.
                                        </p>
                                        <p
                                            v-if="lineError(idx, 'serials')"
                                            class="text-xs text-destructive"
                                        >
                                            {{ lineError(idx, 'serials') }}
                                        </p>
                                    </div>
                                    <div
                                        v-else-if="line.item_id"
                                        class="rounded-xl border border-dashed border-border bg-muted/30 p-2.5 text-xs text-muted-foreground"
                                    >
                                        Barang non-serial: stok bertambah sesuai
                                        Qty tanpa tracking individual serial.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-6 flex flex-col-reverse gap-2 border-t border-border/70 pt-4 sm:flex-row sm:justify-end"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground transition-colors hover:bg-muted"
                            @click="closeForm"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-emerald-600/25 transition-all hover:brightness-105 disabled:opacity-50"
                        >
                            <Plus class="size-4" />
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : editingId
                                      ? 'Simpan Perubahan'
                                      : 'Simpan Barang Masuk'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
