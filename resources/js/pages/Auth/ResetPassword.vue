<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { z } from 'zod';
import AuthLayout from '@/layouts/auth.vue';
import { update } from '@/routes/password';

defineOptions({ layout: AuthLayout });

const props = defineProps<{ token: string; email: string }>();

const schema = z
    .object({
        password: z.string().min(8, 'Password must be at least 8 characters'),
        password_confirmation: z.string(),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match',
        path: ['password_confirmation'],
    });

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const fields = [
    { name: 'email', type: 'text' as const, label: 'Email', disabled: true, defaultValue: props.email },
    { name: 'password', type: 'password' as const, label: 'Password', placeholder: 'Enter your new password', autofocus: true },
    { name: 'password_confirmation', type: 'password' as const, label: 'Confirm Password', placeholder: 'Confirm your new password' },
];

function onSubmit(event: { data: { password: string; password_confirmation: string } }) {
    form.password = event.data.password;
    form.password_confirmation = event.data.password_confirmation;

    form.post(update.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <div>
        <UAuthForm
            title="Reset Password"
            icon="i-lucide-lock-keyhole"
            :fields="fields"
            :schema="schema"
            :submit="{ label: 'Reset Password', block: true }"
            :loading="form.processing"
            @submit="onSubmit"
        >
            <template #validation>
                <UAlert
                    v-if="form.errors.email"
                    color="error"
                    icon="i-lucide-alert-circle"
                    :title="form.errors.email"
                />
                <UAlert
                    v-if="form.errors.password"
                    color="error"
                    icon="i-lucide-alert-circle"
                    :title="form.errors.password"
                />
            </template>

            <template #footer>
                <p class="text-center text-sm text-muted">
                    <ULink
                        to="/login"
                        class="text-primary font-medium"
                    >
                        Back to login
                    </ULink>
                </p>
            </template>
        </UAuthForm>
    </div>
</template>
