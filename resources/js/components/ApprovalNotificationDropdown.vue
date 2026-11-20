<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowRight,
    ArrowUpFromLine,
    Bell,
    Check,
    CheckCircle2,
    Clock,
    X,
} from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRole } from '@/composables/useRole';

const { canManage } = useRole();
const page = usePage();

const isOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const pendingCount = computed<number>(() => {
    return (page.props as any).pending_approvals_count ?? 0;
});

const pendingList = computed<any[]>(() => {
    return (page.props as any).pending_approvals ?? [];
});

function toggleDropdown() {
    isOpen.value = !isOpen.value;
}

function closeDropdown() {
    isOpen.value = false;
}

function handleClickOutside(event: MouseEvent) {
    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(event.target as Node)
    ) {
        closeDropdown();
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

function approve(id: number) {
    if (!confirm('Setujui permintaan transaksi ini sekarang?')) return;
    router.post(`/transaksi/${id}/setujui`, {}, { preserveScroll: true });
}

function reject(id: number) {
    const reason = prompt('Masukkan alasan penolakan permintaan ini:');
    if (reason === null) return;
    router.post(`/transaksi/${id}/tolak`, { reason }, { preserveScroll: true });
}
</script>

<template>
    <div v-if="canManage" ref="dropdownRef" class="relative">
        <!-- Bell Trigger Button -->
        <button
            type="button"
            class="relative flex size-9 items-center justify-center rounded-xl border border-border/80 bg-background transition-all hover:border-[#fb4f00]/40 hover:bg-[#fb4f00]/5 active:scale-95"
            :class="{
                'border-[#fb4f00] bg-[#fb4f00]/5 ring-2 ring-[#fb4f00]/20':
                    isOpen,
            }"
            aria-label="Persetujuan Transaksi"
            @click="toggleDropdown"
        >
            <Bell class="size-4 text-foreground" />
            <!-- Notification Badge Counter -->
            <span
                v-if="pendingCount > 0"
                class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-[#fb4f00] px-1 text-[10px] font-extrabold text-white shadow-xs"
            >
                <span
                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#fb4f00] opacity-50"
                ></span>
                <span class="relative">{{
                    pendingCount > 9 ? '9+' : pendingCount
                }}</span>
            </span>
        </button>

        <!-- Modern Notification Pop-up Dialog / Dropdown -->
        <div
            v-if="isOpen"
            class="absolute top-11 right-0 z-50 w-80 animate-in overflow-hidden rounded-2xl border border-border/80 bg-card shadow-2xl transition-all duration-150 zoom-in-95 fade-in sm:w-96"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-border/70 bg-muted/40 px-4 py-3"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="flex size-7 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400"
                    >
                        <Clock class="size-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-foreground">
                            Persetujuan SPV
                        </h4>
                        <p class="text-[10px] text-muted-foreground">
                            {{ pendingCount }} permintaan menunggu
                        </p>
                    </div>
                </div>
                <Link
                    href="/persetujuan"
                    class="text-[11px] font-semibold text-[#fb4f00] hover:underline"
                    @click="closeDropdown"
                >
                    Lihat Semua
                </Link>
            </div>

            <!-- List Body -->
            <div
                class="max-h-[380px] divide-y divide-border/60 overflow-y-auto"
            >
                <div
                    v-for="item in pendingList"
                    :key="item.id"
                    class="space-y-2 p-3.5 transition-colors hover:bg-muted/30"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span
                                :class="[
                                    'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold',
                                    item.direction === 'in'
                                        ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400'
                                        : 'bg-[#fb4f00]/15 text-[#fb4f00]',
                                ]"
                            >
                                <component
                                    :is="
                                        item.direction === 'in'
                                            ? ArrowDownToLine
                                            : ArrowUpFromLine
                                    "
                                    class="size-3"
                                />
                                {{
                                    item.direction === 'in' ? 'Masuk' : 'Keluar'
                                }}
                            </span>
                            <span
                                class="font-mono text-xs font-bold text-foreground"
                            >
                                {{ item.reference }}
                            </span>
                        </div>
                        <span class="text-[10px] text-muted-foreground">
                            {{ item.created_at_human }}
                        </span>
                    </div>

                    <div class="space-y-0.5 text-xs">
                        <p class="line-clamp-2 text-foreground">
                            <strong>{{ item.user_name }}</strong
                            >: {{ item.summary }}
                        </p>
                        <p
                            v-if="item.customer_name"
                            class="text-[11px] text-muted-foreground"
                        >
                            🎯 {{ item.customer_name }}
                        </p>
                    </div>

                    <!-- Direct Quick Actions -->
                    <div class="flex items-center justify-end gap-1.5 pt-1">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-lg border border-rose-500/30 bg-rose-500/10 px-2.5 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-500/20 active:scale-95 dark:text-rose-400"
                            @click="reject(item.id)"
                        >
                            <X class="size-3" />
                            <span>Tolak</span>
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1 text-[11px] font-bold text-white shadow-2xs hover:bg-emerald-500 active:scale-95"
                            @click="approve(item.id)"
                        >
                            <Check class="size-3" />
                            <span>Setujui</span>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="!pendingList.length"
                    class="space-y-1.5 p-6 text-center text-xs text-muted-foreground"
                >
                    <CheckCircle2 class="mx-auto size-7 text-emerald-500/50" />
                    <p class="font-semibold text-foreground">
                        Tidak Ada Permintaan Tertunda
                    </p>
                    <p class="text-[11px]">
                        Semua inputan transaksi dari tim teknis sudah
                        diverifikasi.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-border/70 bg-muted/30 p-2.5">
                <Link
                    href="/persetujuan"
                    class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-border bg-background py-2 text-xs font-bold text-foreground transition-all hover:border-[#fb4f00]/40 hover:text-[#fb4f00] active:scale-95"
                    @click="closeDropdown"
                >
                    <span>Buka Halaman Persetujuan</span>
                    <ArrowRight class="size-3.5" />
                </Link>
            </div>
        </div>
    </div>
</template>
