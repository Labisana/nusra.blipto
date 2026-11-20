<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
    FileBarChart2,
    LayoutGrid,
    RadioTower,
    ShieldCheck,
    UserCog,
    Users,
    Warehouse,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useRole } from '@/composables/useRole';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const { canViewReports, canManage, canManageUsers, canApprove } = useRole();
const page = usePage();

const pendingCount = computed<number>(() => {
    return (page.props as any).pending_approvals_count ?? 0;
});

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (canApprove.value) {
        items.push({
            title: 'Approval Hub',
            href: '/persetujuan',
            icon: ShieldCheck,
            badge:
                pendingCount.value > 0 ? String(pendingCount.value) : undefined,
        });
    }

    const gudangSubItems: NavItem[] = [
        {
            title: 'Barang Masuk',
            href: '/barang-masuk',
            icon: ArrowDownToLine,
        },
        {
            title: 'Barang Keluar',
            href: '/barang-keluar',
            icon: ArrowUpFromLine,
        },
        {
            title: 'Stock',
            href: '/stok',
            icon: Boxes,
        },
    ];

    if (canViewReports.value) {
        gudangSubItems.push({
            title: 'Laporan',
            href: '/laporan',
            icon: FileBarChart2,
        });
    }

    items.push({
        title: 'Gudang',
        href: '/stok',
        icon: Warehouse,
        items: gudangSubItems,
    });

    items.push(
        {
            title: 'Pelanggan',
            href: '/pelanggan',
            icon: Users,
        },
        {
            title: 'Data BTS/BSC',
            href: '/bts-bsc',
            icon: RadioTower,
        },
    );

    if (canManageUsers.value) {
        items.push({
            title: 'Pengguna',
            href: '/pengguna',
            icon: UserCog,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
