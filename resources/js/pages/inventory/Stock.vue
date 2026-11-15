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
    () => props.items.filter((item) => item.min_stock > 0 && item.remaining <= item.min_stock).length,
);

const totalRemaining = computed(() => props.items.reduce((total, item) => total + item.remaining, 0));

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
    <Head title="Stock" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Stock</h1>
                <p class="text-sm text-muted-foreground">Ringkasan stock per barang dan pelacakan serial number.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-md border bg-card px-2.5 py-1.5 text-xs text-muted-foreground">
                    Akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    :disabled="!canManage"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Stock
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <div class="rounded-lg border bg-card px-3 py-2 text-xs"><span class="text-muted-foreground">Total SKU:</span> <span class="font-semibold">{{ items.length }}</span></div>
            <div class="rounded-lg border bg-card px-3 py-2 text-xs"><span class="text-muted-foreground">Total unit tersisa:</span> <span class="font-semibold">{{ totalRemaining }}</span></div>
            <div class="rounded-lg border bg-card px-3 py-2 text-xs"><span class="text-muted-foreground">Stock menipis:</span> <span class="font-semibold text-amber-600">{{ lowStockCount }}</span></div>
            <div v-if="!canManage" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                Peran Teknisi hanya dapat melihat stock. Penambahan & edit stock hanya untuk Admin dan SPV.
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium">Stock per Barang</h2>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <select v-model="typeFilter" class="rounded-md border bg-background px-3 py-2 text-sm">
                        <option value="">Semua jenis</option>
                        <option v-for="option in itemTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>
                    <div class="relative sm:w-64">
                        <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                        <input v-model="search" type="text" placeholder="Cari SKU / nama barang..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
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
                        <tr v-for="s in filtered" :key="s.id" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs whitespace-nowrap">{{ s.sku }}</td>
                            <td class="px-4 py-2 min-w-[180px] font-medium">{{ s.name }}</td>
                            <td class="px-4 py-2"><span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ s.type_label }}</span></td>
                            <td class="px-4 py-2 text-right text-emerald-600">{{ s.stock_in }}</td>
                            <td class="px-4 py-2 text-right text-amber-600">{{ s.stock_out }}</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ s.remaining }}</td>
                            <td class="px-4 py-2">{{ s.unit }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ s.location ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="s.remaining <= 5 ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'"
                                >
                                    {{ statusLabel(s) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40"
                                    :disabled="!canManage"
                                    @click="openEdit(s)"
                                >
                                    <Pencil class="size-3.5" /> Edit
                                </button>
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40"
                                    :disabled="!canManage"
                                    @click="destroy(s)"
                                >
                                    <Trash2 class="size-3.5" /> Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td colspan="10" class="px-4 py-6 text-center text-muted-foreground">Tidak ada data stock.</td>
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
                        <tr v-for="sn in serials" :key="sn.id" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs font-medium">{{ sn.sn }}</td>
                            <td class="px-4 py-2 min-w-[180px]">{{ sn.item_name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="sn.status_value === 'available' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300'"
                                >
                                    {{ sn.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ sn.received_at ?? '-' }}</td>
                            <td class="px-4 py-2 min-w-[200px]">{{ sn.status_value === 'issued' ? (sn.customer_name ?? '-') : 'Gudang' }}</td>
                        </tr>
                        <tr v-if="!serials.length">
                            <td colspan="5" class="px-4 py-6 text-center text-muted-foreground">Belum ada serial number tercatat.</td>
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
                        <h2 class="font-medium">{{ editingId ? 'Edit Stock' : 'Tambah Stock' }}</h2>
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
                                <p v-if="form.errors.sku" class="text-xs text-destructive">{{ form.errors.sku }}</p>
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Jenis <span class="text-destructive">*</span></label>
                                <select v-model="form.type" class="rounded-md border bg-background px-3 py-2 text-sm">
                                    <option v-for="option in itemTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>
                                <p v-if="form.errors.type" class="text-xs text-destructive">{{ form.errors.type }}</p>
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama Barang <span class="text-destructive">*</span></label>
                            <input v-model="form.name" type="text" required placeholder="Nama barang" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Satuan</label>
                                <input v-model="form.unit" type="text" placeholder="unit / pcs / box" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Lokasi</label>
                                <input v-model="form.location" type="text" placeholder="Rak / Gudang" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="grid gap-1">
                                <label class="text-xs font-medium text-muted-foreground">Min. Stock</label>
                                <input v-model.number="form.min_stock" type="number" min="0" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div v-if="editingId" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                            Total masuk, keluar, dan sisa stock dihitung otomatis dari transaksi barang masuk/keluar.
                        </div>
                        <div v-if="form.type === 'serial'" class="rounded-md border border-dashed p-3 text-xs text-muted-foreground">
                            Barang berserial: detail serial number dikelola melalui transaksi barang masuk/keluar.
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Stock') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
