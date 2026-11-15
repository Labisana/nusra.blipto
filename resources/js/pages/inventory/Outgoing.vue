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

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Barang Keluar</h1>
                    <span class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]">
                        {{ filteredRows.length }} Baris
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">Catat pengeluaran perangkat ke site pelanggan (Instalasi, Mutasi, Maintenance, dll).</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="rounded-xl border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-2xs">
                    Peran: <span class="font-semibold text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95"
                    @click="openForm"
                >
                    <Plus class="size-4" /> Input Barang Keluar
                </button>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div class="flex flex-col gap-3 border-b border-border/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-foreground text-sm">Riwayat Barang Keluar</h2>
                    <p class="text-xs text-muted-foreground">Log mutasi unit ke pelanggan dan status serial terpasang</p>
                </div>
                <div class="relative w-full sm:w-72">
                    <Search class="pointer-events-none absolute left-3 top-2 size-3.5 text-muted-foreground" />
                    <input v-model="search" type="text" placeholder="Cari ref, barang, atau pelanggan..." class="w-full rounded-xl border border-border bg-background py-1.5 pl-8.5 pr-8 text-xs transition-colors hover:border-[#fb4f00]/40 focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                    <button v-if="search" class="absolute right-2.5 top-2 text-muted-foreground hover:text-foreground" @click="search = ''">
                        <X class="size-3.5" />
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">No. Ref</th>
                            <th class="px-4 py-3">Pelanggan Tujuan</th>
                            <th class="px-4 py-3">Activity</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3">Serial Number</th>
                            <th class="px-4 py-3">Petugas</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr v-for="r in filteredRows" :key="r.id" class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]">
                            <td class="px-4 py-3 font-mono text-muted-foreground whitespace-nowrap">{{ r.date }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-foreground whitespace-nowrap">{{ r.ref }}</td>
                            <td class="px-4 py-3 min-w-[200px] font-semibold text-foreground">{{ r.customer }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-bold text-foreground">
                                    {{ r.activity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 min-w-[180px] font-medium text-foreground">{{ r.item }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex rounded-full border border-border px-2 py-0.5 text-[10px] font-medium bg-background">
                                    {{ r.type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-[#fb4f00] whitespace-nowrap">-{{ r.qty }}</td>
                            <td class="px-4 py-3 max-w-[160px] truncate font-mono text-[11px] text-muted-foreground" :title="r.serials">{{ r.serials }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 rounded bg-muted/70 px-2 py-0.5 font-medium text-foreground">
                                    {{ r.user }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button class="rounded-lg border border-border bg-background px-2 py-1 text-xs font-medium hover:border-[#fb4f00]/40 hover:text-[#fb4f00] transition-colors disabled:opacity-40" :disabled="!canManage" @click="openEditById(r.transaction_id)">Edit</button>
                                    <button class="rounded-lg border border-destructive/20 bg-background px-2 py-1 text-xs font-medium text-destructive hover:bg-destructive/10 transition-colors disabled:opacity-40" :disabled="!canManage" @click="destroy(r.transaction_id)">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filteredRows.length">
                            <td colspan="10" class="px-4 py-8 text-center text-muted-foreground">Belum ada transaksi barang keluar yang sesuai.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-border/70 px-4 py-2.5 text-xs text-muted-foreground flex items-center justify-between">
                <span>Menampilkan {{ filteredRows.length }} baris dari {{ transactions.length }} transaksi. Terhubung langsung ke data pelanggan & BTS.</span>
                <span v-if="!canManage" class="text-amber-600 dark:text-amber-400 font-medium">Akses edit/hapus dikunci untuk Teknisi</span>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border border-border bg-card shadow-2xl sm:max-w-2xl sm:rounded-2xl">
                <div class="relative flex items-center justify-between border-b border-border/80 px-5 py-4">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#fb4f00] via-amber-400 to-[#fb4f00]"></div>
                    <div>
                        <h2 class="font-bold text-foreground text-base">{{ editingId ? 'Edit Transaksi Barang Keluar' : 'Input Barang Keluar Baru' }}</h2>
                        <p class="text-xs text-muted-foreground">Pilih pelanggan dan tentukan item beserta serial number yang dikeluarkan.</p>
                    </div>
                    <button type="button" class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-5" @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground">Pelanggan Tujuan <span class="text-destructive">*</span></label>
                            <select v-model="form.customer_id" required class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden">
                                <option value="">Pilih pelanggan...</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.nama_pelanggan }} (CID: {{ customer.cid ?? '-' }})
                                </option>
                            </select>
                            <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Jenis Activity <span class="text-destructive">*</span></label>
                                <select v-model="form.activity" required class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden">
                                    <option value="">Pilih activity...</option>
                                    <option v-for="activity in activities" :key="activity.value" :value="activity.value">{{ activity.label }}</option>
                                </select>
                                <p v-if="form.errors.activity" class="text-xs text-destructive">{{ form.errors.activity }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Tanggal Keluar <span class="text-destructive">*</span></label>
                                <input v-model="form.transaction_date" type="date" required class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                                <p v-if="form.errors.transaction_date" class="text-xs text-destructive">{{ form.errors.transaction_date }}</p>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">No. Referensi (SPK/WO)</label>
                                <input v-model="form.reference" type="text" placeholder="Otomatis jika kosong" class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                                <p v-if="form.errors.reference" class="text-xs text-destructive">{{ form.errors.reference }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-xs font-semibold text-foreground">Catatan / Keterangan</label>
                                <input v-model="form.note" type="text" placeholder="Keterangan tambahan transaksi" class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                            </div>
                        </div>

                        <div class="mt-2 flex items-center justify-between border-t border-border/70 pt-3">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">Daftar Barang Keluar</h3>
                                <p class="text-[11px] text-muted-foreground">Pilih barang dan tandai serial number yang dipasang.</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-1 rounded-xl border border-[#fb4f00]/30 bg-[#fb4f00]/10 px-2.5 py-1.5 text-xs font-semibold text-[#fb4f00] hover:bg-[#fb4f00]/20 transition-colors" @click="addItem">
                                <Plus class="size-3.5" /> Tambah Baris
                            </button>
                        </div>

                        <p v-if="form.errors.items" class="text-xs text-destructive">{{ form.errors.items }}</p>

                        <div class="grid gap-3">
                            <div v-for="(line, idx) in form.items" :key="idx" class="rounded-xl border border-border/80 bg-muted/20 p-3.5 transition-all">
                                <div class="mb-2 flex items-center justify-between border-b border-border/60 pb-2">
                                    <span class="text-xs font-bold text-foreground">Barang #{{ idx + 1 }}</span>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium text-destructive hover:bg-destructive/10 disabled:opacity-40 transition-colors"
                                        :disabled="form.items.length === 1"
                                        @click="removeItem(idx)"
                                    >
                                        <Trash2 class="size-3" /> Hapus
                                    </button>
                                </div>

                                <div class="grid gap-3">
                                    <div class="grid gap-1.5">
                                        <label class="text-xs font-semibold text-foreground">Pilih Barang <span class="text-destructive">*</span></label>
                                        <select v-model="line.item_id" required class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" @change="onItemChange(line)">
                                            <option value="">Pilih barang...</option>
                                            <option v-for="opt in items" :key="opt.id" :value="opt.id">
                                                {{ opt.name }} (Sisa: {{ opt.remaining }} {{ opt.unit }})
                                            </option>
                                        </select>
                                        <p v-if="lineError(idx, 'item_id')" class="text-xs text-destructive">{{ lineError(idx, 'item_id') }}</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="grid gap-1.5">
                                            <label class="text-xs font-semibold text-muted-foreground">Jenis Item</label>
                                            <input
                                                :value="line.item_id ? (itemFor(line.item_id)?.type_label ?? '-') : '-'"
                                                type="text"
                                                readonly
                                                class="rounded-xl border border-border bg-muted/40 px-3 py-2 text-xs font-medium text-foreground"
                                            />
                                        </div>
                                        <div class="grid gap-1.5">
                                            <label class="text-xs font-semibold text-foreground">Jumlah (Qty) <span class="text-destructive">*</span></label>
                                            <input
                                                v-model.number="line.quantity"
                                                type="number"
                                                min="1"
                                                required
                                                :readonly="itemFor(line.item_id)?.type === 'serial'"
                                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden read-only:bg-muted/40"
                                            />
                                            <p v-if="itemFor(line.item_id)?.type === 'serial'" class="text-[11px] text-muted-foreground">Otomatis dihitung dari centang SN di bawah.</p>
                                            <p v-if="lineError(idx, 'quantity')" class="text-xs text-destructive">{{ lineError(idx, 'quantity') }}</p>
                                        </div>
                                    </div>

                                    <div v-if="line.item_id && itemFor(line.item_id)?.type === 'serial'" class="grid gap-1.5">
                                        <label class="text-xs font-semibold text-foreground">Pilih Serial Number (Status: Tersedia di Gudang)</label>
                                        <div v-if="serialChoices(line).length" class="grid max-h-44 grid-cols-1 gap-1.5 overflow-y-auto rounded-xl border border-border bg-background p-2.5 sm:grid-cols-2">
                                            <label v-for="sn in serialChoices(line)" :key="sn" class="flex cursor-pointer items-center gap-2 rounded-lg border border-transparent p-2 text-xs transition-colors hover:border-[#fb4f00]/30 hover:bg-[#fb4f00]/5 has-checked:border-[#fb4f00] has-checked:bg-[#fb4f00]/10">
                                                <input
                                                    type="checkbox"
                                                    class="rounded text-[#fb4f00] focus:ring-[#fb4f00]"
                                                    :checked="line.serials.includes(sn)"
                                                    @change="toggleSerial(line, sn, ($event.target as HTMLInputElement).checked)"
                                                />
                                                <span class="font-mono text-xs font-medium text-foreground">{{ sn }}</span>
                                            </label>
                                        </div>
                                        <div v-else class="rounded-xl border border-dashed border-destructive/30 bg-destructive/5 p-3 text-xs text-destructive">
                                            Tidak ada serial number tersedia untuk barang ini.
                                        </div>
                                        <p class="text-[11px] text-muted-foreground">Centang SN unit yang akan dipasang ke pelanggan.</p>
                                        <p v-if="lineError(idx, 'serials')" class="text-xs text-destructive">{{ lineError(idx, 'serials') }}</p>
                                    </div>

                                    <div v-else-if="line.item_id" class="rounded-xl border border-dashed border-border bg-muted/30 p-2.5 text-xs text-muted-foreground">
                                        Barang non-serial: masukkan jumlah Qty yang akan dikeluarkan.
                                    </div>
                                    <div v-else class="rounded-xl border border-dashed border-border bg-muted/20 p-2.5 text-xs text-muted-foreground">
                                        Pilih barang terlebih dahulu untuk memunculkan serial number.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col-reverse gap-2 border-t border-border/70 pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted transition-colors" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 disabled:opacity-50">
                            <Plus class="size-4" /> {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Barang Keluar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
