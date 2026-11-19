<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';
import type { ItemOption, SelectOption, SerialRow } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Stock', href: '/stok' },
        ],
    },
});

const props = defineProps<{
    items: ItemOption[];
    serials: SerialRow[];
    itemTypes: SelectOption[];
    filters: { q: string; type: string };
}>();

const { roleLabel, canManage } = useRole();
const showForm = ref(false);
const editingId = ref<number | null>(null);
const search = ref(props.filters.q ?? '');
const typeFilter = ref(props.filters.type ?? '');

const form = useForm({
    sku: '',
    name: '',
    type: 'non-serial',
    unit: 'unit',
    location: '',
    min_stock: 0,
});

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();

    return props.items.filter((item) => {
        const matchesType = !typeFilter.value || item.type === typeFilter.value;
        const matchesTerm =
            !term ||
            item.sku.toLowerCase().includes(term) ||
            item.name.toLowerCase().includes(term);

        return matchesType && matchesTerm;
    });
});

const lowStockCount = computed(
    () =>
        props.items.filter(
            (item) => item.min_stock > 0 && item.remaining <= item.min_stock,
        ).length,
);

const totalRemaining = computed(() =>
    props.items.reduce((total, item) => total + item.remaining, 0),
);

function openAdd() {
    if (!canManage.value) return;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.type = 'non-serial';
    form.unit = 'unit';
    form.min_stock = 0;
    showForm.value = true;
}

function openEdit(item: ItemOption) {
    if (!canManage.value) return;
    editingId.value = item.id;
    form.clearErrors();
    form.sku = item.sku;
    form.name = item.name;
    form.type = item.type;
    form.unit = item.unit;
    form.location = item.location ?? '';
    form.min_stock = item.min_stock;
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        },
    };

    if (editingId.value) {
        form.put(`/stok/${editingId.value}`, options);
        return;
    }

    form.post('/stok', options);
}

function destroy(item: ItemOption) {
    if (!canManage.value) return;

    if (confirm(`Hapus barang ${item.name}?`)) {
        router.delete(`/stok/${item.id}`, { preserveScroll: true });
    }
}

function statusLabel(item: ItemOption): string {
    return item.remaining <= 5 ? 'Menipis' : 'Aman';
}
</script>

