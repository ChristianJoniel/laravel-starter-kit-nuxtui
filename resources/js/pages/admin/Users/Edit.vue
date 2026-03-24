<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { z } from 'zod';
import Layout from '@/layouts/admin.vue';
import type { User } from '@/types/auth';
import UserController from '@/wayfinder/App/Http/Controllers/Admin/UserController';

defineOptions({ layout: Layout });

const props = defineProps<{
    user: Pick<
        User,
        | 'id'
        | 'name'
        | 'email'
        | 'email_verified_at'
        | 'created_at'
        | 'updated_at'
    >;
}>();

const schema = z
    .object({
        name: z.string().min(1, 'Name is required'),
        email: z.string().email('Enter a valid email'),
        password: z.string().optional(),
        password_confirmation: z.string().optional(),
    })
    .superRefine((data, ctx) => {
        if (data.password && data.password.length > 0 && data.password.length < 8) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                message: 'Use at least 8 characters',
                path: ['password'],
            });
        }

        if (data.password && data.password.length > 0) {
            if (data.password !== data.password_confirmation) {
                ctx.addIssue({
                    code: z.ZodIssueCode.custom,
                    message: 'Passwords do not match',
                    path: ['password_confirmation'],
                });
            }
        }
    });

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
});

watch(
    () => props.user,
    (u) => {
        state.name = u.name;
        state.email = u.email;
    },
);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
});

function onSubmit(): void {
    form.name = state.name ?? '';
    form.email = state.email ?? '';
    form.password = state.password ?? '';
    form.password_confirmation = state.password_confirmation ?? '';

    form.put(UserController.update.url({ user: props.user.id }), {
        preserveScroll: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            state.password = '';
            state.password_confirmation = '';
        },
    });
}
</script>

<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar :title="`Edit ${user.name}`">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>

                <template #right>
                    <UButton
                        color="neutral"
                        variant="ghost"
                        label="Back to list"
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
                            Account
                        </h2>
                        <p class="text-sm text-muted">
                            Update name, email, or set a new password. Leave
                            password blank to keep the current one.
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

                        <UFormField
                            label="New password"
                            name="password"
                            hint="Optional — only fill in to change the password"
                        >
                            <UInput
                                v-model="state.password"
                                type="password"
                                autocomplete="new-password"
                            />
                        </UFormField>

                        <UFormField
                            label="Confirm new password"
                            name="password_confirmation"
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
                                label="Save changes"
                                :loading="form.processing"
                            />
                        </div>
                    </UForm>
                </UCard>
            </div>
        </template>
    </UDashboardPanel>
</template>
