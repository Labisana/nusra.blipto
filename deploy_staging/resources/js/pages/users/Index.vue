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
            return 'bg-purple-500/15 text-purple-600 dark:text-purple-300 border border-purple-500/20';
        case 'admin':
            return 'bg-[#fb4f00]/15 text-[#fb4f00] dark:text-[#ff6b26] border border-[#fb4f00]/20 font-bold';
        default:
            return 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 border border-emerald-500/20';
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
    <Head title="Manajemen Pengguna" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Pengelolaan Pengguna</h1>
                    <span class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]">
                        {{ users.length }} Akun
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">Kelola hak akses dan akun staf gudang, teknisi lapangan, dan supervisor.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="rounded-xl border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-2xs">
                    Peran: <span class="font-semibold text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95 disabled:pointer-events-none disabled:opacity-50"
                    :disabled="!canManageUsers"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Pengguna
                </button>
            </div>
        </div>

        <div v-if="!canManageUsers" class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-xs text-amber-700 dark:text-amber-300">
            Hanya SPV yang memiliki otoritas untuk menambah, mengubah, atau menghapus akun pengguna.
        </div>

        <!-- Role Badges Summary -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs">
                <p class="text-[11px] text-muted-foreground">Total Pengguna</p>
                <p class="text-lg font-bold text-foreground">{{ users.length }}</p>
            </div>
            <div
                v-for="r in roleCounts"
                :key="r.value"
                class="rounded-2xl border border-border/80 bg-card p-3.5 shadow-2xs"
            >
                <p class="text-[11px] text-muted-foreground">{{ r.label }}</p>
                <p class="text-lg font-bold text-foreground">{{ r.total }}</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-2xs">
            <div class="flex flex-col gap-3 border-b border-border/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="rounded-xl bg-[#fb4f00]/10 p-2 text-[#fb4f00]">
                        <UserCog class="size-4" />
                    </div>
                    <div>
                        <h2 class="font-semibold text-foreground text-sm">Daftar Akun Pengguna</h2>
                        <p class="text-xs text-muted-foreground">Otentikasi dan privilege role sistem inventaris BLiP</p>
                    </div>
                </div>
                <div class="relative w-full sm:w-72">
                    <Search class="pointer-events-none absolute left-3 top-2 size-3.5 text-muted-foreground" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, email, peran..."
                        class="w-full rounded-xl border border-border bg-background py-1.5 pl-8.5 pr-8 text-xs transition-colors hover:border-[#fb4f00]/40 focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden"
                    />
                    <button v-if="search" class="absolute right-2.5 top-2 text-muted-foreground hover:text-foreground" @click="search = ''">
                        <X class="size-3.5" />
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border/60 bg-muted/40 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Peran Akses</th>
                            <th class="px-4 py-3">Tgl Bergabung</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-xs">
                        <tr v-for="row in filtered" :key="row.id" class="transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.05]">
                            <td class="px-4 py-3 font-mono font-medium text-muted-foreground whitespace-nowrap">{{ row.id }}</td>
                            <td class="px-4 py-3 min-w-[180px] font-bold text-foreground">
                                {{ row.name }}
                                <span
                                    v-if="row.is_self"
                                    class="ml-1.5 inline-flex items-center gap-1 rounded-full bg-[#fb4f00]/10 border border-[#fb4f00]/20 px-2 py-0.5 text-[10px] font-semibold text-[#fb4f00]"
                                >
                                    <ShieldCheck class="size-3" /> Akun Anda
                                </span>
                            </td>
                            <td class="px-4 py-3 min-w-[200px] font-mono text-xs text-muted-foreground">{{ row.email }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                                    :class="roleBadgeClass(row.role)"
                                >
                                    {{ row.role_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">{{ formatDate(row.created_at) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2 py-1 text-xs font-medium hover:border-[#fb4f00]/40 hover:text-[#fb4f00] transition-colors disabled:opacity-40"
                                        :disabled="!canManageUsers"
                                        @click="openEdit(row)"
                                    >
                                        <Pencil class="size-3" /> Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 rounded-lg border border-destructive/20 bg-background px-2 py-1 text-xs font-medium text-destructive hover:bg-destructive/10 transition-colors disabled:opacity-40"
                                        :disabled="!canManageUsers || row.is_self"
                                        :title="row.is_self ? 'Akun sendiri tidak dapat dihapus' : 'Hapus pengguna'"
                                        @click="destroy(row)"
                                    >
                                        <Trash2 class="size-3" /> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Tidak ada pengguna yang cocok dengan pencarian.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-border/70 px-4 py-2.5 text-xs text-muted-foreground">
                Menampilkan {{ filtered.length }} dari {{ users.length }} akun pengguna sistem.
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border border-border bg-card shadow-2xl sm:max-w-lg sm:rounded-2xl">
                <div class="relative flex items-center justify-between border-b border-border/80 px-5 py-4">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#fb4f00] via-orange-400 to-[#fb4f00]"></div>
                    <div>
                        <h2 class="font-bold text-foreground text-base">{{ editingId ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}</h2>
                        <p class="text-xs text-muted-foreground">Tentukan kredensial login dan wewenang peran di sistem.</p>
                    </div>
                    <button type="button" class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-5" @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground">Nama Lengkap <span class="text-destructive">*</span></label>
                            <input v-model="form.name" type="text" required placeholder="Nama lengkap petugas" class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground">Alamat Email <span class="text-destructive">*</span></label>
                            <input v-model="form.email" type="email" required placeholder="petugas@nusra.blipto.com" class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground">Peran / Hak Akses <span class="text-destructive">*</span></label>
                            <select v-model="form.role" required class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden">
                                <option v-for="role in roles" :key="role.value" :value="role.value">{{ role.label }}</option>
                            </select>
                            <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <label class="text-xs font-semibold text-foreground">
                                Kata Sandi
                                <span v-if="!editingId" class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                :required="!editingId"
                                minlength="8"
                                autocomplete="new-password"
                                :placeholder="editingId ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter'"
                                class="rounded-xl border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden"
                            />
                            <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                            <p class="text-[11px] text-muted-foreground">
                                {{ editingId ? 'Isi hanya jika password pengguna ingin diganti.' : 'Password minimal 8 karakter aman.' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col-reverse gap-2 border-t border-border/70 pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted transition-colors" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Pengguna') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