<template>
    <Head title="Stock Barang" />

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
                        Master Stock Barang
                    </h1>
                    <span
                        class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]"
                    >
                        {{ items.length }} Item
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Monitoring ketersediaan perangkat, kuota minimum, dan
                    pelacakan unit serial number.
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
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95 disabled:pointer-events-none disabled:opacity-50"
                    :disabled="!canManage"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Barang
                </button>
            </div>
        </div>

        <!-- Quick Summary Stats -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div
                class="flex items-center gap-3 rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs"
            >
                <div class="rounded-xl bg-sky-500/10 p-2 text-sky-500">
                    <Boxes class="size-4" />
                </div>
                <div>
                    <p class="text-[11px] text-muted-foreground">Total SKU</p>
                    <p class="text-lg font-bold text-foreground">
                        {{ items.length }}
                    </p>
                </div>
            </div>
            <div
                class="flex items-center gap-3 rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs"
            >
                <div class="rounded-xl bg-emerald-500/10 p-2 text-emerald-500">
                    <Boxes class="size-4" />
                </div>
                <div>
                    <p class="text-[11px] text-muted-foreground">
                        Total Unit Sisa
                    </p>
                    <p class="text-lg font-bold text-foreground">
                        {{ totalRemaining.toLocaleString('id-ID') }}
                    </p>
                </div>
            </div>
            <div
                class="flex items-center gap-3 rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs"
            >
                <div class="rounded-xl bg-amber-500/10 p-2 text-amber-500">
                    <Boxes class="size-4" />
                </div>
                <div>
                    <p class="text-[11px] text-muted-foreground">
                        Stok Menipis
                    </p>
                    <p
                        class="text-lg font-bold text-amber-600 dark:text-amber-400"
                    >
                        {{ lowStockCount }}
                    </p>
                </div>
            </div>
            <div
                class="flex items-center gap-3 rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs"
            >
                <div class="rounded-xl bg-[#fb4f00]/10 p-2 text-[#fb4f00]">
                    <Boxes class="size-4" />
                </div>
                <div>
                    <p class="text-[11px] text-muted-foreground">
                        Serial Terdata
                    </p>
                    <p class="text-lg font-bold text-foreground">
                        {{ serials.length }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="!canManage"
            class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-xs text-amber-700 dark:text-amber-300"
        >
            Peran Teknisi memiliki izin pantau stock. Penambahan & edit item
            hanya dapat dilakukan oleh Admin dan SPV.
        </div>

        <!-- Stock per Item Table Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div
                class="flex flex-col gap-3 border-b border-border/70 p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-sm font-semibold text-foreground">
                        Daftar Stok per Barang
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        Pergerakan unit masuk, keluar, serta sisa kuota gudang
                    </p>
                </div>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <select
                        v-model="typeFilter"
                        class="rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition-colors hover:border-[#fb4f00]/40"
                    >
                        <option value="">Semua Jenis Item</option>
                        <option
                            v-for="option in itemTypes"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="relative sm:w-64">
                        <Search
                            class="pointer-events-none absolute top-2 left-3 size-3.5 text-muted-foreground"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari SKU / nama barang..."
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
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3 text-right">Masuk</th>
                            <th class="px-4 py-3 text-right">Keluar</th>
                            <th class="px-4 py-3 text-right">Sisa Stok</th>
                            <th class="px-4 py-3">Satuan</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="s in filtered"
                            :key="s.id"
                            class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]"
                        >
                            <td
                                class="px-4 py-3 font-mono font-medium whitespace-nowrap text-muted-foreground"
                            >
                                {{ s.sku }}
                            </td>
                            <td
                                class="min-w-[200px] px-4 py-3 font-semibold text-foreground"
                            >
                                {{ s.name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium"
                                >
                                    {{ s.type_label }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 text-right font-medium whitespace-nowrap text-emerald-600 dark:text-emerald-400"
                            >
                                +{{ s.stock_in }}
                            </td>
                            <td
                                class="px-4 py-3 text-right font-medium whitespace-nowrap text-[#fb4f00]"
                            >
                                -{{ s.stock_out }}
                            </td>
                            <td
                                class="px-4 py-3 text-right text-sm font-bold whitespace-nowrap text-foreground"
                            >
                                {{ s.remaining }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-muted-foreground"
                            >
                                {{ s.unit }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-muted-foreground"
                            >
                                {{ s.location ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        s.remaining <= 5
                                            ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400'
                                            : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                    "
                                >
                                    {{ statusLabel(s) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2 py-1 text-xs font-medium transition-colors hover:border-[#fb4f00]/40 hover:text-[#fb4f00] disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="openEdit(s)"
                                    >
                                        <Pencil class="size-3" /> Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-destructive/20 bg-background px-2 py-1 text-xs font-medium text-destructive transition-colors hover:bg-destructive/10 disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="destroy(s)"
                                    >
                                        <Trash2 class="size-3" /> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td
                                colspan="10"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Tidak ada data stock yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Serial Number Tracking Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div class="border-b border-border/70 p-4">
                <h2 class="text-sm font-semibold text-foreground">
                    Pelacakan Serial Number
                </h2>
                <p class="text-xs text-muted-foreground">
                    Detail status unit berserial (Tersedia di gudang / Telah
                    terpasang di pelanggan)
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3">Serial Number</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tgl Diterima</th>
                            <th class="px-4 py-3">
                                Posisi / Pelanggan Terpasang
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="sn in serials"
                            :key="sn.id"
                            class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]"
                        >
                            <td
                                class="px-4 py-3 font-mono font-semibold whitespace-nowrap text-foreground"
                            >
                                {{ sn.sn }}
                            </td>
                            <td
                                class="min-w-[200px] px-4 py-3 text-muted-foreground"
                            >
                                {{ sn.item_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        sn.status_value === 'available'
                                            ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                            : 'bg-slate-500/15 text-slate-600 dark:text-slate-400'
                                    "
                                >
                                    {{ sn.status }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 font-mono whitespace-nowrap text-muted-foreground"
                            >
                                {{ sn.received_at ?? '-' }}
                            </td>
                            <td class="min-w-[200px] px-4 py-3">
                                <span
                                    v-if="sn.status_value === 'issued'"
                                    class="font-medium text-foreground"
                                >
                                    {{ sn.customer_name ?? '-' }}
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded bg-muted/60 px-2 py-0.5 font-medium text-muted-foreground"
                                >
                                    Gudang Utama
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!serials.length">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Belum ada serial number tercatat.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
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
                class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border border-border bg-card shadow-2xl sm:max-w-xl sm:rounded-2xl"
            >
                <!-- Header with BLiP Accent -->
                <div
                    class="relative flex items-center justify-between border-b border-border/80 px-5 py-4"
                >
                    <div
                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#fb4f00] via-orange-400 to-[#fb4f00]"
                    ></div>
                    <div>
                        <h2 class="text-base font-bold text-foreground">
                            {{
                                editingId
                                    ? 'Edit Data Barang'
                                    : 'Tambah Barang Baru'
                            }}
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Lengkapi atribut SKU, kategori barang, dan batas
                            minimum stok gudang.
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
                        <div class="grid grid-cols-2 gap-3">
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Kode SKU
                                    <span class="text-destructive"
                                        >*</span
                                    ></label
                                >
                                <input
                                    v-model="form.sku"
                                    type="text"
                                    required
                                    placeholder="Contoh: RTR-001"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                                <p
                                    v-if="form.errors.sku"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.sku }}
                                </p>
                            </div>
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Jenis Item
                                    <span class="text-destructive"
                                        >*</span
                                    ></label
                                >
                                <select
                                    v-model="form.type"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                >
                                    <option
                                        v-for="option in itemTypes"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.type"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.type }}
                                </p>
                            </div>
                        </div>
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground"
                                >Nama Barang / Perangkat
                                <span class="text-destructive">*</span></label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Contoh: Router Mikrotik RB4011"
                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Satuan</label
                                >
                                <input
                                    v-model="form.unit"
                                    type="text"
                                    placeholder="unit / pcs / meter"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Lokasi Rak</label
                                >
                                <input
                                    v-model="form.location"
                                    type="text"
                                    placeholder="Rak A1 / Gudang"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                            </div>
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-semibold text-foreground"
                                    >Min. Stok</label
                                >
                                <input
                                    v-model.number="form.min_stock"
                                    type="number"
                                    min="0"
                                    class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground outline-hidden transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40"
                                />
                            </div>
                        </div>
                        <div
                            v-if="editingId"
                            class="rounded-xl border border-dashed border-border/80 bg-muted/30 p-3 text-xs text-muted-foreground"
                        >
                            Jumlah stok masuk, keluar, dan sisa dihitung secara
                            otomatis oleh sistem dari riwayat transaksi.
                        </div>
                        <div
                            v-if="form.type === 'serial'"
                            class="rounded-xl border border-dashed border-[#fb4f00]/30 bg-[#fb4f00]/5 p-3 text-xs text-muted-foreground"
                        >
                            <span class="font-semibold text-[#fb4f00]"
                                >Item Berserial:</span
                            >
                            nomor serial unit diinput secara dinamis pada saat
                            pencatatan transaksi barang masuk & keluar.
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
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : editingId
                                      ? 'Simpan Perubahan'
                                      : 'Simpan Barang'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
