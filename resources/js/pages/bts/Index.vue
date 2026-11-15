<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, RadioTower, Search, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data BTS/BSC', href: '/bts-bsc' },
        ],
    },
});

type BtsRow = {
    id: number;
    nama: string;
    alamat: string;
    status: string;
};

const props = defineProps<{
    btsList: BtsRow[];
    filters: { q: string };
    statuses: string[];
}>();

const { roleLabel, canManage } = useRole();
const showForm = ref(false);
const editingId = ref<number | null>(null);
const search = ref(props.filters.q ?? '');

const form = useForm({
    nama: '',
    alamat: '',
    status: 'Aktif',
});

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return props.btsList;
    }

    return props.btsList.filter((row) =>
        [row.nama, row.alamat, row.status, String(row.id)]
            .some((value) => value.toLowerCase().includes(term)),
    );
});

function openAdd() {
    if (!canManage.value) return;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.status = 'Aktif';
    showForm.value = true;
}

function openEdit(row: BtsRow) {
    if (!canManage.value) return;
    editingId.value = row.id;
    form.clearErrors();
    form.nama = row.nama;
    form.alamat = row.alamat;
    form.status = row.status;
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
        form.put(`/bts-bsc/${editingId.value}`, options);
        return;
    }

    form.post('/bts-bsc', options);
}

function destroy(row: BtsRow) {
    if (!canManage.value) return;

    if (confirm(`Hapus data ${row.nama}?`)) {
        router.delete(`/bts-bsc/${row.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Data BTS/BSC" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Data BTS/BSC</h1>
                <p class="text-sm text-muted-foreground">Kelola data BTS/BSC: identitas, alamat, dan status operasional.</p>
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
                    <Plus class="size-4" /> Tambah BTS/BSC
                </button>
            </div>
        </div>

        <div v-if="!canManage" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            Peran Teknisi hanya dapat melihat data BTS/BSC (tanpa tambah, edit, atau hapus).
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-primary/10 p-2 text-primary">
                        <RadioTower class="size-4" />
                    </div>
                    <h2 class="font-medium">Daftar BTS/BSC</h2>
                </div>
                <div class="relative w-full sm:w-64">
                    <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari ID / nama / alamat..."
                        class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm"
                    />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">ID</th>
                            <th class="px-4 py-2 font-medium">Nama BTS/BSC</th>
                            <th class="px-4 py-2 font-medium">Alamat</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="r in filtered" :key="r.id" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs whitespace-nowrap">{{ r.id }}</td>
                            <td class="px-4 py-2 min-w-[160px] font-medium">{{ r.nama }}</td>
                            <td class="px-4 py-2 min-w-[260px]">{{ r.alamat }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="r.status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-300'"
                                >
                                    {{ r.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40"
                                    :disabled="!canManage"
                                    @click="openEdit(r)"
                                >
                                    <Pencil class="size-3.5" /> Edit
                                </button>
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40"
                                    :disabled="!canManage"
                                    @click="destroy(r)"
                                >
                                    <Trash2 class="size-3.5" /> Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td colspan="5" class="px-4 py-6 text-center text-muted-foreground">Tidak ada data BTS/BSC.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Total {{ filtered.length }} data BTS/BSC.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-lg sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ editingId ? 'Edit BTS/BSC' : 'Tambah BTS/BSC' }}</h2>
                        <p class="text-xs text-muted-foreground">Hanya Admin dan SPV yang dapat menambah atau mengubah data.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">ID</label>
                            <input
                                :value="editingId ?? 'Otomatis'"
                                type="text"
                                readonly
                                class="rounded-md border bg-muted/40 px-3 py-2 text-sm"
                            />
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama BTS/BSC <span class="text-destructive">*</span></label>
                            <input v-model="form.nama" type="text" required placeholder="Contoh: BTS-MTR-01" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            <p v-if="form.errors.nama" class="text-xs text-destructive">{{ form.errors.nama }}</p>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Alamat <span class="text-destructive">*</span></label>
                            <textarea v-model="form.alamat" rows="3" required placeholder="Alamat lokasi BTS/BSC" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                            <p v-if="form.errors.alamat" class="text-xs text-destructive">{{ form.errors.alamat }}</p>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Status <span class="text-destructive">*</span></label>
                            <select v-model="form.status" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                            <p v-if="form.errors.status" class="text-xs text-destructive">{{ form.errors.status }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
