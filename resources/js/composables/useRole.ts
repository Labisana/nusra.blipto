import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export type AppRole = 'manager' | 'spv' | 'admin' | 'teknisi';

type SharedProps = {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            role: AppRole;
            can_manage: boolean;
            can_view_reports: boolean;
            can_manage_users: boolean;
        } | null;
    };
};

const roleLabels: Record<AppRole, string> = {
    manager: 'Manager (Full Akses)',
    spv: 'SPV (Full Akses)',
    admin: 'Admin (Kelola Data)',
    teknisi: 'Teknisi (Input Transaksi)',
};

export function useRole() {
    const page = usePage<SharedProps>();
    const user = computed(() => page.props.auth?.user ?? null);
    const role = computed<AppRole>(() => user.value?.role ?? 'teknisi');
    const roleLabel = computed(() => roleLabels[role.value]);
    const isTeknisi = computed(() => role.value === 'teknisi');
    const isSpv = computed(() => role.value === 'spv');
    const isManager = computed(() => role.value === 'manager');
    const canManage = computed(
        () => user.value?.can_manage ?? role.value !== 'teknisi',
    );
    const canViewReports = computed(
        () => user.value?.can_view_reports ?? role.value !== 'teknisi',
    );
    const canManageUsers = computed(
        () =>
            user.value?.can_manage_users ??
            (role.value === 'manager' || role.value === 'spv'),
    );

    return {
        role,
        roleLabel,
        user,
        isTeknisi,
        isSpv,
        isManager,
        canManage,
        canViewReports,
        canManageUsers,
    };
}
