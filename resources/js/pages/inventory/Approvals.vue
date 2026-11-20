<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowDownToLine,
    ArrowRight,
    ArrowUpFromLine,
    Check,
    CheckCircle2,
    Clock,
    Copy,
    Filter,
    MessageCircle,
    Package,
    RefreshCw,
    Search,
    ShieldAlert,
    ShieldCheck,
    X,
    XCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import WhatsAppReportModal from '@/components/WhatsAppReportModal.vue';
import { useRole } from '@/composables/useRole';
import type { StockTransaction, WhatsAppReport } from '@/types/inventory';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Approval Hub', href: '/persetujuan' },
        ],
    },
});

const props = defineProps<{
    pending: StockTransaction[];
    history: StockTransaction[];
    stats: {
        pending_total: number;
        pending_in: number;
        pending_out: number;
        approved_today: number;
        rejected_today: number;
    };
}>();

const { canApprove } = useRole();

// Filter & Tab states
const activeTab = ref<'pending' | 'history'>('pending');
const directionFilter = ref<'all' | 'in' | 'out'>('all');
const search = ref('');

// Rejection Modal state
const showRejectModal = ref(false);
const rejectingTransaction = ref<StockTransaction | null>(null);
const rejectionReason = ref('');
const isRejecting = ref(false);

// WhatsApp Report Modal state
const showWhatsAppModal = ref(false);
const activeWhatsAppReport = ref<WhatsAppReport | null>(null);

// Copy feedback state
const copiedRef = ref<string | null>(null);

function copyReference(refStr: string) {
    navigator.clipboard.writeText(refStr);
    copiedRef.value = refStr;
    setTimeout(() => {
        copiedRef.value = null;
    }, 2000);
}

// Filtered lists
const currentList = computed(() => {
    return activeTab.value === 'pending' ? props.pending : props.history;
});

const filteredTransactions = computed(() => {
    let list = currentList.value;

    if (directionFilter.value !== 'all') {
        list = list.filter((t) => t.direction === directionFilter.value);
    }

    const term = search.value.trim().toLowerCase();
    if (!term) return list;

    return list.filter((t) => {
        const matchesRef = t.reference.toLowerCase().includes(term);
        const matchesUser = (t.user?.name ?? '').toLowerCase().includes(term);
        const matchesCustomer = (t.customer?.nama_pelanggan ?? '')
            .toLowerCase()
            .includes(term);
        const matchesItem = t.items.some(
            (i) =>
                i.item_name.toLowerCase().includes(term) ||
                i.serials.some((s) => s.toLowerCase().includes(term)),
        );
        return matchesRef || matchesUser || matchesCustomer || matchesItem;
    });
});

