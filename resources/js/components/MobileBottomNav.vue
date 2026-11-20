<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
    FileSpreadsheet,
    LayoutGrid,
    Plus,
    RadioTower,
    Users,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { useRole } from '@/composables/useRole';
import { dashboard } from '@/routes';

const page = usePage();
const { canViewReports, canManage } = useRole();
const showQuickActions = ref(false);

const currentUrl = computed(() => page.url);

function isActive(path: string): boolean {
    if (path === '/dashboard') {
        return currentUrl.value === '/dashboard' || currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(path);
}

function toggleQuickActions() {
    showQuickActions.value = !showQuickActions.value;
}

function closeQuickActions() {
    showQuickActions.value = false;
}
</script>

<template>
    <!-- Mobile Bottom Navigation Bar (Visible only on < md screens) -->
    <nav
        aria-label="Navigasi Bawah Mobile"
        class="fixed inset-x-0 bottom-0 z-40 border-t border-border/80 bg-background/95 px-3 pt-2 pb-[max(env(safe-area-inset-bottom),0.5rem)] shadow-lg backdrop-blur-md md:hidden"
    >
        <div class="mx-auto flex max-w-md items-center justify-between">
            <!-- 1. Beranda / Dashboard -->
            <Link
                :href="dashboard()"
                class="flex flex-1 flex-col items-center justify-center gap-1 py-1 text-center transition-all duration-150 active:scale-90"
                :class="
                    isActive('/dashboard')
                        ? 'font-semibold text-[#fb4f00]'
                        : 'text-muted-foreground hover:text-foreground'
                "
            >
                <div
                    class="relative flex size-7 items-center justify-center rounded-xl transition-colors"
                    :class="isActive('/dashboard') ? 'bg-[#fb4f00]/15' : ''"
                >
                    <LayoutGrid class="size-4.5" />
                    <span
                        v-if="isActive('/dashboard')"
                        class="absolute -bottom-0.5 size-1 rounded-full bg-[#fb4f00]"
                    ></span>
                </div>
                <span class="text-[10px] tracking-tight">Beranda</span>
            </Link>

            <!-- 2. Barang Masuk -->
            <Link
                href="/barang-masuk"
                class="flex flex-1 flex-col items-center justify-center gap-1 py-1 text-center transition-all duration-150 active:scale-90"
                :class="
                    isActive('/barang-masuk')
                        ? 'font-semibold text-emerald-600 dark:text-emerald-400'
                        : 'text-muted-foreground hover:text-foreground'
                "
            >
                <div
                    class="relative flex size-7 items-center justify-center rounded-xl transition-colors"
                    :class="
                        isActive('/barang-masuk') ? 'bg-emerald-500/15' : ''
                    "
                >
                    <ArrowDownToLine class="size-4.5" />
                    <span
                        v-if="isActive('/barang-masuk')"
                        class="absolute -bottom-0.5 size-1 rounded-full bg-emerald-500"
                    ></span>
                </div>
                <span class="text-[10px] tracking-tight">Masuk</span>
            </Link>

            <!-- 3. Floating Action Center Button (+) -->
            <div
                class="relative -top-4 flex flex-1 items-center justify-center"
            >
                <button
                    type="button"
                    class="group relative flex size-13 items-center justify-center rounded-full bg-gradient-to-tr from-[#d83f00] via-[#fb4f00] to-[#ff7333] text-white shadow-lg shadow-[#fb4f00]/40 transition-all duration-200 hover:scale-105 active:scale-90"
                    :class="showQuickActions ? 'rotate-45' : ''"
                    @click="toggleQuickActions"
                    aria-label="Aksi Cepat"
                >
                    <Plus class="size-6 transition-transform duration-200" />
                    <span
                        class="absolute -inset-1 -z-10 animate-ping rounded-full bg-[#fb4f00]/20 opacity-75"
                        v-if="!showQuickActions"
                    ></span>
                </button>
            </div>

            <!-- 4. Barang Keluar -->
            <Link
                href="/barang-keluar"
                class="flex flex-1 flex-col items-center justify-center gap-1 py-1 text-center transition-all duration-150 active:scale-90"
                :class="
                    isActive('/barang-keluar')
                        ? 'font-semibold text-[#fb4f00]'
                        : 'text-muted-foreground hover:text-foreground'
                "
            >
                <div
                    class="relative flex size-7 items-center justify-center rounded-xl transition-colors"
                    :class="isActive('/barang-keluar') ? 'bg-[#fb4f00]/15' : ''"
                >
                    <ArrowUpFromLine class="size-4.5" />
                    <span
                        v-if="isActive('/barang-keluar')"
                        class="absolute -bottom-0.5 size-1 rounded-full bg-[#fb4f00]"
                    ></span>
                </div>
                <span class="text-[10px] tracking-tight">Keluar</span>
            </Link>

            <!-- 5. Stok Barang -->
            <Link
                href="/stok"
                class="flex flex-1 flex-col items-center justify-center gap-1 py-1 text-center transition-all duration-150 active:scale-90"
                :class="
                    isActive('/stok')
                        ? 'font-semibold text-sky-600 dark:text-sky-400'
                        : 'text-muted-foreground hover:text-foreground'
                "
            >
                <div
                    class="relative flex size-7 items-center justify-center rounded-xl transition-colors"
                    :class="isActive('/stok') ? 'bg-sky-500/15' : ''"
                >
                    <Boxes class="size-4.5" />
                    <span
                        v-if="isActive('/stok')"
                        class="absolute -bottom-0.5 size-1 rounded-full bg-sky-500"
                    ></span>
                </div>
                <span class="text-[10px] tracking-tight">Stok</span>
            </Link>
        </div>
    </nav>

    <!-- Mobile Quick Actions Bottom Sheet Modal -->
    <Teleport to="body">
        <div
            v-if="showQuickActions"
            class="fixed inset-0 z-50 flex items-end justify-center md:hidden"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"
                @click="closeQuickActions"
            ></div>

            <!-- Bottom Sheet Content -->
            <div
                class="relative z-10 w-full max-w-lg animate-in rounded-t-3xl border-t border-border/80 bg-card p-5 pb-[max(env(safe-area-inset-bottom),1.5rem)] shadow-2xl transition-all duration-300 slide-in-from-bottom"
            >
                <!-- Grab Handle -->
                <div
                    class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-muted-foreground/30"
                ></div>

                <!-- Header -->
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-foreground">
                            Aksi Cepat Gudang
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Pilih operasional yang ingin Anda lakukan
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full bg-muted p-1.5 text-muted-foreground hover:text-foreground active:scale-90"
                        @click="closeQuickActions"
                        aria-label="Tutup"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <!-- Grid of Quick Action Cards -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- 1. Input Masuk -->
                    <Link
                        href="/barang-masuk"
                        class="group flex flex-col justify-between rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4 transition-all duration-150 hover:bg-emerald-500/10 active:scale-95"
                        @click="closeQuickActions"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400"
                            >
                                <ArrowDownToLine class="size-5" />
                            </div>
                            <span
                                class="text-[10px] font-semibold text-emerald-600 uppercase"
                                >Inflow</span
                            >
                        </div>
                        <div class="mt-3">
                            <p class="text-sm font-bold text-foreground">
                                Barang Masuk
                            </p>
                            <p class="text-[11px] text-muted-foreground">
                                FPB & Dismantle
                            </p>
                        </div>
                    </Link>

                    <!-- 2. Input Keluar -->
                    <Link
                        href="/barang-keluar"
                        class="group flex flex-col justify-between rounded-2xl border border-[#fb4f00]/20 bg-[#fb4f00]/5 p-4 transition-all duration-150 hover:bg-[#fb4f00]/10 active:scale-95"
                        @click="closeQuickActions"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-[#fb4f00]/15 text-[#fb4f00]"
                            >
                                <ArrowUpFromLine class="size-5" />
                            </div>
                            <span
                                class="text-[10px] font-semibold text-[#fb4f00] uppercase"
                                >Outflow</span
                            >
                        </div>
                        <div class="mt-3">
                            <p class="text-sm font-bold text-foreground">
                                Barang Keluar
                            </p>
                            <p class="text-[11px] text-muted-foreground">
                                Pasang Baru & WO
                            </p>
                        </div>
                    </Link>

                    <!-- 3. Cek Stok -->
                    <Link
                        href="/stok"
                        class="group flex flex-col justify-between rounded-2xl border border-sky-500/20 bg-sky-500/5 p-4 transition-all duration-150 hover:bg-sky-500/10 active:scale-95"
                        @click="closeQuickActions"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-600 dark:text-sky-400"
                            >
                                <Boxes class="size-5" />
                            </div>
                            <span
                                class="text-[10px] font-semibold text-sky-600 uppercase"
                                >Stok</span
                            >
                        </div>
                        <div class="mt-3">
                            <p class="text-sm font-bold text-foreground">
                                Stok Barang
                            </p>
                            <p class="text-[11px] text-muted-foreground">
                                Cek sisa kuota & serial
                            </p>
                        </div>
                    </Link>

                    <!-- 4. Laporan atau Pelanggan -->
                    <Link
                        :href="canViewReports ? '/laporan' : '/pelanggan'"
                        class="group flex flex-col justify-between rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 transition-all duration-150 hover:bg-indigo-500/10 active:scale-95"
                        @click="closeQuickActions"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400"
                            >
                                <component
                                    :is="
                                        canViewReports ? FileSpreadsheet : Users
                                    "
                                    class="size-5"
                                />
                            </div>
                            <span
                                class="text-[10px] font-semibold text-indigo-600 uppercase"
                            >
                                {{ canViewReports ? 'Laporan' : 'Master' }}
                            </span>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm font-bold text-foreground">
                                {{
                                    canViewReports
                                        ? 'Laporan Mutasi'
                                        : 'Data Pelanggan'
                                }}
                            </p>
                            <p class="text-[11px] text-muted-foreground">
                                {{
                                    canViewReports
                                        ? 'Ekspor Excel / PDF'
                                        : 'Daftar ID Pelanggan'
                                }}
                            </p>
                        </div>
                    </Link>
                </div>

                <!-- Quick Master Data Links -->
                <div
                    class="mt-4 flex items-center justify-around border-t border-border/60 pt-3 text-xs text-muted-foreground"
                >
                    <Link
                        href="/pelanggan"
                        class="flex items-center gap-1.5 py-1 font-medium hover:text-foreground active:scale-95"
                        @click="closeQuickActions"
                    >
                        <Users class="size-3.5 text-muted-foreground" />
                        <span>Pelanggan</span>
                    </Link>
                    <span class="text-border">•</span>
                    <Link
                        href="/bts"
                        class="flex items-center gap-1.5 py-1 font-medium hover:text-foreground active:scale-95"
                        @click="closeQuickActions"
                    >
                        <RadioTower class="size-3.5 text-muted-foreground" />
                        <span>BTS & BSC</span>
                    </Link>
                </div>
            </div>
        </div>
    </Teleport>
</template>
