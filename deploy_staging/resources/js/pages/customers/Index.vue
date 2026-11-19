<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp, Pencil, Plus, Search, Trash2, UserRound, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { useRole } from '@/composables/useRole';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Pelanggan', href: '/pelanggan' },
        ],
    },
});

type BtsOption = {
    id: number;
    nama: string;
    alamat: string;
    status: string;
};

type CustomerRow = {
    id: number;
    cabang: string | null;
    item: string | null;
    tipe_pelanggan: string;
    cid: string | null;
    sid: string | null;
    status: string;
    nama_pelanggan: string;
    area: string | null;
    sales: string | null;
    alamat: string | null;
    koordinat: string | null;
    pic: string | null;
    kontak: string | null;
    email: string | null;
    lastmile: string | null;
    produk: string | null;
    bandwidth_main: string | null;
    bandwidth_2nd: string | null;
    bandwidth_3rd: string | null;
    service: string | null;
    aplikasi: string | null;
    main_link: string | null;
    backup_link: string | null;
    segment: string | null;
    klasifikasi_pelanggan: string | null;
    visit: string;
    bts_bsc_id: number | null;
    bts_bsc: BtsOption | null;
    jenis_router: string | null;
    onu_ont: string | null;
    odp: string | null;
    pon_port: string | null;
    radio_station: string | null;
    ap_sektor: string | null;
    jumlah_ap: number | null;
    merk_ap: string | null;
    tipe_ap: string | null;
};

type CustomerForm = {
    nama_pelanggan: string;
    tipe_pelanggan: string;
    status: string;
    bts_bsc_id: number | null;
    cabang: string;
    item: string;
    cid: string;
    sid: string;
    area: string;
    sales: string;
    alamat: string;
    koordinat: string;
    pic: string;
    kontak: string;
    email: string;
    lastmile: string;
    produk: string;
    bandwidth_main: string;
    bandwidth_2nd: string;
    bandwidth_3rd: string;
    service: string;
    aplikasi: string;
    main_link: string;
    backup_link: string;
    segment: string;
    klasifikasi_pelanggan: string;
    visit: string;
    jenis_router: string;
    onu_ont: string;
    odp: string;
    pon_port: string;
    radio_station: string;
    ap_sektor: string;
    jumlah_ap: number | string;
    merk_ap: string;
    tipe_ap: string;
};

const props = defineProps<{
    customers: CustomerRow[];
    btsOptions: BtsOption[];
    filters: { q: string };
    options: { statuses: string[]; types: string[] };
}>();

const { roleLabel, canManage } = useRole();
const expanded = ref<number | null>(null);
const showForm = ref(false);
const editingId = ref<number | null>(null);
const search = ref(props.filters.q ?? '');

function visitByKlasifikasi(klasifikasi: string | null | undefined): string {
    return String(klasifikasi ?? '').trim().toLowerCase() === 'platinum' ? '/1Bulan' : '/3Bulan';
}

const form = useForm<CustomerForm>({
    nama_pelanggan: '',
    tipe_pelanggan: 'Busol',
    status: 'Aktif',
    bts_bsc_id: null,
    cabang: '',
    item: '',
    cid: '',
    sid: '',
    area: '',
    sales: '',
    alamat: '',
    koordinat: '',
    pic: '',
    kontak: '',
    email: '',
    lastmile: '',
    produk: '',
    bandwidth_main: '',
    bandwidth_2nd: '',
    bandwidth_3rd: '',
    service: '',
    aplikasi: '',
    main_link: '',
    backup_link: '',
    segment: '',
    klasifikasi_pelanggan: '',
    visit: '/3Bulan',
    jenis_router: '',
    onu_ont: '',
    odp: '',
    pon_port: '',
    radio_station: '',
    ap_sektor: '',
    jumlah_ap: '',
    merk_ap: '',
    tipe_ap: '',
});

watch(
    () => form.klasifikasi_pelanggan,
    (value) => {
        form.visit = visitByKlasifikasi(value);
    },
);

