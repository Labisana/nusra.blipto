<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp, Pencil, Plus, Search, Trash2, UserRound, X } from '@lucide/vue';
import { ref, watch } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Pelanggan', href: '/pelanggan' },
        ],
    },
});

type Customer = Record<string, string | number>;

const { role, canManage } = useRole();
const expanded = ref<number | null>(1);
const showForm = ref(false);
const formMode = ref<'add' | 'edit'>('add');

const btsOptions = [
    { id: 1, nama: 'BTS-MTR-01', status: 'Aktif' },
    { id: 2, nama: 'BTS-MTR-02', status: 'Aktif' },
    { id: 3, nama: 'BTS-PRA-03', status: 'Aktif' },
    { id: 4, nama: 'BSC-LOM-01', status: 'Non Aktif' },
];

const tipePelangganOptions = ['Busol', 'Broadband'];
const statusOptions = ['Aktif', 'Suspend', 'Non Aktif'];

function visitByKlasifikasi(klasifikasi: string | number | undefined): string {
    return String(klasifikasi ?? '').trim().toLowerCase() === 'platinum' ? '/1Bulan' : '/3Bulan';
}

const customers = ref<Customer[]>([
    {
        id: 1,
        Cabang: 'Mataram',
        Item: 'Internet Dedicated',
        'Tipe Pelanggan': 'Busol',
        CID: 'C-001',
        SID: 'S-1001',
        Status: 'Aktif',
        'Nama Pelanggan': 'PT Nusra Net - Cab. Mataram',
        AREA: 'Mataram Kota',
        Sales: 'Rizky',
        Alamat: 'Jl. Pejanggik No. 10, Mataram',
        Koordinat: '-8.5833, 116.1167',
        PIC: 'Ahmad Fauzi',
        Kontak: '0812-3456-7001',
        Email: 'noc.mataram@nusranet.id',
        Lastmile: 'Fiber Optic',
        Produk: 'Metro-E',
        'Bandwith Main': '100 Mbps',
        'Bandwith 2nd': '50 Mbps',
        'Bandwith 3rd': '-',
        Service: 'Managed Service',
        Aplikasi: 'ERP, CCTV',
        'Main LInk': 'FO-MTR-01',
        'Backup Link': 'Radio-MTR-02',
        Segment: 'Enterprise',
        'Klasifikasi Pelanggan': 'Gold',
        Visit: '/3Bulan',
        'BTS/BSC': 'BTS-MTR-01',
        'Jenis Router': 'Mikrotik CCR1009',
        'ONU/ONT': 'ZTE F609 (SN: F609-A01)',
        ODP: 'ODP-MTR-010',
        'PON Port': '1/1/8',
        'Radio Station': '-',
        'AP Sektor': '-',
        'Jml AP': '0',
        'Merk AP': '-',
        'Tipe AP': '-',
    },
    {
        id: 2,
        Cabang: 'Praya',
        Item: 'Internet Broadband',
        'Tipe Pelanggan': 'Broadband',
        CID: 'C-002',
        SID: 'S-1002',
        Status: 'Aktif',
        'Nama Pelanggan': 'PT Nusra Net - Cab. Praya',
        AREA: 'Lombok Tengah',
        Sales: 'Sinta',
        Alamat: 'Jl. Gajah Mada No. 5, Praya',
        Koordinat: '-8.7054, 116.2704',
        PIC: 'Lalu Hendra',
        Kontak: '0813-9988-1122',
        Email: 'noc.praya@nusranet.id',
        Lastmile: 'Wireless',
        Produk: 'Broadband',
        'Bandwith Main': '50 Mbps',
        'Bandwith 2nd': '-',
        'Bandwith 3rd': '-',
        Service: 'Best Effort',
        Aplikasi: 'POS',
        'Main LInk': 'RAD-PRA-01',
        'Backup Link': '-',
        Segment: 'SME',
        'Klasifikasi Pelanggan': 'Silver',
        Visit: '/3Bulan',
        'BTS/BSC': 'BTS-PRA-03',
        'Jenis Router': 'Mikrotik RB750',
        'ONU/ONT': '-',
        ODP: '-',
        'PON Port': '-',
        'Radio Station': 'RS-PRA-01',
        'AP Sektor': 'Sektor 2',
        'Jml AP': '2',
        'Merk AP': 'Ubiquiti',
        'Tipe AP': 'LiteBeam AC',
    },
    {
        id: 3,
        Cabang: 'Mataram',
        Item: 'VPN + Internet',
        'Tipe Pelanggan': 'Busol',
        CID: 'C-003',
        SID: 'S-1003',
        Status: 'Suspend',
        'Nama Pelanggan': 'CV Sinar Lombok',
        AREA: 'Cakranegara',
        Sales: 'Rizky',
        Alamat: 'Jl. Selaparang No. 22, Cakranegara',
        Koordinat: '-8.5889, 116.1300',
        PIC: 'Baiq Nur',
        Kontak: '0877-2200-3344',
        Email: 'it@sinar-lombok.co.id',
        Lastmile: 'Fiber Optic',
        Produk: 'L2VPN',
        'Bandwith Main': '20 Mbps',
        'Bandwith 2nd': '-',
        'Bandwith 3rd': '-',
        Service: 'Managed Service',
        Aplikasi: 'VPN Kantor',
        'Main LInk': 'FO-CKR-04',
        'Backup Link': '-',
        Segment: 'SOHO',
        'Klasifikasi Pelanggan': 'Bronze',
        Visit: '/3Bulan',
        'BTS/BSC': 'BTS-MTR-02',
        'Jenis Router': 'Mikrotik RB450',
        'ONU/ONT': 'ZTE F609 (SN: F609-A11)',
        ODP: 'ODP-CKR-004',
        'PON Port': '1/2/3',
        'Radio Station': '-',
        'AP Sektor': '-',
        'Jml AP': '0',
        'Merk AP': '-',
        'Tipe AP': '-',
    },
]);

