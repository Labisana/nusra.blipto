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
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Log in
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Don't have an account?
            <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
        </div>
    </Form>

    <div v-if="demoAccounts?.length" class="mt-6 flex flex-col gap-3">
        <div class="flex items-center gap-3">
            <span class="h-px flex-1 bg-border" />
            <span class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Akun demo</span>
            <span class="h-px flex-1 bg-border" />
        </div>

        <div class="grid gap-2">
            <button
                v-for="account in demoAccounts"
                :key="account.email"
                type="button"
                class="group flex w-full items-center justify-between gap-3 rounded-lg border bg-card px-3 py-2.5 text-left transition-colors hover:border-primary/50 hover:bg-muted/50"
                :title="`Isi email & password ${account.role_label}`"
                @click="useDemoAccount(account)"
            >
                <span class="min-w-0">
                    <span class="flex flex-wrap items-center gap-1.5">
                        <span class="rounded-full border bg-muted px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
                            {{ account.role_label }}
                        </span>
                        <span class="text-[11px] text-muted-foreground">{{ account.description }}</span>
                    </span>
                    <span class="mt-1 block truncate font-mono text-xs text-foreground">{{ account.email }}</span>
                </span>
                <span class="shrink-0 text-xs font-medium text-primary opacity-70 group-hover:opacity-100">Gunakan</span>
            </button>
        </div>

        <p class="text-center text-[11px] text-muted-foreground">
            Klik akun untuk mengisi otomatis. Kata sandi semua akun demo:
            <span class="font-mono text-foreground">password</span>
        </p>
    </div>
</template>
