<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { z } from 'zod';
import Layout from '@/layouts/admin.vue';
import UserController from '@/wayfinder/App/Http/Controllers/Admin/UserController';

defineOptions({ layout: Layout });

const schema = z
    .object({
        name: z.string().min(1, 'Name is required'),
        email: z.string().email('Enter a valid email'),
        password: z.string().min(8, 'Use at least 8 characters'),
        password_confirmation: z.string().min(1, 'Confirm your password'),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match',
        path: ['password_confirmation'],
    });

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function onSubmit(): void {
    form.name = state.name ?? '';
    form.email = state.email ?? '';
    form.password = state.password ?? '';
    form.password_confirmation = state.password_confirmation ?? '';

    form.post(UserController.store.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Add user">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>

                <template #right>
                    <UButton
                        color="neutral"
                        variant="ghost"
                        label="Cancel"
                        icon="i-lucide-arrow-left"
                        @click="router.visit(UserController.index.url())"
                    />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <div class="mx-auto w-full max-w-lg p-4 sm:p-6">
                <UCard>
                    <template #header>
                        <h2 class="text-lg font-semibold text-highlighted">
                            User details
                        </h2>
                        <p class="text-sm text-muted">
                            Create a new account. The user can sign in with this
                            email and password.
                        </p>
                    </template>

                    <UForm
                        :schema="schema"
                        :state="state"
                        class="flex flex-col gap-4"
                        @submit="onSubmit"
                    >
                        <UFormField label="Name" name="name" required>
                            <UInput v-model="state.name" autocomplete="name" />
                        </UFormField>

                        <UFormField label="Email" name="email" required>
                            <UInput
                                v-model="state.email"
                                type="email"
                                autocomplete="email"
                            />
                        </UFormField>

                        <UFormField label="Password" name="password" required>
                            <UInput
                                v-model="state.password"
                                type="password"
                                autocomplete="new-password"
                            />
                        </UFormField>

                        <UFormField
                            label="Confirm password"
                            name="password_confirmation"
                            required
                        >
                            <UInput
                                v-model="state.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                            />
                        </UFormField>

                        <div
                            v-if="Object.keys(form.errors).length"
                            class="rounded-md border border-error/30 bg-error/5 px-3 py-2 text-sm text-error"
                        >
                            <ul class="list-inside list-disc space-y-1">
                                <li v-for="(err, key) in form.errors" :key="key">
                                    {{ err }}
                                </li>
                            </ul>
                        </div>

                        <div class="flex justify-end gap-2">
                            <UButton
                                color="neutral"
                                variant="ghost"
                                label="Cancel"
                                type="button"
                                @click="router.visit(UserController.index.url())"
                            />
                            <UButton
                                type="submit"
                                label="Create user"
                                :loading="form.processing"
                            />
                        </div>
                    </UForm>
                </UCard>
            </div>
        </template>
    </UDashboardPanel>
</template>
