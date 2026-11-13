import { computed, ref } from 'vue';

export type AppRole = 'spv' | 'admin' | 'teknisi';

const role = ref<AppRole>('spv');

export function useRole() {
    const isTeknisi = computed(() => role.value === 'teknisi');
    const canManage = computed(() => role.value === 'spv' || role.value === 'admin');

    return {
        role,
        isTeknisi,
        canManage,
    };
}
