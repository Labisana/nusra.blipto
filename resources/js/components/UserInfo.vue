<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { useRole } from '@/composables/useRole';
import type { User } from '@/types';

type Props = {
    user?: User | null;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    user: null,
    showEmail: false,
});

const { getInitials } = useInitials();
const { role } = useRole();

const displayName = computed(() => props.user?.name ?? 'Guest');
const displayEmail = computed(() => props.user?.email ?? '');

const showAvatar = computed(
    () => !!props.user?.avatar && props.user.avatar !== '',
);

const roleBadgeColor = computed(() => {
    switch (role.value) {
        case 'spv':
            return 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800';
        case 'admin':
            return 'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-400 border-orange-200 dark:border-orange-900';
        default:
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900';
    }
});
</script>

<template>
    <div class="relative">
        <Avatar
            class="ring-1.5 size-8 overflow-hidden rounded-lg ring-[#fb4f00]/30 transition-transform group-hover:scale-105"
        >
            <AvatarImage
                v-if="showAvatar"
                :src="user!.avatar!"
                :alt="displayName"
            />
            <AvatarFallback
                class="rounded-lg bg-gradient-to-br from-[#fb4f00]/15 to-[#fb4f00]/5 font-bold text-[#fb4f00]"
            >
                {{ getInitials(displayName) }}
            </AvatarFallback>
        </Avatar>
        <span
            class="absolute -right-0.5 -bottom-0.5 size-2 rounded-full bg-emerald-500 ring-2 ring-background"
        ></span>
    </div>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <div class="flex items-center gap-1.5">
            <span class="truncate font-semibold">{{ displayName }}</span>
            <span
                class="py-0.2 rounded border px-1 text-[9px] font-bold tracking-wider uppercase"
                :class="roleBadgeColor"
            >
                {{ role }}
            </span>
        </div>
        <span
            v-if="showEmail && displayEmail"
            class="truncate text-xs text-muted-foreground"
            >{{ displayEmail }}</span
        >
    </div>
</template>