// Actions
function approve(transaction: StockTransaction) {
    const isOut = transaction.direction === 'out';
    const confirmMsg = isOut
        ? `Setujui pengeluaran barang ${transaction.reference}? Stok unit dan serial number akan resmi dikeluarkan.`
        : `Setujui penerimaan barang ${transaction.reference}? Stok unit dan serial number akan resmi masuk ke sistem.`;

    if (!confirm(confirmMsg)) return;

    router.post(
        `/transaksi/${transaction.id}/setujui`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function openReject(transaction: StockTransaction) {
    rejectingTransaction.value = transaction;
    rejectionReason.value = '';
    showRejectModal.value = true;
}

function submitReject() {
    if (!rejectingTransaction.value) return;

    if (!rejectionReason.value.trim()) {
        alert(
            'Mohon cantumkan alasan penolakan agar tim teknis mengetahui kendalanya.',
        );
        return;
    }

    isRejecting.value = true;
    router.post(
        `/transaksi/${rejectingTransaction.value.id}/tolak`,
        { reason: rejectionReason.value },
        {
            preserveScroll: true,
            onFinish: () => {
                isRejecting.value = false;
                showRejectModal.value = false;
                rejectingTransaction.value = null;
            },
        },
    );
}

function openWhatsApp(t: StockTransaction) {
    activeWhatsAppReport.value = {
        id: t.id,
        reference: t.reference,
        direction: t.direction,
        direction_label:
            t.direction === 'in' ? 'BARANG MASUK' : 'BARANG KELUAR',
        activity: t.activity,
        date: t.transaction_date,
        user_name: t.user?.name ?? 'Teknisi',
        customer_name: t.customer?.nama_pelanggan ?? null,
        customer_type: t.customer?.tipe_pelanggan ?? null,
        status: t.status,
        status_label:
            t.status_label ??
            (t.status === 'pending'
                ? 'Menunggu Approval'
                : t.status === 'approved'
                  ? 'Disetujui'
                  : 'Ditolak'),
        note: t.note,
        items: t.items.map((line) => ({
            name: line.item_name,
            quantity: line.quantity,
            unit: 'unit',
            serials: line.serials,
        })),
    };
    showWhatsAppModal.value = true;
}
</script>

<template>
    <Head title="Approval Hub - Persetujuan Transaksi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header Banner with Glassmorphism & BLiP Accents -->
        <div
            class="relative overflow-hidden rounded-2xl border border-border/80 bg-card p-6 shadow-xs"
        >
            <div
                class="pointer-events-none absolute -top-12 -right-12 size-56 rounded-full bg-gradient-to-br from-amber-500/20 via-[#fb4f00]/15 to-transparent blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-amber-500 via-[#fb4f00] to-transparent"
            ></div>

            <div
                class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-0.5 text-xs font-bold text-amber-600 dark:text-amber-400"
                        >
                            <ShieldCheck class="size-3.5" />
                            Approval Hub
                        </span>
                        <span
                            v-if="stats.pending_total > 0"
                            class="inline-flex items-center gap-1 rounded-full bg-[#fb4f00] px-2 py-0.5 text-[10px] font-extrabold text-white"
                        >
                            {{ stats.pending_total }} Menunggu
                        </span>
                    </div>
                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-foreground md:text-3xl"
                    >
                        Approval Hub Transaksi
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Verifikasi, setujui, atau tolak permohonan transaksi
                        barang masuk & barang keluar dari tim teknis.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        href="/persetujuan"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3.5 py-2 text-xs font-semibold text-foreground transition-all hover:border-[#fb4f00]/40 hover:bg-[#fb4f00]/5 active:scale-95"
                    >
                        <RefreshCw class="size-3.5 text-muted-foreground" />
                        <span>Refresh Data</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- 1. Total Pending -->
            <div
                class="relative overflow-hidden rounded-2xl border border-amber-500/30 bg-card p-5 shadow-xs transition-all hover:border-amber-500/50"
            >
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-amber-500"
                ></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground">
                            Menunggu Approval
                        </p>
                        <p
                            class="mt-1.5 text-3xl font-extrabold text-amber-600 dark:text-amber-400"
                        >
                            {{ stats.pending_total }}
                        </p>
                    </div>
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400"
                    >
                        <Clock class="size-6" />
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-muted-foreground">
                    Perlu tindakan verifikasi SPV / Manager
                </p>
            </div>

            <!-- 2. Pending Inflow -->
            <div
                class="relative overflow-hidden rounded-2xl border border-border/80 bg-card p-5 shadow-xs transition-all hover:border-emerald-500/30"
            >
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-emerald-500"
                ></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground">
                            Barang Masuk Tertunda
                        </p>
                        <p
                            class="mt-1.5 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400"
                        >
                            {{ stats.pending_in }}
                        </p>
                    </div>
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400"
                    >
                        <ArrowDownToLine class="size-6" />
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-muted-foreground">
                    FPB & Dismantle teknisi
                </p>
            </div>

            <!-- 3. Pending Outflow -->
            <div
                class="relative overflow-hidden rounded-2xl border border-border/80 bg-card p-5 shadow-xs transition-all hover:border-[#fb4f00]/30"
            >
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-[#fb4f00]"
                ></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground">
                            Barang Keluar Tertunda
                        </p>
                        <p
                            class="mt-1.5 text-3xl font-extrabold text-[#fb4f00]"
                        >
                            {{ stats.pending_out }}
                        </p>
                    </div>
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-[#fb4f00]/15 text-[#fb4f00]"
                    >
                        <ArrowUpFromLine class="size-6" />
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-muted-foreground">
                    Pasang Baru & WO pelanggan
                </p>
            </div>

            <!-- 4. Approved Today -->
            <div
                class="relative overflow-hidden rounded-2xl border border-border/80 bg-card p-5 shadow-xs transition-all hover:border-border"
            >
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-muted-foreground/30"
                ></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground">
                            Disetujui Hari Ini
                        </p>
                        <p
                            class="mt-1.5 text-3xl font-extrabold text-foreground"
                        >
                            {{ stats.approved_today }}
                        </p>
                    </div>
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-muted text-foreground"
                    >
                        <CheckCircle2 class="size-6 text-emerald-500" />
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-muted-foreground">
                    Ditolak hari ini: {{ stats.rejected_today }} transaksi
                </p>
            </div>
        </div>

        <!-- Controls: Tabs, Filters, and Search -->
        <div
            class="flex flex-col gap-3 rounded-2xl border border-border/80 bg-card p-4 shadow-2xs sm:flex-row sm:items-center sm:justify-between"
        >
            <!-- Main Tabs: Menunggu vs Riwayat -->
            <div class="inline-flex rounded-xl bg-muted/60 p-1">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all"
                    :class="
                        activeTab === 'pending'
                            ? 'bg-background text-[#fb4f00] shadow-xs'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    @click="activeTab = 'pending'"
                >
                    <Clock class="size-3.5" />
                    <span>Menunggu Approval</span>
                    <span
                        v-if="stats.pending_total > 0"
                        class="py-0.2 rounded-full bg-[#fb4f00]/15 px-1.5 text-[10px] font-extrabold text-[#fb4f00]"
                    >
                        {{ stats.pending_total }}
                    </span>
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all"
                    :class="
                        activeTab === 'history'
                            ? 'bg-background text-foreground shadow-xs'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    @click="activeTab = 'history'"
                >
                    <CheckCircle2 class="size-3.5" />
                    <span>Riwayat Selesai</span>
                </button>
            </div>

            <!-- Direction Filters & Search -->
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                <!-- Direction Segments -->
                <div
                    class="inline-flex rounded-xl border border-border bg-background p-0.5 text-xs"
                >
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-medium transition-colors"
                        :class="
                            directionFilter === 'all'
                                ? 'bg-muted font-bold text-foreground'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        @click="directionFilter = 'all'"
                    >
                        Semua
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-medium transition-colors"
                        :class="
                            directionFilter === 'in'
                                ? 'bg-emerald-500/15 font-bold text-emerald-600 dark:text-emerald-400'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        @click="directionFilter = 'in'"
                    >
                        Masuk
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-medium transition-colors"
                        :class="
                            directionFilter === 'out'
                                ? 'bg-[#fb4f00]/15 font-bold text-[#fb4f00]'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        @click="directionFilter = 'out'"
                    >
                        Keluar
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <Search
                        class="pointer-events-none absolute top-2 left-3 size-3.5 text-muted-foreground"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari ref, teknisi, barang, SN..."
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

        <!-- Request Cards List -->
        <div class="space-y-4">
            <div
                v-for="t in filteredTransactions"
                :key="t.id"
                class="group relative overflow-hidden rounded-2xl border border-border/80 bg-card p-5 shadow-xs transition-all duration-200 hover:border-border hover:shadow-md"
            >
                <!-- Top Accent Line -->
                <div
                    :class="[
                        'pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r',
                        t.status === 'pending'
                            ? t.direction === 'in'
                                ? 'from-emerald-500 to-amber-500'
                                : 'from-[#fb4f00] to-amber-500'
                            : t.status === 'approved'
                              ? 'from-emerald-500 to-emerald-600'
                              : 'from-rose-500 to-rose-600',
                    ]"
                ></div>

                <!-- Card Header -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Direction Pill -->
                            <span
                                :class="[
                                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-extrabold tracking-wide uppercase',
                                    t.direction === 'in'
                                        ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                        : 'bg-[#fb4f00]/15 text-[#fb4f00]',
                                ]"
                            >
                                <component
                                    :is="
                                        t.direction === 'in'
                                            ? ArrowDownToLine
                                            : ArrowUpFromLine
                                    "
                                    class="size-3.5"
                                />
                                {{
                                    t.direction === 'in'
                                        ? 'Barang Masuk'
                                        : 'Barang Keluar'
                                }}
                            </span>

                            <!-- Activity Pill -->
                            <span
                                class="inline-flex rounded-full border border-border bg-background px-2.5 py-0.5 text-xs font-semibold text-foreground"
                            >
                                {{ t.activity }}
                            </span>

                            <!-- Status Badge -->
                            <span
                                v-if="t.status === 'pending'"
                                class="inline-flex items-center gap-1 rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-0.5 text-xs font-bold text-amber-600 dark:text-amber-400"
                            >
                                <Clock class="size-3" />
                                Menunggu Approval
                            </span>
                            <span
                                v-else-if="t.status === 'approved'"
                                class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                <CheckCircle2 class="size-3" />
                                Disetujui
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1 rounded-full border border-rose-500/30 bg-rose-500/10 px-2.5 py-0.5 text-xs font-bold text-rose-600 dark:text-rose-400"
                            >
                                <XCircle class="size-3" />
                                Ditolak
                            </span>
                        </div>

                        <!-- Reference & Date -->
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <span
                                class="font-mono text-sm font-bold text-foreground"
                            >
                                {{ t.reference }}
                            </span>
                            <button
                                type="button"
                                class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground active:scale-95"
                                title="Salin No. Referensi"
                                @click="copyReference(t.reference)"
                            >
                                <component
                                    :is="
                                        copiedRef === t.reference ? Check : Copy
                                    "
                                    :class="[
                                        'size-3.5',
                                        copiedRef === t.reference
                                            ? 'text-emerald-500'
                                            : '',
                                    ]"
                                />
                            </button>
                            <span class="text-xs text-muted-foreground">·</span>
                            <span class="text-xs text-muted-foreground">{{
                                t.transaction_date
                            }}</span>
                        </div>
                    </div>

                    <!-- Submitter Info -->
                    <div
                        class="flex items-center gap-2 rounded-xl border border-border/60 bg-muted/30 px-3 py-1.5 text-xs"
                    >
                        <span class="text-muted-foreground"
                            >Petugas / Teknisi:</span
                        >
                        <span class="font-bold text-foreground"
                            >👤 {{ t.user?.name ?? 'Teknisi' }}</span
                        >
                    </div>
                </div>

                <!-- Customer Target (If outgoing / dismantle) -->
                <div
                    v-if="t.customer"
                    class="mt-3 flex items-center gap-2 rounded-xl bg-muted/40 px-3 py-2 text-xs"
                >
                    <span class="font-semibold text-muted-foreground"
                        >🎯 Tujuan Pelanggan / Site:</span
                    >
                    <span class="font-bold text-foreground">{{
                        t.customer.nama_pelanggan
                    }}</span>
                    <span
                        v-if="t.customer.tipe_pelanggan"
                        class="rounded bg-background px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground"
                    >
                        {{ t.customer.tipe_pelanggan }}
                    </span>
                </div>

                <!-- Items Detail Box -->
                <div
                    class="mt-4 space-y-2.5 rounded-xl border border-border/60 bg-background/50 p-4"
                >
                    <p
                        class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                    >
                        Rincian Perangkat & Komponen ({{ t.items.length }}
                        Item):
                    </p>
                    <div class="divide-y divide-border/40">
                        <div
                            v-for="item in t.items"
                            :key="item.id"
                            class="flex flex-col gap-2 py-2.5 first:pt-0 last:pb-0 sm:flex-row sm:items-start sm:justify-between"
                        >
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-bold text-foreground"
                                    >
                                        {{ item.item_name }}
                                    </span>
                                    <span
                                        class="inline-flex rounded-full border border-border bg-muted px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                    >
                                        {{ item.item_type_label }}
                                    </span>
                                </div>

                                <!-- Serial Numbers Chips -->
                                <div
                                    v-if="item.serials && item.serials.length"
                                    class="flex flex-wrap items-center gap-1.5 pt-0.5"
                                >
                                    <span
                                        class="text-[10px] font-medium text-muted-foreground"
                                        >SN:</span
                                    >
                                    <span
                                        v-for="sn in item.serials"
                                        :key="sn"
                                        class="inline-flex items-center gap-1 rounded-md border border-border/80 bg-muted/60 px-1.5 py-0.5 font-mono text-[10px] text-foreground"
                                    >
                                        {{ sn }}
                                    </span>
                                </div>
                            </div>

                            <!-- Quantity Badge -->
                            <div class="shrink-0 self-start sm:self-auto">
                                <span
                                    :class="[
                                        'inline-flex rounded-lg px-2.5 py-1 text-xs font-extrabold',
                                        t.direction === 'in'
                                            ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                            : 'bg-[#fb4f00]/15 text-[#fb4f00]',
                                    ]"
                                >
                                    {{ t.direction === 'in' ? '+' : '-'
                                    }}{{ item.quantity }} Unit
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes / Rejection Reason -->
                <div
                    v-if="t.note"
                    class="mt-3 rounded-xl border border-border/40 bg-muted/20 p-2.5 text-xs text-muted-foreground"
                >
                    <span class="font-semibold text-foreground"
                        >📝 Catatan Teknisi:</span
                    >
                    {{ t.note }}
                </div>
                <div
                    v-if="t.rejection_note"
                    class="mt-3 rounded-xl border border-rose-500/30 bg-rose-500/10 p-2.5 text-xs text-rose-700 dark:text-rose-400"
                >
                    <span class="font-bold">❌ Alasan Penolakan:</span>
                    {{ t.rejection_note }}
                    <span
                        v-if="t.approved_by"
                        class="mt-0.5 block text-[11px] opacity-80"
                    >
                        Oleh: {{ t.approved_by.name }}
                    </span>
                </div>
                <div
                    v-else-if="t.status === 'approved' && t.approved_by"
                    class="mt-2 text-[11px] font-medium text-emerald-600 dark:text-emerald-400"
                >
                    ✓ Telah disetujui resmi oleh: {{ t.approved_by.name }}
                </div>

                <!-- Action Footer -->
                <div
                    class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-border/60 pt-3.5"
                >
                    <!-- WhatsApp Report Preview -->
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-colors hover:bg-emerald-500/20 active:scale-95 dark:text-emerald-400"
                        @click="openWhatsApp(t)"
                    >
                        <MessageCircle class="size-4" />
                        <span>Format WA</span>
                    </button>

                    <!-- Pending Decision Buttons -->
                    <div
                        v-if="t.status === 'pending'"
                        class="flex items-center gap-2"
                    >
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-xs font-bold text-rose-600 transition-all hover:bg-rose-500/20 active:scale-95 dark:text-rose-400"
                            @click="openReject(t)"
                        >
                            <X class="size-4" />
                            <span>Tolak</span>
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-2 text-xs font-bold text-white shadow-sm shadow-emerald-600/30 transition-all hover:brightness-105 active:scale-95"
                            @click="approve(t)"
                        >
                            <Check class="size-4" />
                            <span>Setujui Permintaan</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!filteredTransactions.length"
                class="flex flex-col items-center justify-center rounded-2xl border border-border/80 bg-card p-12 text-center"
            >
                <div
                    class="mb-3 rounded-full bg-emerald-500/10 p-4 text-emerald-600 dark:text-emerald-400"
                >
                    <CheckCircle2 class="size-8" />
                </div>
                <h3 class="text-base font-bold text-foreground">
                    {{
                        activeTab === 'pending'
                            ? 'Semua Permintaan Selesai Diverifikasi'
                            : 'Tidak Ada Riwayat Transaksi'
                    }}
                </h3>
                <p class="mt-1 max-w-sm text-xs text-muted-foreground">
                    {{
                        activeTab === 'pending'
                            ? 'Saat ini tidak ada permohonan barang masuk atau barang keluar dari tim teknis yang tertunda.'
                            : 'Belum ada transaksi yang sesuai dengan kriteria pencarian Anda.'
                    }}
                </p>
                <Link
                    v-if="activeTab === 'pending'"
                    href="/dashboard"
                    class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-[#fb4f00] hover:underline"
                >
                    <span>Kembali ke Dashboard</span>
                    <ArrowRight class="size-3.5" />
                </Link>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <Teleport to="body">
        <div
            v-if="showRejectModal && rejectingTransaction"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div
                class="fixed inset-0 bg-background/80 backdrop-blur-xs"
                @click="showRejectModal = false"
            ></div>

            <div
                class="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-border/80 bg-card shadow-2xl transition-all"
            >
                <div class="border-b border-border/70 bg-rose-500/10 px-5 py-4">
                    <div
                        class="flex items-center gap-2.5 text-rose-600 dark:text-rose-400"
                    >
                        <AlertCircle class="size-5" />
                        <h3 class="text-sm font-bold">
                            Tolak Permintaan Transaksi
                        </h3>
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        No. Ref:
                        <strong class="font-mono text-foreground">{{
                            rejectingTransaction.reference
                        }}</strong>
                    </p>
                </div>

                <div class="space-y-4 p-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-foreground">
                            Alasan Penolakan
                            <span class="text-destructive">*</span>
                        </label>
                        <textarea
                            v-model="rejectionReason"
                            rows="3"
                            placeholder="Contoh: Serial number tidak sesuai fisik / stock barang sedang dialokasikan untuk maintenance..."
                            class="w-full rounded-xl border border-border bg-background p-3 text-xs text-foreground outline-hidden transition-colors focus:border-rose-500 focus:ring-1 focus:ring-rose-500/40"
                        ></textarea>
                        <p class="text-[11px] text-muted-foreground">
                            Alasan ini akan tersimpan dan terlihat oleh teknisi
                            penginput.
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-2 border-t border-border/70 bg-muted/30 p-4"
                >
                    <button
                        type="button"
                        class="rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground transition-all hover:bg-muted"
                        @click="showRejectModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        :disabled="isRejecting"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition-all hover:bg-rose-500 active:scale-95 disabled:opacity-50"
                        @click="submitReject"
                    >
                        <X class="size-4" />
                        <span>{{
                            isRejecting ? 'Menolak...' : 'Konfirmasi Tolak'
                        }}</span>
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- WhatsApp Report Modal -->
    <WhatsAppReportModal
        :show="showWhatsAppModal"
        :report="activeWhatsAppReport"
        @close="showWhatsAppModal = false"
    />
</template>
