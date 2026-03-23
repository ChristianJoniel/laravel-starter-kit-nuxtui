<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { z } from 'zod';
import AppLayout from '@/layouts/app.vue';
import { email } from '@/routes/password';

defineOptions({ layout: AppLayout });

const page = usePage();

const schema = z.object({
    email: z.string().email('Please enter a valid email address'),
});

const form = useForm({
    email: '',
});

const fields = [
    { name: 'email', type: 'text' as const, label: 'Email', placeholder: 'Enter your email', autofocus: true },
];

function onSubmit(event: { data: { email: string } }) {
    form.email = event.data.email;

    form.post(email.url());
}
</script>

<template>
    <div class="flex min-h-[calc(100vh-var(--ui-header-height)-var(--ui-footer-height,0px))] items-center justify-center">
        <div class="w-full max-w-sm">
            <UAlert
                v-if="page.props.status"
                :title="page.props.status"
                color="success"
                icon="i-lucide-check-circle"
                class="mb-4"
            />

            <UAuthForm
                title="Forgot password?"
                icon="i-lucide-key-round"
                :fields="fields"
                :schema="schema"
                :submit="{ label: 'Send Reset Link', block: true }"
                :loading="form.processing"
                @submit="onSubmit"
            >
                <template #description>
                    Enter your email and we'll send you a reset link.
                </template>

                <template #validation>
                    <UAlert
                        v-if="form.errors.email"
                        color="error"
                        icon="i-lucide-alert-circle"
                        :title="form.errors.email"
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
    </div>
</template>