const fields: [string, string][] = [
    ['id', 'ID'],
    ['Cabang', 'Cabang'],
    ['Item', 'Item'],
    ['Tipe Pelanggan', 'Tipe Pelanggan'],
    ['CID', 'CID'],
    ['SID', 'SID'],
    ['Status', 'Status'],
    ['Nama Pelanggan', 'Nama Pelanggan'],
    ['AREA', 'Area'],
    ['Sales', 'Sales'],
    ['Alamat', 'Alamat'],
    ['Koordinat', 'Koordinat'],
    ['PIC', 'PIC'],
    ['Kontak', 'Kontak'],
    ['Email', 'Email'],
    ['Lastmile', 'Lastmile'],
    ['Produk', 'Produk'],
    ['Bandwith Main', 'Bandwidth Main'],
    ['Bandwith 2nd', 'Bandwidth 2nd'],
    ['Bandwith 3rd', 'Bandwidth 3rd'],
    ['Service', 'Service'],
    ['Aplikasi', 'Aplikasi'],
    ['Main LInk', 'Main Link'],
    ['Backup Link', 'Backup Link'],
    ['Segment', 'Segment'],
    ['Klasifikasi Pelanggan', 'Klasifikasi'],
    ['Visit', 'Visit'],
    ['BTS/BSC', 'BTS/BSC'],
    ['Jenis Router', 'Jenis Router'],
    ['ONU/ONT', 'ONU/ONT'],
    ['ODP', 'ODP'],
    ['PON Port', 'PON Port'],
    ['Radio Station', 'Radio Station'],
    ['AP Sektor', 'AP Sektor'],
    ['Jml AP', 'Jml AP'],
    ['Merk AP', 'Merk AP'],
    ['Tipe AP', 'Tipe AP'],
];

const textFields: [string, string, string?][] = [
    ['Cabang', 'Cabang'],
    ['Item', 'Item'],
    ['CID', 'CID'],
    ['SID', 'SID'],
    ['Nama Pelanggan', 'Nama Pelanggan'],
    ['AREA', 'Area'],
    ['Sales', 'Sales'],
    ['Koordinat', 'Koordinat'],
    ['PIC', 'PIC'],
    ['Kontak', 'Kontak'],
    ['Email', 'Email', 'email'],
    ['Lastmile', 'Lastmile'],
    ['Produk', 'Produk'],
    ['Bandwith Main', 'Bandwidth Main'],
    ['Bandwith 2nd', 'Bandwidth 2nd'],
    ['Bandwith 3rd', 'Bandwidth 3rd'],
    ['Service', 'Service'],
    ['Aplikasi', 'Aplikasi'],
    ['Main LInk', 'Main Link'],
    ['Backup Link', 'Backup Link'],
    ['Segment', 'Segment'],
    ['Klasifikasi Pelanggan', 'Klasifikasi'],
    ['Visit', 'Visit'],
    ['Jenis Router', 'Jenis Router'],
    ['ONU/ONT', 'ONU/ONT'],
    ['ODP', 'ODP'],
    ['PON Port', 'PON Port'],
    ['Radio Station', 'Radio Station'],
    ['AP Sektor', 'AP Sektor'],
    ['Jml AP', 'Jml AP'],
    ['Merk AP', 'Merk AP'],
    ['Tipe AP', 'Tipe AP'],
];

