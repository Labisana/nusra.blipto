<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Search, ShieldCheck, Trash2, UserCog, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Pengguna', href: '/pengguna' },
        ],
    },
});

type RoleOption = {
    value: string;
    label: string;
};

type UserRow = {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    email_verified_at: string | null;
    created_at: string | null;
    is_self: boolean;
};

const props = defineProps<{
    users: UserRow[];
    roles: RoleOption[];
    filters: { q: string };
}>();

const { roleLabel, canManageUsers } = useRole();
const showForm = ref(false);
const editingId = ref<number | null>(null);
const search = ref(props.filters.q ?? '');

const form = useForm({
    name: '',
    email: '',
    role: 'teknisi',
    password: '',
});

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return props.users;
    }

    return props.users.filter((row) =>
        [row.name, row.email, row.role_label].some((value) => value.toLowerCase().includes(term)),
    );
});

const roleCounts = computed(() =>
    props.roles.map((role) => ({
        ...role,
        total: props.users.filter((user) => user.role === role.value).length,
    })),
);

function roleBadgeClass(role: string): string {
    switch (role) {
        case 'spv':
            return 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300';
        case 'admin':
            return 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300';
        default:
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300';
    }
}

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function openAdd() {
    if (!canManageUsers.value) return;

    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.role = 'teknisi';
    showForm.value = true;
}

function openEdit(row: UserRow) {
    if (!canManageUsers.value) return;

    editingId.value = row.id;
    form.clearErrors();
    form.name = row.name;
    form.email = row.email;
    form.role = row.role;
    form.password = '';
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
        form.put(`/pengguna/${editingId.value}`, options);
        return;
    }

    form.post('/pengguna', options);
}

function destroy(row: UserRow) {
    if (!canManageUsers.value || row.is_self) return;

    if (confirm(`Hapus pengguna ${row.name}?`)) {
        router.delete(`/pengguna/${row.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Pengguna" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Pengguna</h1>
                <p class="text-sm text-muted-foreground">Kelola akun dan peran pengguna aplikasi gudang.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-md border bg-card px-2.5 py-1.5 text-xs text-muted-foreground">
                    Akses: <span class="font-medium text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    :disabled="!canManageUsers"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Pengguna
                </button>
            </div>
        </div>

        <div v-if="!canManageUsers" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            Hanya SPV yang dapat menambah, mengubah, atau menghapus pengguna.
        </div>

        <div class="flex flex-wrap gap-2">
            <div class="rounded-lg border bg-card px-3 py-2 text-xs">
                <span class="text-muted-foreground">Total pengguna:</span>
                <span class="font-semibold">{{ users.length }}</span>
            </div>
            <div
                v-for="role in roleCounts"
                :key="role.value"
                class="rounded-lg border bg-card px-3 py-2 text-xs"
            >
                <span class="text-muted-foreground">{{ role.label }}:</span>
                <span class="font-semibold">{{ role.total }}</span>
            </div>
        </div>

        <div class="rounded-xl border bg-card shadow-sm">
            <div class="flex flex-col gap-2 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-primary/10 p-2 text-primary">
                        <UserCog class="size-4" />
                    </div>
                    <h2 class="font-medium">Daftar Pengguna</h2>
                </div>
                <div class="relative w-full sm:w-64">
                    <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama / email / peran..."
                        class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm"
                    />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-xs text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2 font-medium">ID</th>
                            <th class="px-4 py-2 font-medium">Nama</th>
                            <th class="px-4 py-2 font-medium">Email</th>
                            <th class="px-4 py-2 font-medium">Peran</th>
                            <th class="px-4 py-2 font-medium">Dibuat</th>
                            <th class="px-4 py-2 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="row in filtered" :key="row.id" class="hover:bg-muted/30">
                            <td class="px-4 py-2 font-mono text-xs whitespace-nowrap">{{ row.id }}</td>
                            <td class="px-4 py-2 min-w-[160px] font-medium">
                                {{ row.name }}
                                <span
                                    v-if="row.is_self"
                                    class="ml-1 inline-flex items-center gap-1 rounded-full border px-1.5 py-0.5 text-[10px] text-muted-foreground"
                                >
                                    <ShieldCheck class="size-3" /> Anda
                                </span>
                            </td>
                            <td class="px-4 py-2 min-w-[200px] font-mono text-xs">{{ row.email }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="roleBadgeClass(row.role)"
                                >
                                    {{ row.role_label }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-muted-foreground">{{ formatDate(row.created_at) }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs hover:bg-muted disabled:opacity-40"
                                    :disabled="!canManageUsers"
                                    @click="openEdit(row)"
                                >
                                    <Pencil class="size-3.5" /> Edit
                                </button>
                                <button
                                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs text-destructive hover:bg-destructive/10 disabled:opacity-40"
                                    :disabled="!canManageUsers || row.is_self"
                                    :title="row.is_self ? 'Akun sendiri tidak dapat dihapus' : 'Hapus pengguna'"
                                    @click="destroy(row)"
                                >
                                    <Trash2 class="size-3.5" /> Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td colspan="6" class="px-4 py-6 text-center text-muted-foreground">Tidak ada pengguna yang cocok.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t px-4 py-2 text-xs text-muted-foreground">
                Menampilkan {{ filtered.length }} dari {{ users.length }} pengguna.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-lg sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                        <p class="text-xs text-muted-foreground">Atur identitas dan peran akses pengguna.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-3">
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Nama <span class="text-destructive">*</span></label>
                            <input v-model="form.name" type="text" required placeholder="Nama lengkap" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Email <span class="text-destructive">*</span></label>
                            <input v-model="form.email" type="email" required placeholder="nama@nusra.test" class="rounded-md border bg-background px-3 py-2 text-sm" />
                            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">Peran <span class="text-destructive">*</span></label>
                            <select v-model="form.role" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                <option v-for="role in roles" :key="role.value" :value="role.value">{{ role.label }}</option>
                            </select>
                            <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
                        </div>
                        <div class="grid gap-1">
                            <label class="text-xs font-medium text-muted-foreground">
                                Password
                                <span v-if="!editingId" class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                :required="!editingId"
                                minlength="8"
                                autocomplete="new-password"
                                :placeholder="editingId ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter'"
                                class="rounded-md border bg-background px-3 py-2 text-sm"
                            />
                            <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                            <p class="text-[11px] text-muted-foreground">
                                {{ editingId ? 'Isi hanya jika password ingin diganti.' : 'Password minimal 8 karakter.' }}
                            </p>
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
