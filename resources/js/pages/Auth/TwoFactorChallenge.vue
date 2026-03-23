<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { z } from 'zod';
import AuthLayout from '@/layouts/auth.vue';
import { store } from '@/routes/two-factor/login';

defineOptions({ layout: AuthLayout });

const isRecoveryMode = ref(false);

const codeSchema = z.object({
    code: z.array(z.string().min(1)).length(6, 'Please enter all 6 digits'),
});

const recoverySchema = z.object({
    recovery_code: z.string().min(1, 'Recovery code is required'),
});

const schema = computed(() => isRecoveryMode.value ? recoverySchema : codeSchema);

const form = useForm({
    code: '',
    recovery_code: '',
});

const codeFields = [
    { name: 'code', type: 'otp' as const, label: 'Authentication Code', length: 6, otp: true },
];

const recoveryFields = [
    { name: 'recovery_code', type: 'text' as const, label: 'Recovery Code', placeholder: 'Enter your recovery code', autofocus: true },
];

const fields = computed(() => isRecoveryMode.value ? recoveryFields : codeFields);

function toggleMode() {
    isRecoveryMode.value = !isRecoveryMode.value;
    form.reset();
    form.clearErrors();
}

function onSubmit(event: { data: Record<string, unknown> }) {
    if (isRecoveryMode.value) {
        form.code = '';
        form.recovery_code = event.data.recovery_code as string;
    } else {
        form.code = (event.data.code as string[]).join('');
        form.recovery_code = '';
    }

    form.post(store.url());
}
</script>

<template>
    <div>
        <UAuthForm
            :key="isRecoveryMode ? 'recovery' : 'code'"
            :title="isRecoveryMode ? 'Recovery Code' : 'Two-Factor Authentication'"
            icon="i-lucide-shield-check"
            :fields="fields"
            :schema="schema"
            :submit="{ label: 'Verify', block: true }"
            :loading="form.processing"
            @submit="onSubmit"
        >
            <template #description>
                <span v-if="isRecoveryMode">
                    Enter one of your emergency recovery codes to verify your identity.
                </span>
                <span v-else>
                    Enter the 6-digit code from your authenticator app to verify your identity.
                </span>
            </template>

            <template #validation>
                <UAlert
                    v-if="form.errors.code"
                    color="error"
                    icon="i-lucide-alert-circle"
                    :title="form.errors.code"
                />
                <UAlert
                    v-if="form.errors.recovery_code"
                    color="error"
                    icon="i-lucide-alert-circle"
                    :title="form.errors.recovery_code"
                />
            </template>

            <template #footer>
                <UButton
                    variant="link"
                    color="primary"
                    block
                    @click="toggleMode"
                >
                    {{ isRecoveryMode ? 'Use authentication code' : 'Use a recovery code' }}
                </UButton>
            </template>
        </UAuthForm>
    </div>
</template>
