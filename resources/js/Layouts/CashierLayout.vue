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
                                        src="/storage/assets/IMG_5472 (1).png"
                                        class="w-8"
                                    />
                                    <img
                                        src="/storage/assets/IMG_5472.png"
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
                                <button
                                    type="button"
                                    class="relative rounded-full bg-white p-1 text-[#1C486F] hover:text-[#1C486F] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                >
                                    <span class="absolute -inset-1.5" />
                                    <span class="sr-only"
                                        >View notifications</span
                                    >
                                    <BellIcon
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    />
                                </button>

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
                                                <a
                                                    :href="item.href"
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100'
                                                            : '',
                                                        'block px-4 py-2 text-sm text-gray-700',
                                                    ]"
                                                    >{{ item.name }}</a
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
        </div>
    </a-config-provider>
</template>

<script setup>
import { computed } from "vue";
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

const page = computed(() => usePage());

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
    { name: "Your Profile", href: "#" },
    { name: "Settings", href: "#" },
    { name: "Sign out", href: "#" },
];
</script>