function emptyCustomer(): Customer {
    const c: Customer = { id: 0 };
    for (const [key] of fields) {
        if (key === 'id') continue;
        c[key] = '';
    }
    c['Tipe Pelanggan'] = 'Busol';
    c['Status'] = 'Aktif';
    c['BTS/BSC'] = '';
    c['Alamat'] = '';
    c['Klasifikasi Pelanggan'] = '';
    c.Visit = visitByKlasifikasi('');
    return c;
}

const form = ref<Customer>(emptyCustomer());

watch(
    () => form.value['Klasifikasi Pelanggan'],
    (val) => {
        form.value.Visit = visitByKlasifikasi(val);
    },
);

function toggle(id: number) {
    expanded.value = expanded.value === id ? null : id;
}

function openAdd() {
    if (!canManage.value) return;
    formMode.value = 'add';
    form.value = emptyCustomer();
    showForm.value = true;
}

function openEdit(c: Customer) {
    if (!canManage.value) return;
    formMode.value = 'edit';
    form.value = { ...c, Visit: visitByKlasifikasi(c['Klasifikasi Pelanggan']) };
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
    <Head title="Pelanggan" />

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">Data Pelanggan</h1>
                <p class="text-sm text-muted-foreground">
                    Daftar pelanggan untuk relasi barang masuk & barang keluar. Menyimpan data identitas, layanan, dan perangkat.
                </p>
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
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    :disabled="!canManage"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Pelanggan
                </button>
            </div>
        </div>

        <div v-if="!canManage" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            Peran Teknisi hanya dapat melihat data pelanggan (tanpa tambah, edit, atau hapus).
        </div>

        <div class="flex flex-col gap-3 rounded-xl border bg-card p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-md">
                <Search class="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                <input type="text" placeholder="Cari nama, CID, SID, cabang, area..." class="w-full rounded-md border bg-background py-2 pl-8 pr-3 text-sm" />
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Aktif: 2</span>
                <span class="rounded-full bg-amber-50 px-2.5 py-1 font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Suspend: 1</span>
                <span class="rounded-full border px-2.5 py-1 text-muted-foreground">Total: {{ customers.length }}</span>
            </div>
        </div>

        <div class="grid gap-3">
            <div
                v-for="c in customers"
                :key="c.id as number"
                class="overflow-hidden rounded-xl border bg-card shadow-sm"
            >
                <button
                    class="flex w-full items-start gap-3 p-4 text-left hover:bg-muted/30"
                    @click="toggle(c.id as number)"
                >
                    <div class="mt-0.5 rounded-lg bg-primary/10 p-2 text-primary">
                        <UserRound class="size-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-medium">{{ c['Nama Pelanggan'] }}</h2>
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="c.Status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'"
                            >{{ c.Status }}</span>
                            <span class="inline-flex rounded-full border px-2 py-0.5 text-xs">{{ c['Klasifikasi Pelanggan'] }}</span>
                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-xs text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-400">{{ c['Tipe Pelanggan'] }}</span>
                        </div>
                        <p class="mt-1 text-sm text-muted-foreground">
                            CID {{ c.CID }} · SID {{ c.SID }} · {{ c.Cabang }} · {{ c.AREA }}
                        </p>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                            <span>PIC: <span class="text-foreground">{{ c.PIC }}</span></span>
                            <span>Kontak: <span class="text-foreground">{{ c.Kontak }}</span></span>
                            <span>Tipe: <span class="text-foreground">{{ c['Tipe Pelanggan'] }}</span></span>
                            <span>Produk: <span class="text-foreground">{{ c.Produk }}</span></span>
                            <span>BW: <span class="text-foreground">{{ c['Bandwith Main'] }}</span></span>
                        </div>
                    </div>
                    <component :is="expanded === c.id ? ChevronUp : ChevronDown" class="mt-1 size-5 shrink-0 text-muted-foreground" />
                </button>

                <div v-if="expanded === c.id" class="border-t bg-muted/20 p-4">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-medium">Detail Lengkap Pelanggan</h3>
                        <div class="flex gap-2">
                            <button class="inline-flex items-center gap-1 rounded-md border bg-background px-3 py-1.5 text-xs font-medium hover:bg-muted disabled:opacity-40" :disabled="!canManage" @click.stop="openEdit(c)">
                                <Pencil class="size-3.5" /> Edit
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-md border border-destructive/30 bg-background px-3 py-1.5 text-xs font-medium text-destructive hover:bg-destructive/10 disabled:opacity-40" :disabled="!canManage">
                                <Trash2 class="size-3.5" /> Hapus
                            </button>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div v-for="[key, label] in fields" :key="key" class="min-w-0 rounded-md border bg-background px-3 py-2">
                            <dt class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">{{ label }}</dt>
                            <dd class="mt-0.5 break-words text-sm text-foreground">{{ c[key] || '-' }}</dd>
                        </div>
                    </dl>
                    <p class="mt-3 text-xs text-muted-foreground">
                        Relasi: pelanggan ini dapat dipilih pada transaksi <strong>Barang Keluar</strong> dan <strong>Barang Masuk</strong> (retur).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-4xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ formMode === 'add' ? 'Tambah Pelanggan' : 'Edit Pelanggan' }}</h2>
                        <p class="text-xs text-muted-foreground">Lengkapi data pelanggan. Field BTS/BSC diambil dari Data BTS/BSC.</p>
                    </div>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="closeForm" aria-label="Tutup">
                        <X class="size-4" />
                    </button>
                </div>
                <form class="overflow-y-auto p-4" @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div>
                            <h3 class="mb-2 text-sm font-medium">Identitas & Layanan</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">ID</label>
                                    <input :value="formMode === 'edit' ? form.id : 'Otomatis'" type="text" readonly class="rounded-md border bg-muted/40 px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Nama Pelanggan <span class="text-destructive">*</span></label>
                                    <input v-model="form['Nama Pelanggan']" type="text" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Status</label>
                                    <select v-model="form.Status" class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
                                    </select>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Tipe Pelanggan <span class="text-destructive">*</span></label>
                                    <select v-model="form['Tipe Pelanggan']" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option v-for="t in tipePelangganOptions" :key="t" :value="t">{{ t }}</option>
                                    </select>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">CID</label>
                                    <input v-model="form.CID" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">SID</label>
                                    <input v-model="form.SID" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Cabang</label>
                                    <input v-model="form.Cabang" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Item</label>
                                    <input v-model="form.Item" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Area</label>
                                    <input v-model="form.AREA" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Sales</label>
                                    <input v-model="form.Sales" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Produk</label>
                                    <input v-model="form.Produk" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Segment</label>
                                    <input v-model="form.Segment" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Klasifikasi Pelanggan</label>
                                    <input v-model="form['Klasifikasi Pelanggan']" type="text" placeholder="Platinum / Gold / Silver / Bronze" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                    <p class="text-[11px] text-muted-foreground">Visit otomatis: Platinum = /1Bulan, selain itu = /3Bulan.</p>
                                </div>
                                <div class="grid gap-1 sm:col-span-2 lg:col-span-3">
                                    <label class="text-xs font-medium text-muted-foreground">Alamat</label>
                                    <textarea v-model="form.Alamat" rows="2" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium">Kontak & Teknis</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">PIC</label>
                                    <input v-model="form.PIC" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Kontak</label>
                                    <input v-model="form.Kontak" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Email</label>
                                    <input v-model="form.Email" type="email" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Koordinat</label>
                                    <input v-model="form.Koordinat" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Lastmile</label>
                                    <input v-model="form.Lastmile" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Service</label>
                                    <input v-model="form.Service" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth Main</label>
                                    <input v-model="form['Bandwith Main']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth 2nd</label>
                                    <input v-model="form['Bandwith 2nd']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth 3rd</label>
                                    <input v-model="form['Bandwith 3rd']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Aplikasi</label>
                                    <input v-model="form.Aplikasi" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Main Link</label>
                                    <input v-model="form['Main LInk']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Backup Link</label>
                                    <input v-model="form['Backup Link']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Visit</label>
                                    <input v-model="form.Visit" type="text" readonly class="rounded-md border bg-muted/40 px-3 py-2 text-sm" />
                                    <p class="text-[11px] text-muted-foreground">Otomatis sesuai klasifikasi pelanggan.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium">Perangkat & Jaringan</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">BTS/BSC <span class="text-destructive">*</span></label>
                                    <select v-model="form['BTS/BSC']" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option value="">Pilih BTS/BSC...</option>
                                        <option v-for="b in btsOptions" :key="b.id" :value="b.nama">
                                            {{ b.nama }} ({{ b.status }})
                                        </option>
                                    </select>
                                    <p class="text-[11px] text-muted-foreground">Data diambil dari menu Data BTS/BSC.</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Jenis Router</label>
                                    <input v-model="form['Jenis Router']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">ONU/ONT</label>
                                    <input v-model="form['ONU/ONT']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">ODP</label>
                                    <input v-model="form.ODP" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">PON Port</label>
                                    <input v-model="form['PON Port']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Radio Station</label>
                                    <input v-model="form['Radio Station']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">AP Sektor</label>
                                    <input v-model="form['AP Sektor']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Jml AP</label>
                                    <input v-model="form['Jml AP']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Merk AP</label>
                                    <input v-model="form['Merk AP']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1 sm:col-span-2 lg:col-span-1">
                                    <label class="text-xs font-medium text-muted-foreground">Tipe AP</label>
                                    <input v-model="form['Tipe AP']" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
                            {{ formMode === 'add' ? 'Simpan Pelanggan' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
