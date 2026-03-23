<script setup lang="ts">
import Layout from '@/layouts/admin.vue';

defineOptions({ layout: Layout });
</script>

<template>
    <UDashboardPanel>
        <template #header>
            <UDashboardNavbar title="Dashboard" />
        </template>

        <template #body>
            <div class="flex flex-1 items-center justify-center">
                <div class="text-center">
                    <UIcon
                        name="i-lucide-layout-dashboard"
                        class="mx-auto size-12 text-muted"
                    />
                    <h2 class="mt-4 text-lg font-semibold">
                        Welcome to your dashboard
                    </h2>
                    <p class="mt-1 text-sm text-muted">
                        This is where your admin content will live.
                    </p>
                </div>
            </div>
        </template>
    </UDashboardPanel>
</template>
