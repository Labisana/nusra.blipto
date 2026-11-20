<script setup lang="ts">
import { Check, Copy, ExternalLink, MessageCircle, Send, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import type { WhatsAppReport } from '@/types/inventory';

const props = defineProps<{
    show: boolean;
    report: WhatsAppReport | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const copied = ref(false);

const generatedMessage = computed(() => {
    if (!props.report) return '';

    const r = props.report;
    const isIncoming = r.direction === 'in';
    const title = isIncoming
        ? '*LAPORAN BARANG MASUK - BLiP NUSRA*'
        : '*LAPORAN BARANG KELUAR - BLiP NUSRA*';

    const lines: string[] = [
        title,
        '--------------------------------------------------',
        `📌 *No. Ref:* ${r.reference}`,
        `📅 *Tanggal:* ${r.date}`,
        `👤 *Petugas/Teknisi:* ${r.user_name}`,
        `⚡ *Aktivitas:* ${r.activity}`,
    ];

    if (r.customer_name) {
        lines.push(
            `🎯 *Tujuan/Pelanggan:* ${r.customer_name}${r.customer_type ? ` (${r.customer_type})` : ''}`,
        );
    }

    lines.push('');
    lines.push('📦 *Daftar Barang:*');

    r.items.forEach((item, index) => {
        lines.push(
            `${index + 1}. *${item.name}* - ${item.quantity} ${item.unit || 'unit'}`,
        );
        if (item.serials && item.serials.length > 0) {
            lines.push(`   SN: ${item.serials.join(', ')}`);
        }
    });

    if (r.note) {
        lines.push('');
        lines.push(`📝 *Catatan:* ${r.note}`);
    }

    lines.push('');
    if (r.status === 'pending') {
        lines.push('⏳ *Status:* *MENUNGGU PERSETUJUAN SPV*');
        lines.push('--------------------------------------------------');
        lines.push(
            'Kepada Yth. Bapak/Ibu SPV, mohon untuk dapat melakukan verifikasi & persetujuan pada sistem:',
        );
    } else if (r.status === 'approved') {
        lines.push('✅ *Status:* *DISETUJUI / SELESAI*');
        lines.push('--------------------------------------------------');
    } else {
        lines.push('❌ *Status:* *DITOLAK*');
        lines.push('--------------------------------------------------');
    }

    const host =
        typeof window !== 'undefined'
            ? window.location.origin
            : 'https://nusratim.my.id';
    lines.push(`${host}`);

    return lines.join('\n');
});

async function copyText() {
    if (!generatedMessage.value) return;

    try {
        await navigator.clipboard.writeText(generatedMessage.value);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 3000);
    } catch {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = generatedMessage.value;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 3000);
    }
}

function sendWhatsApp() {
    if (!generatedMessage.value) return;
    const url = `https://api.whatsapp.com/send?text=${encodeURIComponent(generatedMessage.value)}`;
    window.open(url, '_blank');
}
</script>

<template>
    <div
        v-if="show && report"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <!-- Backdrop -->
        <div
            class="fixed inset-0 bg-background/80 backdrop-blur-xs transition-opacity"
            @click="emit('close')"
        ></div>

        <!-- Modal Dialog -->
        <div
            class="relative z-10 flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-border/80 bg-card shadow-2xl transition-all"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-border/70 bg-muted/30 px-5 py-4"
            >
                <div class="flex items-center gap-2.5">
                    <div
                        class="flex size-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                    >
                        <MessageCircle class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-foreground">
                            Report Konfirmasi WhatsApp
                        </h3>
                        <p class="text-xs text-muted-foreground">
                            Salin format laporan untuk dikirim ke grup WhatsApp
                        </p>
                    </div>
                </div>
                <button
                    class="rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    @click="emit('close')"
                >
                    <X class="size-4" />
                </button>
            </div>

            <!-- Content Body -->
            <div class="flex-1 space-y-4 overflow-y-auto p-5">
                <!-- Status Alert Banner -->
                <div
                    v-if="report.status === 'pending'"
                    class="flex items-center gap-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 p-3 text-xs text-amber-700 dark:text-amber-400"
                >
                    <span
                        class="size-2 animate-ping rounded-full bg-amber-500"
                    ></span>
                    <span>
                        Inputan telah disimpan sebagai
                        <strong>Permintaan</strong> dan sedang
                        <strong>menunggu persetujuan SPV</strong>.
                    </span>
                </div>
                <div
                    v-else-if="report.status === 'approved'"
                    class="flex items-center gap-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-700 dark:text-emerald-400"
                >
                    <Check class="size-4 shrink-0 text-emerald-600" />
                    <span>
                        Transaksi resmi <strong>disetujui</strong> dan stok
                        gudang telah ter-update.
                    </span>
                </div>

                <!-- WhatsApp Preview Box -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-foreground">
                        Preview Format Pesan WhatsApp:
                    </label>
                    <div
                        class="relative max-h-60 overflow-y-auto rounded-xl border border-emerald-500/30 bg-[#075e54]/5 p-3.5 font-mono text-[11px] leading-relaxed whitespace-pre-wrap text-foreground select-all dark:bg-emerald-950/20"
                    >
                        {{ generatedMessage }}
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex flex-col-reverse gap-2 border-t border-border/70 bg-muted/30 p-4 sm:flex-row sm:items-center sm:justify-end"
            >
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground transition-all hover:bg-muted active:scale-95 sm:w-auto"
                    @click="emit('close')"
                >
                    Selesai / Tutup
                </button>
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-border bg-background px-4 py-2 text-xs font-semibold text-foreground transition-all hover:border-emerald-500/40 hover:bg-emerald-500/5 active:scale-95 sm:w-auto"
                    @click="copyText"
                >
                    <component
                        :is="copied ? Check : Copy"
                        :class="[
                            'size-4',
                            copied
                                ? 'text-emerald-500'
                                : 'text-muted-foreground',
                        ]"
                    />
                    <span>{{
                        copied ? 'Teks Berhasil Disalin!' : 'Salin Pesan WA'
                    }}</span>
                </button>
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:brightness-105 active:scale-95 sm:w-auto"
                    @click="sendWhatsApp"
                >
                    <Send class="size-4" />
                    <span>Buka WhatsApp</span>
                </button>
            </div>
        </div>
    </div>
</template>
