<script setup lang="ts">
import Layout from '@/layouts/admin.vue'

defineOptions({ layout: Layout })
</script>

<template>
  <UDashboardPanel>
    <template #header>
      <UDashboardNavbar title="Dashboard" />
    </template>

    <template #body>
      <div class="flex flex-1 items-center justify-center">
        <div class="text-center">
          <UIcon name="i-lucide-layout-dashboard" class="text-muted mx-auto size-12" />
          <h2 class="mt-4 text-lg font-semibold">Welcome to your dashboard</h2>
          <p class="text-muted mt-1 text-sm">This is where your admin content will live.</p>
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
