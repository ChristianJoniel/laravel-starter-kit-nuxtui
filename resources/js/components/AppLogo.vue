<script setup lang="ts">
defineProps<{
  collapsed?: boolean
}>()
</script>

<template>
  <div class="flex items-center gap-2 font-semibold">
    <UIcon name="i-lucide-zap" class="size-5 shrink-0 text-primary" />
    <span v-if="!collapsed" class="truncate">Sutra</span>
  </div>
</template>
