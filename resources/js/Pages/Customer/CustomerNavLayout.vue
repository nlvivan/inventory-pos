<script setup>
import TabNavVue from "@/Components/TabNav.vue";
import CustomerLayout from "@/Layouts/CutomerLayout.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
const page = computed(() => usePage());

const props = defineProps({
    reseller: {
        required: true,
        type: Object,
    },
});

const navigations = computed(() => {
    return [
        {
            name: "Order History",
            href: route("customer.orders"),
            method: "get",
            is_active: page.value.component === "Customer/Orders",
        },
        {
            name: "My Cart",
            href: route("cart.index"),
            method: "get",
            is_active: false,
        },
        {
            name: "Settings",
            href: route("customer.profile.edit"),
            method: "get",
            is_active: page.value.component === "Profile/Edit",
        },
        {
            name: "Logout",
            href: route("logout"),
            method: "post",
            is_active: false,
        },
    ];
});
</script>

<template>
    <CustomerLayout>
        <div class="p-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col mb-8">
                <div class="flex space-x-3">
                    <div>
                        <!-- <span class="text-gray-500">{{
                            reseller?.sub_domain
                        }}</span> -->
                    </div>
                </div>
            </div>
            <div class="flex">
                <a-card class="flex-shrink-0 w-[250px] mr-6 space-y-2">
                    <TabNavVue
                        v-for="(nav, index) in navigations"
                        :active="nav?.is_active"
                        :href="nav?.href"
                        :key="index"
                        :method="nav?.method"
                    >
                        {{ nav?.name }}
                    </TabNavVue>
                </a-card>
                <div class="flex-1 min-w-0">
                    <slot />
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
