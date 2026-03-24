<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import type { TableColumn } from '@nuxt/ui';
import { computed, ref } from 'vue';
import Layout from '@/layouts/admin.vue';
import type { Paginated, UserListItem } from '@/types/admin';
import UserController from '@/wayfinder/App/Http/Controllers/Admin/UserController';

defineOptions({ layout: Layout });

defineProps<{
    users: Paginated<UserListItem>;
}>();

const page = usePage();
const status = computed(() => page.props.status as string | undefined);
const authUserId = computed(() => page.props.auth.user?.id as number | undefined);

const deleteTarget = ref<UserListItem | null>(null);
const deleteOpen = computed({
    get: () => deleteTarget.value !== null,
    set: (v: boolean) => {
        if (!v) {
            deleteTarget.value = null;
        }
    },
});

const columns: TableColumn<UserListItem>[] = [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { id: 'verified', header: 'Status' },
    { id: 'actions', header: '' },
];

function onPageChange(p: number): void {
    router.get(
        UserController.index.url({ query: { page: p } }),
        {},
        { preserveState: true, preserveScroll: true },
    );
}

function requestDelete(user: UserListItem): void {
    deleteTarget.value = user;
}

function confirmDelete(): void {
    if (!deleteTarget.value) {
        return;
    }

    router.delete(UserController.destroy.url({ user: deleteTarget.value.id }), {
        preserveScroll: true,
        onFinish: () => {
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Users">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>

                <template #right>
                    <UButton
                        icon="i-lucide-plus"
                        label="Add user"
                        @click="router.visit(UserController.create.url())"
                    />
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <div class="flex flex-1 flex-col gap-4 p-4 sm:p-6">
                <UAlert
                    v-if="status"
                    color="info"
                    variant="subtle"
                    :title="status"
                    icon="i-lucide-info"
                />

                <UCard :ui="{ body: 'p-0 sm:p-0' }">
                    <UTable :data="users.data" :columns="columns" class="min-w-full">
                        <template #verified-cell="{ row }">
                            <UBadge
                                :color="
                                    row.original.email_verified_at
                                        ? 'success'
                                        : 'neutral'
                                "
                                variant="subtle"
                            >
                                {{
                                    row.original.email_verified_at
                                        ? 'Verified'
                                        : 'Unverified'
                                }}
                            </UBadge>
                        </template>

                        <template #actions-cell="{ row }">
                            <div class="flex items-center justify-end gap-1">
                                <UButton
                                    icon="i-lucide-pencil"
                                    color="neutral"
                                    variant="ghost"
                                    size="sm"
                                    square
                                    @click="
                                        router.visit(
                                            UserController.edit.url({
                                                user: row.original.id,
                                            }),
                                        )
                                    "
                                />

                                <UButton
                                    icon="i-lucide-trash-2"
                                    color="error"
                                    variant="ghost"
                                    size="sm"
                                    square
                                    :disabled="authUserId === row.original.id"
                                    :title="
                                        authUserId === row.original.id
                                            ? 'You cannot delete your own account'
                                            : 'Delete user'
                                    "
                                    @click="requestDelete(row.original)"
                                />
                            </div>
                        </template>
                    </UTable>

                    <div
                        v-if="users.last_page > 1"
                        class="flex justify-center border-t border-default p-4"
                    >
                        <UPagination
                            :page="users.current_page"
                            :items-per-page="users.per_page"
                            :total="users.total"
                            @update:page="onPageChange"
                        />
                    </div>
                </UCard>
            </div>

            <UModal
                v-model:open="deleteOpen"
                title="Delete user"
                description="This action cannot be undone."
            >
                <template #body>
                    <p class="text-sm text-muted">
                        Remove
                        <span class="font-medium text-default">{{
                            deleteTarget?.name
                        }}</span>
                        ({{ deleteTarget?.email }})?
                    </p>
                </template>

                <template #footer>
                    <UButton
                        color="neutral"
                        variant="ghost"
                        label="Cancel"
                        @click="deleteOpen = false"
                    />
                    <UButton
                        color="error"
                        label="Delete"
                        @click="confirmDelete"
                    />
                </template>
            </UModal>
        </template>
    </UDashboardPanel>
</template>
