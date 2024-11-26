<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <a-config-provider
        :theme="{
            token: {
                colorPrimary: 'rgb(245 158 11)',
            },
        }"
    >
        <div
            class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white"
        >
            <div class="absolute top-6">
                <Link href="/">
                    <img src="/assets/login_logo.png" class="w-[212px]" />
                </Link>
            </div>

            <div
                class="w-full sm:max-w-md mt-6 px-6 overflow-hidden sm:rounded-lg"
            >
                <slot />
            </div>
        </div>
    </a-config-provider>
</template>
