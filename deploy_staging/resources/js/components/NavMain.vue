<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import { ref, watch } from 'vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

const props = defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl, currentUrl } = useCurrentUrl();

const openMenus = ref<Record<string, boolean>>({});

const hasActiveChild = (item: NavItem): boolean => {
    return Boolean(item.items?.some((sub) => isCurrentUrl(sub.href)));
};

// Auto-expand when a child route is active
watch(
    () => currentUrl.value,
    () => {
        props.items.forEach((item) => {
            if (item.items && hasActiveChild(item)) {
                openMenus.value[item.title] = true;
            }
        });
    },
    { immediate: true },
);
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Collapsible Submenu Item -->
                <Collapsible
                    v-if="item.items && item.items.length"
                    v-model:open="openMenus[item.title]"
                    as-child
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :tooltip="item.title"
                                :is-active="hasActiveChild(item)"
                                class="cursor-pointer"
                            >
                                <component :is="item.icon" v-if="item.icon" />
                                <span class="font-medium">{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="subItem in item.items"
                                    :key="subItem.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="isCurrentUrl(subItem.href)"
                                    >
                                        <Link :href="subItem.href">
                                            <component
                                                :is="subItem.icon"
                                                v-if="subItem.icon"
                                                class="size-4 shrink-0"
                                            />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Regular Nav Item -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
