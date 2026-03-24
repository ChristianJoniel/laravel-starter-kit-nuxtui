<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { computed, ref } from 'vue';
import type { AppNotification, NotificationData } from '@/types/notification';
import {
    index,
    markAllAsRead,
    markAsRead,
} from '@/wayfinder/App/Http/Controllers/Admin/NotificationController';

const page = usePage();
const user = computed(() => page.props.auth.user);
const unreadCount = ref(page.props.auth.unreadNotificationsCount);

const notifications = ref<AppNotification[]>([]);
const isOpen = ref(false);
const isLoading = ref(false);

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

function timeAgo(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (seconds < 60) {
        return 'just now';
    }

    const minutes = Math.floor(seconds / 60);

    if (minutes < 60) {
        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
    }

    const hours = Math.floor(minutes / 60);

    if (hours < 24) {
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    }

    const days = Math.floor(hours / 24);

    return `${days} day${days !== 1 ? 's' : ''} ago`;
}

async function fetchNotifications(): Promise<void> {
    isLoading.value = true;

    try {
        const response = await fetch(index.url(), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        const data = await response.json();
        notifications.value = data.notifications;
        unreadCount.value = data.unreadCount;
    } finally {
        isLoading.value = false;
    }
}

async function handleMarkAsRead(notification: AppNotification): Promise<void> {
    if (!notification.read_at) {
        await fetch(markAsRead.url(notification.id), {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'same-origin',
        });
        notification.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    }

    if (notification.data.action_url) {
        isOpen.value = false;
        router.visit(notification.data.action_url);
    }
}

async function handleMarkAllAsRead(): Promise<void> {
    await fetch(markAllAsRead.url(), {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
        credentials: 'same-origin',
    });
    notifications.value.forEach(
        (n) => (n.read_at = n.read_at ?? new Date().toISOString()),
    );
    unreadCount.value = 0;
}

function onPopoverUpdate(value: boolean): void {
    isOpen.value = value;

    if (value) {
        fetchNotifications();
    }
}

useEchoNotification<NotificationData>(
    `App.Models.User.${user.value.id}`,
    (notification) => {
        notifications.value.unshift({
            id: notification.id,
            type: notification.type,
            data: {
                title: notification.title,
                body: notification.body,
                icon: notification.icon,
                action_url: notification.action_url,
            },
            read_at: null,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        });
        unreadCount.value++;
    },
);
</script>

<template>
    <UPopover
        :open="isOpen"
        :content="{ side: 'bottom', align: 'end', sideOffset: 8 }"
        @update:open="onPopoverUpdate"
    >
        <template #default>
            <button
                type="button"
                class="hover:text-foreground relative inline-flex items-center justify-center rounded-md p-1.5 text-muted transition-colors"
            >
                <UIcon name="i-lucide-bell" class="size-5" />
                <UBadge
                    v-if="unreadCount > 0"
                    color="error"
                    :label="String(unreadCount)"
                    size="xs"
                    class="absolute -top-1 -right-1 min-w-5 justify-center"
                />
            </button>
        </template>

        <template #content>
            <div class="w-80">
                <div
                    class="flex items-center justify-between border-b border-default px-4 py-3"
                >
                    <span class="text-sm font-semibold">Notifications</span>
                    <UButton
                        v-if="unreadCount > 0"
                        label="Mark all read"
                        variant="link"
                        color="primary"
                        size="xs"
                        @click="handleMarkAllAsRead"
                    />
                </div>

                <div
                    v-if="isLoading"
                    class="flex items-center justify-center py-8"
                >
                    <UIcon
                        name="i-lucide-loader-2"
                        class="size-5 animate-spin text-muted"
                    />
                </div>

                <div
                    v-else-if="notifications.length === 0"
                    class="px-4 py-8 text-center text-sm text-muted"
                >
                    No notifications yet
                </div>

                <div v-else class="max-h-80 overflow-y-auto">
                    <button
                        v-for="notification in notifications"
                        :key="notification.id"
                        type="button"
                        class="flex w-full items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-elevated/50"
                        :class="{
                            'bg-primary/5': !notification.read_at,
                        }"
                        @click="handleMarkAsRead(notification)"
                    >
                        <UIcon
                            :name="
                                notification.data.icon || 'i-lucide-bell-ring'
                            "
                            class="mt-0.5 size-5 shrink-0"
                            :class="
                                notification.read_at
                                    ? 'text-muted'
                                    : 'text-primary'
                            "
                        />
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm"
                                :class="{
                                    'font-medium': !notification.read_at,
                                }"
                            >
                                {{ notification.data.title }}
                            </p>
                            <p class="truncate text-xs text-muted">
                                {{ notification.data.body }}
                            </p>
                            <p class="mt-1 text-xs text-muted">
                                {{ timeAgo(notification.created_at) }}
                            </p>
                        </div>
                        <span
                            v-if="!notification.read_at"
                            class="mt-2 size-2 shrink-0 rounded-full bg-primary"
                        />
                    </button>
                </div>
            </div>
        </template>
    </UPopover>
</template>
