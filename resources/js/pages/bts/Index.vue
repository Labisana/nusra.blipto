<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, RadioTower, Search, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
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
    status: 'Aktif' | 'Non Aktif';
};

const { role, canManage } = useRole();
const showForm = ref(false);
const formMode = ref<'add' | 'edit'>('add');

const rows = ref<BtsRow[]>([
    { id: 1, nama: 'BTS-MTR-01', alamat: 'Jl. Pejanggik No. 10, Mataram', status: 'Aktif' },
    { id: 2, nama: 'BTS-MTR-02', alamat: 'Jl. Selaparang No. 22, Cakranegara', status: 'Aktif' },
    { id: 3, nama: 'BTS-PRA-03', alamat: 'Jl. Gajah Mada No. 5, Praya', status: 'Aktif' },
    { id: 4, nama: 'BSC-LOM-01', alamat: 'Jl. Raya Senggigi, Lombok Barat', status: 'Non Aktif' },
]);

const form = ref<BtsRow>({
    id: 0,
    nama: '',
    alamat: '',
    status: 'Aktif',
});

function openAdd() {
    if (!canManage.value) return;
    formMode.value = 'add';
    form.value = { id: 0, nama: '', alamat: '', status: 'Aktif' };
    showForm.value = true;
}

function openEdit(row: BtsRow) {
    if (!canManage.value) return;
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
    <Head title="Data BTS/BSC" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Data BTS/BSC</h1>
                <p class="text-sm text-muted-foreground">Kelola data BTS/BSC: identitas, alamat, dan status operasional.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <label class="flex items-center gap-2 text-sm">
                    <span class="text-muted-foreground">Simulasi peran</span>
                    <select v-model="role" class="rounded-md border bg-background px-2 py-1.5 text-sm">
                        <option value="spv">SPV (Full)</option>
                        <option value="admin">Admin (Add/Edit/Delete)</option>
                        <option value="teknisi">Teknisi (Lihat saja)</option>
                    </select>
                </label>
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
                    <input type="text" placeholder="Cari ID / nama / alamat..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
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
                        <tr v-for="r in rows" :key="r.id" class="hover:bg-muted/30">
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
                                >
                                    <Trash2 class="size-3.5" /> Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Total {{ rows.length }} data BTS/BSC.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-lg sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ formMode === 'add' ? 'Tambah BTS/BSC' : 'Edit BTS/BSC' }}</h2>
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
                                :value="formMode === 'edit' ? form.id : 'Otomatis'"
                                type="text"
                                readonly
                                class="rounded-md border bg-muted/40 px-3 py-2 text-sm"
                            />
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama BTS/BSC <span class="text-destructive">*</span></label>
                            <input v-model="form.nama" type="text" required placeholder="Contoh: BTS-MTR-01" class="rounded-md border bg-background px-3 py-2 text-sm" />
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Alamat <span class="text-destructive">*</span></label>
                            <textarea v-model="form.alamat" rows="3" required placeholder="Alamat lokasi BTS/BSC" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Status <span class="text-destructive">*</span></label>
                            <select v-model="form.status" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option value="Aktif">Aktif</option>
                                <option value="Non Aktif">Non Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
                            {{ formMode === 'add' ? 'Simpan' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
