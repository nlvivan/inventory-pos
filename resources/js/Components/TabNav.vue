<script setup>
// import { Link } from "@inertiajs/vue3";
defineProps({
    page: "",
    active: Boolean,
    method: String,
});
</script>

<template>
    <Link
        :method="method"
        class="flex items-center justify-between px-4 py-2 rounded-md text-gray-800 hover:bg-amber-500"
        :class="{ 'bg-amber-500 font-semibold': active }"
    >
        <a-button type="link">
            <template #icon> </template>
            <span
                class="hover:text-white font-semibold"
                :class="active ? 'text-white' : 'text-black'"
            >
                <slot />

                <div class="items-center">
                    <slot name="right"> </slot>
                </div>
            </span>
        </a-button>
    </Link>
</template>
