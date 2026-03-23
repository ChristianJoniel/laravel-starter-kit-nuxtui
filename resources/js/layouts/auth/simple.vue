<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <UApp>
        <div class="relative flex min-h-svh items-center justify-center px-4 py-12">
            <UColorModeButton class="absolute top-4 right-4" />

            <div class="w-full max-w-sm">
                <div class="mb-8 flex justify-center">
                    <Link href="/">
                        <AppLogo />
                    </Link>
                </div>

                <slot />
            </div>
        </div>
    </UApp>
</template>
