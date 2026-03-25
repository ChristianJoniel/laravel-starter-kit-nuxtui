<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';

import AuthLayout from '@/layouts/auth.vue';
import { send } from '@/wayfinder/routes/verification';

defineOptions({ layout: AuthLayout });

const page = usePage();

const form = useForm({});

function onResend() {
    form.post(send.url());
}
</script>

<template>
    <div>
        <UAlert
            v-if="page.props.status === 'verification-link-sent'"
            title="A new verification link has been sent to your email address."
            color="success"
            icon="i-lucide-check-circle"
            class="mb-4"
        />

        <UAuthForm
            title="Verify your email"
            icon="i-lucide-mail-check"
            :loading="form.processing"
            @submit="onResend"
        >
            <template #description>
                Thanks for signing up! Please verify your email address by clicking on the link we just emailed to you.
            </template>

            <template #footer>
                <UButton
                    label="Resend Verification Email"
                    block
                    :loading="form.processing"
                    @click="onResend"
                />
            </template>
        </UAuthForm>
    </div>
</template>
