<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/auth.vue';
import { store } from '@/routes/login';

defineOptions({ layout: AuthLayout });
 
const page = usePage();
const toast = useToast();


const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const fields = [
    { name: 'email', type: 'text' as const, label: 'Email', placeholder: 'Enter your email', autofocus: true },
    { name: 'password', type: 'password' as const, label: 'Password', placeholder: 'Enter your password' },
    { name: 'remember', type: 'checkbox' as const, label: 'Remember me' },
];

const providers = [
    {
        label: 'Google',
        icon: 'i-simple-icons:google',
        color: 'neutral' as const,
        variant: 'subtle' as const,
        block: true,
        onClick: () => toast.add({ title: 'Google login is not yet available', color: 'warning' }),
    },
    {
        label: 'GitHub',
        icon: 'i-simple-icons:github',
        color: 'neutral' as const,
        variant: 'subtle' as const,
        block: true,
        onClick: () => toast.add({ title: 'GitHub login is not yet available', color: 'warning' }),
    },
];

function onSubmit(event: { data: { email: string; password: string; remember?: boolean } }) {
    form.email = event.data.email;
    form.password = event.data.password;
    form.remember = event.data.remember ?? false;

    form.post(store.url(), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div>
        <UAlert
            v-if="page.props.status"
            :title="page.props.status"
            color="success"
            icon="i-lucide-check-circle"
            class="mb-4"
        />

        <UAuthForm
            title="Welcome back!"
            icon="i-lucide-lock"
            :fields="fields"
            :providers="providers"

            :submit="{ label: 'Sign in', block: true }"
            :loading="form.processing"
            @submit="onSubmit"
        >
            <template #description>
                Don't have an account?
                <ULink class="text-primary font-medium">
                    Sign up
                </ULink>
            </template>

            <template #password-hint>
                <ULink
                    :to="'/forgot-password'"
                    class="text-primary text-sm font-medium"
                >
                    Forgot password?
                </ULink>
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
                    By signing in, you agree to our
                    <ULink class="text-primary font-medium">Terms of Service</ULink>
                    and
                    <ULink class="text-primary font-medium">Privacy Policy</ULink>.
                </p>
            </template>
        </UAuthForm>
    </div>
</template>
