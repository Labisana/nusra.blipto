<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import PasskeyVerify from '@/components/PasskeyVerify.vue';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

type DemoAccount = {
    role: string;
    role_label: string;
    description: string;
    name: string;
    email: string;
    password: string;
};

const props = defineProps<{
    status?: string;
    canResetPassword: boolean;
    demoAccounts?: DemoAccount[];
}>();

const email = ref('');
const password = ref('');

function useDemoAccount(account: DemoAccount) {
    email.value = account.email;
    password.value = account.password;
}
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    v-model="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Password</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    v-model="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Remember me</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full bg-gradient-to-r from-[#fb4f00] to-[#ff6b26] font-semibold text-white shadow-md shadow-[#fb4f00]/25 transition-all hover:from-[#e04600] hover:to-[#fb4f00] hover:shadow-lg hover:shadow-[#fb4f00]/30 active:scale-[0.99]"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Masuk ke Sistem
            </Button>
        </div>

        <div class="text-center text-xs text-muted-foreground">
            Belum memiliki akun?
            <TextLink
                :href="register()"
                :tabindex="5"
                class="ml-1 font-semibold text-[#fb4f00] hover:underline"
                >Daftar Akun</TextLink
            >
        </div>
    </Form>

    <div v-if="demoAccounts?.length" class="mt-6 flex flex-col gap-3">
        <div class="flex items-center gap-3">
            <span class="h-px flex-1 bg-border/80" />
            <span
                class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                >Akses Cepat Demo</span
            >
            <span class="h-px flex-1 bg-border/80" />
        </div>

        <div class="grid gap-2">
            <button
                v-for="account in demoAccounts"
                :key="account.email"
                type="button"
                class="group relative flex w-full items-center justify-between gap-3 overflow-hidden rounded-xl border border-border/80 bg-background/60 p-3 text-left transition-all duration-200 hover:-translate-y-0.5 hover:border-[#fb4f00]/40 hover:bg-[#fb4f00]/5 hover:shadow-sm"
                :title="`Isi email & password ${account.role_label}`"
                @click="useDemoAccount(account)"
            >
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span
                            class="rounded-full px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase"
                            :class="
                                account.role === 'spv'
                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300'
                                    : account.role === 'admin'
                                      ? 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400'
                                      : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
                            "
                        >
                            {{ account.role_label }}
                        </span>
                        <span
                            class="truncate text-[11px] text-muted-foreground"
                            >{{ account.description }}</span
                        >
                    </div>
                    <span
                        class="mt-1 block truncate font-mono text-xs font-medium text-foreground"
                        >{{ account.email }}</span
                    >
                </div>
                <div
                    class="flex shrink-0 items-center gap-1 rounded-md bg-[#fb4f00]/10 px-2 py-1 text-xs font-semibold text-[#fb4f00] transition-colors group-hover:bg-[#fb4f00] group-hover:text-white"
                >
                    <span>Pilih</span>
                    <span class="text-xs">&rarr;</span>
                </div>
            </button>
        </div>

        <p class="text-center text-[11px] text-muted-foreground">
            Klik akun untuk mengisi otomatis. Kata sandi default:
            <span
                class="rounded bg-muted px-1.5 py-0.5 font-mono font-semibold text-foreground"
                >password</span
            >
        </p>
    </div>
</template>
