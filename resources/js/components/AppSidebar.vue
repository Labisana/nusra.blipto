<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowDownToLine,
    ArrowUpFromLine,
    Boxes,
    FileBarChart2,
    LayoutGrid,
    RadioTower,
    UserCog,
    Users,
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

const { canViewReports, canManageUsers } = useRole();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
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
        items.push({
            title: 'Laporan',
            href: '/laporan',
            icon: FileBarChart2,
        });
    }

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
