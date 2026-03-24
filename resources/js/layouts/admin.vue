<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import type { CommandPaletteGroup, NavigationMenuItem } from '@nuxt/ui';
import { computed } from 'vue';
import { logout } from '@/routes';

const page = usePage();
const user = computed(() => page.props.auth.user);

const searchGroups = computed<CommandPaletteGroup[]>(() => [
    {
        id: 'pages',
        label: 'Pages',
        items: [
            { label: 'Home', icon: 'i-lucide-house', to: '/dashboard' },
            {
                label: 'Users',
                icon: 'i-lucide-users-round',
                to: '/dashboard/users',
            },
            { label: 'Inbox', icon: 'i-lucide-inbox', to: '/dashboard/inbox' },
            {
                label: 'Contacts',
                icon: 'i-lucide-users',
                to: '/dashboard/contacts',
            },
            {
                label: 'Settings',
                icon: 'i-lucide-settings',
                to: '/dashboard/settings',
            },
        ],
    },
    {
        id: 'actions',
        label: 'Actions',
        items: [
            { label: 'New contact', icon: 'i-lucide-user-plus' },
            { label: 'New message', icon: 'i-lucide-message-square-plus' },
        ],
    },
]);

const userMenuItems = computed(() => [
    [
        {
            label: user.value?.name,
            slot: 'account' as const,
            disabled: true,
        },
    ],
    [
        {
            label: 'Log out',
            icon: 'i-lucide-log-out',
            onSelect: () => {
                router.flushAll();
                router.post(logout.url());
            },
        },
    ],
]);

const items: NavigationMenuItem[][] = [
    [
        {
            label: 'Home',
            icon: 'i-lucide-house',
            to: '/dashboard',
        },
        {
            label: 'Users',
            icon: 'i-lucide-users-round',
            to: '/dashboard/users',
        },
        {
            label: 'Inbox',
            icon: 'i-lucide-inbox',
            badge: '4',
        },
        {
            label: 'Contacts',
            icon: 'i-lucide-users',
        },
        {
            label: 'Settings',
            icon: 'i-lucide-settings',
            defaultOpen: true,
            children: [
                { label: 'General' },
                { label: 'Members' },
                { label: 'Notifications' },
            ],
        },
    ],
    [
        {
            label: 'Feedback',
            icon: 'i-lucide-message-circle',
            to: 'https://github.com/nuxt-ui-templates/dashboard',
            target: '_blank',
        },
        {
            label: 'Help & Support',
            icon: 'i-lucide-info',
            to: 'https://github.com/nuxt/ui',
            target: '_blank',
        },
        {
            label: 'Telescope',
            icon: 'i-lucide-telescope',
            to: '/telescope',
            target: '_blank',
        },
    ],
];
</script>

<template>
    <UApp>
        <UDashboardGroup>
            <UDashboardSidebar
                collapsible
                resizable
                :min-size="15"
                :default-size="20"
                :max-size="30"
                :ui="{ footer: 'border-t border-default' }"
            >
                <template #header="{ collapsed }">
                    <div
                        class="flex w-full items-center"
                        :class="
                            collapsed ? 'justify-center' : 'justify-between'
                        "
                    >
                        <AppLogo
                            v-if="!collapsed"
                            class="h-5 w-auto shrink-0"
                        />
                        <UIcon
                            v-else
                            name="i-lucide-zap"
                            class="size-5 text-primary"
                        />
                        <NotificationBell v-if="!collapsed" />
                    </div>
                </template>

                <template #default="{ collapsed }">
                    <UDashboardSearchButton :collapsed="collapsed" />

                    <UNavigationMenu
                        :collapsed="collapsed"
                        :items="items[0]"
                        orientation="vertical"
                    />

                    <UNavigationMenu
                        :collapsed="collapsed"
                        :items="items[1]"
                        orientation="vertical"
                        class="mt-auto"
                    />
                </template>

                <template #footer="{ collapsed }">
                    <UDropdownMenu :items="userMenuItems">
                        <UButton
                            :avatar="{
                                src: user?.avatar,
                                alt: user?.name,
                            }"
                            :label="collapsed ? undefined : user?.name"
                            color="neutral"
                            variant="ghost"
                            class="w-full"
                            :block="collapsed"
                        />

                        <template #account>
                            <div class="text-left">
                                <p class="truncate font-medium">
                                    {{ user?.name }}
                                </p>
                                <p class="truncate text-xs text-muted">
                                    {{ user?.email }}
                                </p>
                            </div>
                        </template>
                    </UDropdownMenu>
                </template>
            </UDashboardSidebar>

            <UDashboardSearch :groups="searchGroups" />

            <slot />
        </UDashboardGroup>
    </UApp>
</template>