const detailFields: { key: keyof CustomerRow; label: string }[] = [
    { key: 'id', label: 'ID' },
    { key: 'cabang', label: 'Cabang' },
    { key: 'item', label: 'Item' },
    { key: 'tipe_pelanggan', label: 'Tipe Pelanggan' },
    { key: 'cid', label: 'CID' },
    { key: 'sid', label: 'SID' },
    { key: 'status', label: 'Status' },
    { key: 'nama_pelanggan', label: 'Nama Pelanggan' },
    { key: 'area', label: 'Area' },
    { key: 'sales', label: 'Sales' },
    { key: 'alamat', label: 'Alamat' },
    { key: 'koordinat', label: 'Koordinat' },
    { key: 'pic', label: 'PIC' },
    { key: 'kontak', label: 'Kontak' },
    { key: 'email', label: 'Email' },
    { key: 'lastmile', label: 'Lastmile' },
    { key: 'produk', label: 'Produk' },
    { key: 'bandwidth_main', label: 'Bandwidth Main' },
    { key: 'bandwidth_2nd', label: 'Bandwidth 2nd' },
    { key: 'bandwidth_3rd', label: 'Bandwidth 3rd' },
    { key: 'service', label: 'Service' },
    { key: 'aplikasi', label: 'Aplikasi' },
    { key: 'main_link', label: 'Main Link' },
    { key: 'backup_link', label: 'Backup Link' },
    { key: 'segment', label: 'Segment' },
    { key: 'klasifikasi_pelanggan', label: 'Klasifikasi' },
    { key: 'visit', label: 'Visit' },
    { key: 'bts_bsc', label: 'BTS/BSC' },
    { key: 'jenis_router', label: 'Jenis Router' },
    { key: 'onu_ont', label: 'ONU/ONT' },
    { key: 'odp', label: 'ODP' },
    { key: 'pon_port', label: 'PON Port' },
    { key: 'radio_station', label: 'Radio Station' },
    { key: 'ap_sektor', label: 'AP Sektor' },
    { key: 'jumlah_ap', label: 'Jml AP' },
    { key: 'merk_ap', label: 'Merk AP' },
    { key: 'tipe_ap', label: 'Tipe AP' },
];

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return props.customers;
    }

    return props.customers.filter((customer) =>
        [
            customer.nama_pelanggan,
            customer.cid,
            customer.sid,
            customer.cabang,
            customer.area,
        ]
            .filter(Boolean)
            .some((value) => String(value).toLowerCase().includes(term)),
    );
});

const statusCounts = computed(() => ({
    aktif: props.customers.filter((customer) => customer.status === 'Aktif').length,
    suspend: props.customers.filter((customer) => customer.status === 'Suspend').length,
}));

function detailValue(customer: CustomerRow, key: keyof CustomerRow): string {
    if (key === 'bts_bsc') {
        return customer.bts_bsc?.nama ?? '-';
    }

    const value = customer[key];

    if (value === null || value === undefined || value === '') {
        return '-';
    }

    return String(value);
}

function toggle(id: number) {
    expanded.value = expanded.value === id ? null : id;
}

function openAdd() {
    if (!canManage.value) return;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.tipe_pelanggan = 'Busol';
    form.status = 'Aktif';
    form.klasifikasi_pelanggan = '';
    form.visit = '/3Bulan';
    showForm.value = true;
}

function openEdit(customer: CustomerRow) {
    if (!canManage.value) return;

    editingId.value = customer.id;
    form.clearErrors();
    form.nama_pelanggan = customer.nama_pelanggan;
    form.tipe_pelanggan = customer.tipe_pelanggan;
    form.status = customer.status;
    form.bts_bsc_id = customer.bts_bsc_id;
    form.cabang = customer.cabang ?? '';
    form.item = customer.item ?? '';
    form.cid = customer.cid ?? '';
    form.sid = customer.sid ?? '';
    form.area = customer.area ?? '';
    form.sales = customer.sales ?? '';
    form.alamat = customer.alamat ?? '';
    form.koordinat = customer.koordinat ?? '';
    form.pic = customer.pic ?? '';
    form.kontak = customer.kontak ?? '';
    form.email = customer.email ?? '';
    form.lastmile = customer.lastmile ?? '';
    form.produk = customer.produk ?? '';
    form.bandwidth_main = customer.bandwidth_main ?? '';
    form.bandwidth_2nd = customer.bandwidth_2nd ?? '';
    form.bandwidth_3rd = customer.bandwidth_3rd ?? '';
    form.service = customer.service ?? '';
    form.aplikasi = customer.aplikasi ?? '';
    form.main_link = customer.main_link ?? '';
    form.backup_link = customer.backup_link ?? '';
    form.segment = customer.segment ?? '';
    form.klasifikasi_pelanggan = customer.klasifikasi_pelanggan ?? '';
    form.visit = visitByKlasifikasi(customer.klasifikasi_pelanggan);
    form.jenis_router = customer.jenis_router ?? '';
    form.onu_ont = customer.onu_ont ?? '';
    form.odp = customer.odp ?? '';
    form.pon_port = customer.pon_port ?? '';
    form.radio_station = customer.radio_station ?? '';
    form.ap_sektor = customer.ap_sektor ?? '';
    form.jumlah_ap = customer.jumlah_ap ?? '';
    form.merk_ap = customer.merk_ap ?? '';
    form.tipe_ap = customer.tipe_ap ?? '';
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
        form.put(`/pelanggan/${editingId.value}`, options);
        return;
    }

    form.post('/pelanggan', options);
}

