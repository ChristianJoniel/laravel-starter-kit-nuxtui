<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <UApp>
        <div class="grid h-dvh lg:grid-cols-2">
            <div class="hidden items-center justify-center bg-zinc-900 lg:flex">
                <Link href="/">
                    <AppLogo class="text-white" />
                </Link>
            </div>

            <div class="relative flex items-center justify-center px-4 py-12">
                <UColorModeButton class="absolute top-4 right-4" />

                <div class="w-full max-w-sm">
                    <div class="mb-8 flex justify-center lg:hidden">
                        <Link href="/">
                            <AppLogo />
                        </Link>
                    </div>

                    <slot />
                </div>
            </div>
        </div>
    </UApp>
</template>
