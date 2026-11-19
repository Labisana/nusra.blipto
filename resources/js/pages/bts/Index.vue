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
        [row.nama, row.alamat, row.status, String(row.id)].some((value) =>
            value.toLowerCase().includes(term),
        ),
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

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <div class="flex items-center gap-2">
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        Data BTS / BSC
                    </h1>
                    <span
                        class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]"
                    >
                        {{ btsList.length }} Site
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Kelola data BTS/BSC: identitas, alamat, dan status
                    operasional jaringan BLiP.
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
                    <Plus class="size-4" /> Tambah BTS/BSC
                </button>
            </div>
        </div>

        <div
            v-if="!canManage"
            class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-xs text-amber-700 dark:text-amber-300"
        >
            Peran Teknisi hanya dapat melihat data BTS/BSC (tanpa tambah, edit,
            atau hapus).
        </div>

        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div
                class="flex flex-col gap-3 border-b border-border/70 p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-2.5">
                    <div class="rounded-xl bg-[#fb4f00]/10 p-2 text-[#fb4f00]">
                        <RadioTower class="size-4" />
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-foreground">
                            Daftar Titik BTS/BSC
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Distribusi node transmisi radio & optik
                        </p>
                    </div>
                </div>
                <div class="relative w-full sm:w-72">
                    <Search
                        class="pointer-events-none absolute top-2 left-3 size-3.5 text-muted-foreground"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari ID, nama site, alamat..."
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
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nama Site BTS/BSC</th>
                            <th class="px-4 py-3">Alamat Lokasi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr
                            v-for="r in filtered"
                            :key="r.id"
                            class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]"
                        >
                            <td
                                class="px-4 py-3 font-mono font-medium whitespace-nowrap text-muted-foreground"
                            >
                                {{ r.id }}
                            </td>
                            <td
                                class="min-w-[180px] px-4 py-3 font-bold text-foreground"
                            >
                                {{ r.nama }}
                            </td>
                            <td
                                class="min-w-[260px] px-4 py-3 text-muted-foreground"
                            >
                                {{ r.alamat }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        r.status === 'Aktif'
                                            ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                            : 'bg-slate-500/15 text-slate-600 dark:text-slate-400'
                                    "
                                >
                                    {{ r.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2 py-1 text-xs font-medium transition-colors hover:border-[#fb4f00]/40 hover:text-[#fb4f00] disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="openEdit(r)"
                                    >
                                        <Pencil class="size-3" /> Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-destructive/20 bg-background px-2 py-1 text-xs font-medium text-destructive transition-colors hover:bg-destructive/10 disabled:opacity-40"
                                        :disabled="!canManage"
                                        @click="destroy(r)"
                                    >
                                        <Trash2 class="size-3" /> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Tidak ada data BTS/BSC yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="border-t border-border/70 px-4 py-2.5 text-xs text-muted-foreground"
            >
                Total {{ filtered.length }} data BTS/BSC.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
        >
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div
                class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-lg sm:rounded-xl"
            >
                <div
                    class="flex items-center justify-between border-b px-4 py-3"
                >
                    <div>
                        <h2 class="font-medium">
                            {{ editingId ? 'Edit BTS/BSC' : 'Tambah BTS/BSC' }}
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Hanya Admin dan SPV yang dapat menambah atau
                            mengubah data.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-md p-1.5 hover:bg-muted"
                        @click="closeForm"
                        aria-label="Tutup"
                    >
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid gap-1">
                            <label
                                class="text-xs font-medium text-muted-foreground"
                                >ID</label
                            >
                            <input
                                :value="editingId ?? 'Otomatis'"
                                type="text"
                                readonly
                                class="rounded-md border bg-muted/40 px-3 py-2 text-sm"
                            />
                        </div>
                        <div class="grid gap-1">
                            <label
                                class="text-xs font-medium text-muted-foreground"
                                >Nama BTS/BSC
                                <span class="text-destructive">*</span></label
                            >
                            <input
                                v-model="form.nama"
                                type="text"
                                required
                                placeholder="Contoh: BTS-MTR-01"
                                class="rounded-md border bg-background px-3 py-2 text-sm"
                            />
                            <p
                                v-if="form.errors.nama"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.nama }}
                            </p>
                        </div>
                        <div class="grid gap-1">
                            <label
                                class="text-xs font-medium text-muted-foreground"
                                >Alamat
                                <span class="text-destructive">*</span></label
                            >
                            <textarea
                                v-model="form.alamat"
                                rows="3"
                                required
                                placeholder="Alamat lokasi BTS/BSC"
                                class="rounded-md border bg-background px-3 py-2 text-sm"
                            ></textarea>
                            <p
                                v-if="form.errors.alamat"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.alamat }}
                            </p>
                        </div>
                        <div class="grid gap-1">
                            <label
                                class="text-xs font-medium text-muted-foreground"
                                >Status
                                <span class="text-destructive">*</span></label
                            >
                            <select
                                v-model="form.status"
                                required
                                class="rounded-md border bg-background px-3 py-2 text-sm"
                            >
                                <option
                                    v-for="status in statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ status }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
                            @click="closeForm"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : editingId
                                      ? 'Simpan Perubahan'
                                      : 'Simpan'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
