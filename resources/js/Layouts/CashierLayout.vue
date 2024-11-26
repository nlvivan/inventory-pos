<template>
    <a-config-provider
        :theme="{
            token: {
                colorPrimary: 'rgb(245 158 11)',
            },
        }"
    >
        <div class="min-h-full">
            <Disclosure as="nav" class="bg-white py-4" v-slot="{ open }">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex gap-2">
                                    <img
                                        src="/assets/IMG_5472 (1).png"
                                        class="w-8"
                                    />
                                    <img
                                        src="/assets/IMG_5472.png"
                                        class="w-36 mt-2"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <Link
                                    v-for="item in navigation"
                                    :key="item.name"
                                    :href="item.href"
                                    :class="[
                                        item.current
                                            ? 'bg-amber-500 text-white'
                                            : 'text-[#1C486F] hover:bg-amber-300 hover:text-white',
                                        'rounded-md px-3 py-2 text-sm font-medium',
                                    ]"
                                    :aria-current="
                                        item.current ? 'page' : undefined
                                    "
                                    >{{ item.name }}</Link
                                >
                            </div>
                            <div class="ml-4 flex items-center md:ml-6">
                                <a-badge
                                    :count="$page.props.auth.notificationsCount"
                                >
                                    <a-button
                                        @click="showNotificationDrawer = true"
                                        shape="circle"
                                        type="button"
                                        class="bg-white p-1 text-[#1C486F] hover:text-[#1C486F] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                    >
                                        <span class="absolute -inset-1.5" />
                                        <span class="sr-only"
                                            >View notifications</span
                                        >
                                        <BellIcon
                                            class="h-6 w-6"
                                            aria-hidden="true"
                                        />
                                    </a-button>
                                </a-badge>

                                <!-- Profile dropdown -->
                                <Menu as="div" class="relative ml-3">
                                    <div>
                                        <MenuButton
                                            class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                        >
                                            <span class="absolute -inset-1.5" />
                                            <span class="sr-only"
                                                >Open user menu</span
                                            >
                                            <img
                                                class="h-8 w-8 rounded-full"
                                                :src="user.imageUrl"
                                                alt=""
                                            />
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <MenuItems
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                        >
                                            <MenuItem
                                                v-for="item in userNavigation"
                                                :key="item.name"
                                                v-slot="{ active }"
                                            >
                                                <Link
                                                    :href="item.href"
                                                    :method="item.method"
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100'
                                                            : '',
                                                        'block px-4 py-2 text-sm text-gray-700',
                                                    ]"
                                                    >{{ item.name }}</Link
                                                >
                                            </MenuItem>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                        </div>
                    </div>
                </div>
            </Disclosure>

            <!-- <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h1>
            </div>
        </header> -->
            <main>
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>

            <a-drawer
                title="Notifications"
                placement="right"
                :closable="false"
                :open="showNotificationDrawer"
                @close="showNotificationDrawer = false"
            >
                <div
                    v-for="notification in $page.props.auth.notifications"
                    :key="notification.id"
                >
                    <div
                        class="flex items-center justify-between border-2 p-4"
                        style="border-bottom: 1px solid rgb(156 163 175)"
                    >
                        <div
                            class="flex gap-4"
                            v-if="notification.type === 'expiry_date'"
                        >
                            <div class="">
                                <WarningOutlined
                                    class="text-amber-500 text-2xl"
                                />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="font-medium text-gray-500">
                                    {{ notification.data.product_name }}
                                    is nearly to expire
                                </div>
                                <div class="text-sm text-gray-500">
                                    Expiry Date:
                                    {{ notification.data.expiry_date }}
                                </div>
                                <div
                                    class="text-sm text-amber-500 cursor-pointer"
                                >
                                    Mark as Read
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex gap-4"
                            v-if="notification.type === 'low_stock'"
                        >
                            <div class="">
                                <WarningOutlined
                                    class="text-amber-500 text-2xl"
                                />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="font-medium text-gray-500">
                                    {{ notification.data.product_name }}
                                    is nearly out of stock
                                </div>
                                <div class="text-sm text-gray-500">
                                    Remaining Stock:
                                    {{ notification.data.remaining_stock }}
                                </div>
                                <div
                                    class="text-sm text-amber-500 cursor-pointer"
                                >
                                    Mark as Read
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex gap-4"
                            v-if="notification.type === 'new_order'"
                        >
                            <div class="">
                                <InfoCircleOutlined
                                    class="text-blue-500 text-2xl"
                                />
                            </div>
                            <div class="flex flex-col gap-2">
                                <Link
                                    :href="
                                        route(
                                            'cashier.orders.show',
                                            notification.data.order_id
                                        )
                                    "
                                >
                                    <div class="font-medium text-gray-500">
                                        {{ notification.data.customer_name }}
                                        has new order
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Email:
                                        {{ notification.data.customer_email }}
                                    </div>
                                </Link>
                                <div
                                    class="text-sm text-amber-500 cursor-pointer"
                                >
                                    Mark as Read
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a-drawer>
        </div>
    </a-config-provider>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from "@headlessui/vue";
import { Bars3Icon, BellIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { InfoCircleOutlined } from "@ant-design/icons-vue";

const page = computed(() => usePage());

const showNotificationDrawer = ref(false);

const user = {
    name: page.value.props?.auth?.user?.name,
    email: page.value.props?.auth?.user?.email,
    imageUrl: `https://ui-avatars.com/api/?name=${page.value.props?.auth?.user?.name}`,
};
const navigation = computed(() => {
    return [
        {
            name: "Home",
            href: route("cashier.orders.home"),
            current: page.value.component === "Cashier/Home",
        },
        {
            name: "Cashier",
            href: route("cashier.cashier"),
            current: page.value.component === "Cashier/Cashier",
        },
    ];
});
const userNavigation = [
    { name: "Your Profile", href: "#", method: "get" },
    { name: "Settings", href: "#", method: "get" },
    { name: "Sign out", href: route("logout"), method: "post" },
];
</script>