function destroy(customer: CustomerRow) {
    if (!canManage.value) return;

    if (confirm(`Hapus pelanggan ${customer.nama_pelanggan}?`)) {
        router.delete(`/pelanggan/${customer.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Pelanggan" />

    <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Data Pelanggan</h1>
                    <span class="rounded-full bg-[#fb4f00]/10 px-2.5 py-0.5 text-xs font-semibold text-[#fb4f00]">
                        {{ customers.length }} Pelanggan
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Master data pelanggan untuk relasi pergerakan barang (Instalasi, Dismantle, Mutasi & Perangkat BTS).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="rounded-xl border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-2xs">
                    Peran: <span class="font-semibold text-foreground">{{ roleLabel }}</span>
                </span>
                <button
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-[#fb4f00]/25 transition-all hover:brightness-105 active:scale-95 disabled:pointer-events-none disabled:opacity-50"
                    :disabled="!canManage"
                    @click="openAdd"
                >
                    <Plus class="size-4" /> Tambah Pelanggan
                </button>
            </div>
        </div>

        <div v-if="!canManage" class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-xs text-amber-700 dark:text-amber-300">
            Peran Teknisi hanya memiliki izin baca data pelanggan (tanpa tambah, ubah, atau hapus).
        </div>

        <div class="flex flex-col gap-3 rounded-2xl border border-border/80 bg-card p-4 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-md">
                <Search class="pointer-events-none absolute left-3 top-2.5 size-3.5 text-muted-foreground" />
                <input v-model="search" type="text" placeholder="Cari nama, CID, SID, cabang, area..." class="w-full rounded-xl border border-border bg-background py-1.5 pl-8.5 pr-8 text-xs transition-colors hover:border-[#fb4f00]/40 focus:border-[#fb4f00] focus:ring-1 focus:ring-[#fb4f00]/40 outline-hidden" />
                <button v-if="search" class="absolute right-2.5 top-2.5 text-muted-foreground hover:text-foreground" @click="search = ''">
                    <X class="size-3.5" />
                </button>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 font-bold text-emerald-600 dark:text-emerald-400">Aktif: {{ statusCounts.aktif }}</span>
                <span class="rounded-full bg-amber-500/15 px-3 py-1 font-bold text-amber-600 dark:text-amber-400">Suspend: {{ statusCounts.suspend }}</span>
                <span class="rounded-full border border-border bg-background px-3 py-1 font-medium text-muted-foreground">Total: {{ customers.length }}</span>
            </div>
        </div>

        <div class="grid gap-3">
            <div
                v-for="c in filtered"
                :key="c.id"
                class="overflow-hidden rounded-2xl border border-border/80 bg-card shadow-2xs transition-all duration-200 hover:border-[#fb4f00]/30 hover:shadow-sm"
            >
                <button
                    class="flex w-full items-start gap-3.5 p-4 text-left transition-colors hover:bg-[#fb4f00]/[0.02] dark:hover:bg-[#fb4f00]/[0.04]"
                    @click="toggle(c.id)"
                >
                    <div class="mt-0.5 rounded-xl bg-[#fb4f00]/10 p-2.5 text-[#fb4f00]">
                        <UserRound class="size-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-bold text-foreground text-sm">{{ c.nama_pelanggan }}</h2>
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                :class="c.status === 'Aktif' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400'"
                            >{{ c.status }}</span>
                            <span class="inline-flex rounded-full border border-border bg-background px-2 py-0.5 text-[10px] font-medium">{{ c.klasifikasi_pelanggan ?? '-' }}</span>
                            <span class="inline-flex rounded-full border border-[#fb4f00]/30 bg-[#fb4f00]/10 px-2 py-0.5 text-[10px] font-bold text-[#fb4f00]">{{ c.tipe_pelanggan }}</span>
                            <span class="inline-flex rounded-full border border-border bg-muted/40 px-2 py-0.5 text-[10px] text-muted-foreground">Visit {{ c.visit }}</span>
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">
                            <span class="font-mono text-foreground">CID: {{ c.cid ?? '-' }}</span> · <span class="font-mono">SID: {{ c.sid ?? '-' }}</span> · {{ c.cabang ?? '-' }} · {{ c.area ?? '-' }}
                        </p>
                        <div class="mt-2.5 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-muted-foreground">
                            <span>PIC: <span class="font-medium text-foreground">{{ c.pic ?? '-' }}</span></span>
                            <span>Kontak: <span class="font-medium text-foreground">{{ c.kontak ?? '-' }}</span></span>
                            <span>Produk: <span class="font-medium text-foreground">{{ c.produk ?? '-' }}</span></span>
                            <span>BW: <span class="font-medium text-foreground">{{ c.bandwidth_main ?? '-' }}</span></span>
                            <span>BTS/BSC: <span class="font-medium text-foreground">{{ c.bts_bsc?.nama ?? '-' }}</span></span>
                        </div>
                    </div>
                    <component :is="expanded === c.id ? ChevronUp : ChevronDown" class="mt-1 size-5 shrink-0 text-muted-foreground transition-transform" />
                </button>

                <div v-if="expanded === c.id" class="border-t border-border/80 bg-muted/20 p-5">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-border/60 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">Spesifikasi & Detail Lengkap Pelanggan</h3>
                        <div class="flex gap-2">
                            <button class="inline-flex items-center gap-1 rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-medium hover:border-[#fb4f00]/40 hover:text-[#fb4f00] transition-colors disabled:opacity-40" :disabled="!canManage" @click.stop="openEdit(c)">
                                <Pencil class="size-3" /> Edit Data
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-xl border border-destructive/20 bg-background px-3 py-1.5 text-xs font-medium text-destructive hover:bg-destructive/10 transition-colors disabled:opacity-40" :disabled="!canManage" @click.stop="destroy(c)">
                                <Trash2 class="size-3" /> Hapus
                            </button>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 gap-x-3 gap-y-2.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div v-for="field in detailFields" :key="field.key" class="min-w-0 rounded-xl border border-border/80 bg-background/80 p-3 shadow-2xs">
                            <dt class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">{{ field.label }}</dt>
                            <dd class="mt-1 break-words font-medium text-xs text-foreground">{{ detailValue(c, field.key) }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs text-muted-foreground">
                        Relasi: pelanggan ini aktif terhubung ke transaksi <strong>Barang Keluar</strong> dan proses penerimaan <strong>Dismantle</strong>.
                    </p>
                </div>
            </div>
            <div v-if="!filtered.length" class="rounded-2xl border border-border bg-card p-8 text-center text-xs text-muted-foreground">
                Tidak ada data pelanggan yang cocok dengan kata kunci pencarian.
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
            <div class="absolute inset-0 bg-black/50" @click="closeForm"></div>
            <div class="relative z-10 flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl border bg-card shadow-xl sm:max-w-4xl sm:rounded-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h2 class="font-medium">{{ editingId ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h2>
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
                                    <input :value="editingId ?? 'Otomatis'" type="text" readonly class="rounded-md border bg-muted/40 px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Nama Pelanggan <span class="text-destructive">*</span></label>
                                    <input v-model="form.nama_pelanggan" type="text" required class="rounded-md border bg-background px-3 py-2 text-sm" />
                                    <p v-if="form.errors.nama_pelanggan" class="text-xs text-destructive">{{ form.errors.nama_pelanggan }}</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Status</label>
                                    <select v-model="form.status" class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option v-for="status in options.statuses" :key="status" :value="status">{{ status }}</option>
                                    </select>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Tipe Pelanggan <span class="text-destructive">*</span></label>
                                    <select v-model="form.tipe_pelanggan" required class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option v-for="type in options.types" :key="type" :value="type">{{ type }}</option>
                                    </select>
                                    <p v-if="form.errors.tipe_pelanggan" class="text-xs text-destructive">{{ form.errors.tipe_pelanggan }}</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">CID</label>
                                    <input v-model="form.cid" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">SID</label>
                                    <input v-model="form.sid" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Cabang</label>
                                    <input v-model="form.cabang" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Item</label>
                                    <input v-model="form.item" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Area</label>
                                    <input v-model="form.area" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Sales</label>
                                    <input v-model="form.sales" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Produk</label>
                                    <input v-model="form.produk" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Segment</label>
                                    <input v-model="form.segment" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Klasifikasi Pelanggan</label>
                                    <input v-model="form.klasifikasi_pelanggan" type="text" placeholder="Platinum / Gold / Silver / Bronze" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                    <p class="text-[11px] text-muted-foreground">Visit otomatis: Platinum = /1Bulan, selain itu = /3Bulan.</p>
                                </div>
                                <div class="grid gap-1 sm:col-span-2 lg:col-span-3">
                                    <label class="text-xs font-medium text-muted-foreground">Alamat</label>
                                    <textarea v-model="form.alamat" rows="2" class="rounded-md border bg-background px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium">Kontak & Teknis</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">PIC</label>
                                    <input v-model="form.pic" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Kontak</label>
                                    <input v-model="form.kontak" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Email</label>
                                    <input v-model="form.email" type="email" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                    <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Koordinat</label>
                                    <input v-model="form.koordinat" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Lastmile</label>
                                    <input v-model="form.lastmile" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Service</label>
                                    <input v-model="form.service" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth Main</label>
                                    <input v-model="form.bandwidth_main" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth 2nd</label>
                                    <input v-model="form.bandwidth_2nd" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Bandwidth 3rd</label>
                                    <input v-model="form.bandwidth_3rd" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Aplikasi</label>
                                    <input v-model="form.aplikasi" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Main Link</label>
                                    <input v-model="form.main_link" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Backup Link</label>
                                    <input v-model="form.backup_link" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Visit</label>
                                    <input v-model="form.visit" type="text" readonly class="rounded-md border bg-muted/40 px-3 py-2 text-sm" />
                                    <p class="text-[11px] text-muted-foreground">Otomatis sesuai klasifikasi pelanggan.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium">Perangkat & Jaringan</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">BTS/BSC</label>
                                    <select v-model="form.bts_bsc_id" class="rounded-md border bg-background px-3 py-2 text-sm">
                                        <option :value="null">Pilih BTS/BSC...</option>
                                        <option v-for="b in btsOptions" :key="b.id" :value="b.id">
                                            {{ b.nama }} ({{ b.status }})
                                        </option>
                                    </select>
                                    <p class="text-[11px] text-muted-foreground">Data diambil dari menu Data BTS/BSC.</p>
                                    <p v-if="form.errors.bts_bsc_id" class="text-xs text-destructive">{{ form.errors.bts_bsc_id }}</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Jenis Router</label>
                                    <input v-model="form.jenis_router" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">ONU/ONT</label>
                                    <input v-model="form.onu_ont" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">ODP</label>
                                    <input v-model="form.odp" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">PON Port</label>
                                    <input v-model="form.pon_port" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Radio Station</label>
                                    <input v-model="form.radio_station" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">AP Sektor</label>
                                    <input v-model="form.ap_sektor" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Jml AP</label>
                                    <input v-model="form.jumlah_ap" type="number" min="0" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                    <p v-if="form.errors.jumlah_ap" class="text-xs text-destructive">{{ form.errors.jumlah_ap }}</p>
                                </div>
                                <div class="grid gap-1">
                                    <label class="text-xs font-medium text-muted-foreground">Merk AP</label>
                                    <input v-model="form.merk_ap" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                                <div class="grid gap-1 sm:col-span-2 lg:col-span-1">
                                    <label class="text-xs font-medium text-muted-foreground">Tipe AP</label>
                                    <input v-model="form.tipe_ap" type="text" class="rounded-md border bg-background px-3 py-2 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col-reverse gap-2 border-t pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border bg-background px-4 py-2 text-sm font-medium hover:bg-muted" @click="closeForm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">
                            {{ form.processing ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Pelanggan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
