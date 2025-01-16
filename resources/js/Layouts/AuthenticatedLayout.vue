<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, watch, h, onBeforeMount } from "vue";
import {
    UserOutlined,
    VideoCameraOutlined,
    UploadOutlined,
    MenuUnfoldOutlined,
    MenuFoldOutlined,
    DownOutlined,
    AppstoreOutlined,
    BellOutlined,
    CheckOutlined,
    WarningOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import {
    IconDashboard,
    IconTicket,
    IconMap,
    IconUsersGroup,
    IconCategory,
    IconCategory2,
    IconBox,
    IconArchive,
    IconStack2,
    IconSTurnRight,
    IconUsers,
    IconBarcode,
} from "@tabler/icons-vue";

const collapsed = ref(false);

const pageWatch = usePage();
// get the current url without query string
const selectedKeys = ref([window.location.href.split("?")[0]]);
const openKeys = ref([]);

const menus = [
    {
        label: "Dashboard",
        path: route("admin.dashboard"),
        icon: IconDashboard,
    },
    {
        label: "Production Batches",
        path: route("production-batches.index"),
        icon: IconBarcode,
    },
    {
        label: "Categories",
        path: route("categories.index"),
        icon: IconCategory,
    },
    {
        label: "Products",
        path: route("products.index"),
        icon: IconBox,
    },
    {
        label: "Archive Products",
        path: route("products.archive"),
        icon: IconArchive,
    },
    {
        label: "Stocks",
        path: route("stocks.index"),
        icon: IconStack2,
    },
    {
        label: "Product Returns",
        path: route("product-returns.index"),
        icon: IconSTurnRight,
    },
    {
        label: "Users",
        path: route("users.index"),
        icon: IconUsers,
    },
    {
        label: "Schedules",
        path: route("schedules.index"),
        icon: IconUsers,
    },
];

const mapLinkItem = (link) => {
    return {
        key: link.path || link.key,
        path: link.path,
        label: link.label,
        children: link.children
            ? link.children.map((child) => mapLinkItem(child))
            : null,
        icon: link.icon
            ? h("span", null, [
                  h(link.icon, {
                      class: "flex-shrink-0 text-base w-5 h-5",
                  }),
              ])
            : null,
        rightCounter: link.rightCounter,
    };
};

onBeforeMount(() => {
    const url = new URL(selectedKeys.value);
    selectedKeys.value = [url.href];

    menus.find((l) => {
        if (l.children) {
            l.children.find((c) => {
                if (c.path === url.href) {
                    openKeys.value = [l.key];
                    return true;
                }
            });
        }
    });
});

watch(pageWatch, () => {
    const current = window.location.href.split("?")[0];
    if (selectedKeys.value[0] !== current) {
        selectedKeys.value = [current];
    }
});

const handleLogout = () => {
    router.post(route("logout"));
};

const showNotificationDrawer = ref(false);

const handleNotification = () => {
    showNotificationDrawer.value = true;
};

const handleMarkAsRead = (id) => {
    router.post(route("notifications.markAsRead", id));
};
</script>
<template>
    <a-config-provider
        :theme="{
            token: {
                colorPrimary: 'rgb(245 158 11)',
            },
        }"
    >
        <a-layout class="min-h-screen font-sans font-normal">
            <a-layout-sider
                :style="{
                    overflow: 'auto',
                    position: 'sticky',
                    left: 0,
                    top: 0,
                    bottom: 0,
                }"
                width="250"
                v-model:collapsed="collapsed"
                :trigger="null"
                collapsible
            >
                <Link :href="route('dashboard')">
                    <div class="logo flex items-center justify-center gap-2">
                        <img src="/assets/IMG_5472 (1).png" class="w-8" />
                        <img
                            src="/assets/IMG_5472.png"
                            class="w-36 mt-2"
                            v-if="!collapsed"
                        />
                    </div>
                </Link>
                <a-menu
                    :selectedKeys="selectedKeys"
                    v-model:openKeys="openKeys"
                    theme="dark"
                    mode="inline"
                >
                    <template
                        v-for="link in menus.map((l) => mapLinkItem(l))"
                        :key="link.key"
                    >
                        <a-menu-item
                            :icon="link.icon"
                            v-if="!link.children"
                            :key="link.key"
                            class="flex items-center"
                        >
                            <Link
                                :href="link.key"
                                class="flex items-center justify-between"
                            >
                                <span class="font-medium">{{
                                    link.label
                                }}</span>
                                <span
                                    v-if="link.rightCounter"
                                    class="font-bold px-2.5 py-1 text-xs rounded-full bg-white/20"
                                >
                                    {{ link.rightCounter }}
                                </span>
                            </Link>
                        </a-menu-item>
                        <a-sub-menu
                            v-if="link.children"
                            :icon="link.icon"
                            :key="link.key"
                        >
                            <template #title>{{ link.label }}</template>
                            <a-menu-item
                                :icon="child.icon"
                                v-for="child in link.children"
                                :key="child.key"
                            >
                                <Link :href="child.key">
                                    {{ child.label }}
                                </Link>
                            </a-menu-item>
                        </a-sub-menu>
                    </template>
                </a-menu>
            </a-layout-sider>
            <a-layout>
                <a-layout-header style="background: #fff; padding: 0">
                    <div class="flex justify-between px-8">
                        <div class="flex justify-around items-center">
                            <div>
                                <menu-unfold-outlined
                                    v-if="collapsed"
                                    class="trigger"
                                    @click="() => (collapsed = !collapsed)"
                                />
                                <menu-fold-outlined
                                    v-else
                                    class="trigger"
                                    @click="() => (collapsed = !collapsed)"
                                />
                            </div>

                            <div class="font-semibold ml-4 hidden md:block">
                                {{ $page.props.system_identity }}
                            </div>
                        </div>
                        <div class="flex py-2 justify-center items-center">
                            <img src="/assets/IMG_5472 (1).png" class="w-8" />
                            <img
                                src="/assets/IMG_5472.png"
                                class="w-36 mt-2"
                                v-if="!collapsed"
                            />
                            <span
                                class="text-2xl font-bold mt-1.5 text-blue-950"
                            >
                                (Sales And Inventory Management)</span
                            >
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <a-badge
                                :count="$page.props.auth.notificationsCount"
                            >
                                <a-button
                                    shape="circle"
                                    @click="handleNotification"
                                >
                                    <template #icon>
                                        <BellOutlined />
                                    </template>
                                </a-button>
                            </a-badge>

                            <a-dropdown
                                :trigger="['click']"
                                class="flex items-center float-right"
                                placement="bottomRight"
                            >
                                <div>
                                    <button
                                        type="button"
                                        class="flex items-center rounded-full bg-white text-sm focus:outline-none"
                                    >
                                        <span
                                            class="mr-3 font-medium hidden md:block"
                                        >
                                            {{ $page.props.auth.user.name }}
                                        </span>

                                        <img
                                            class="h-10 w-10 rounded-full"
                                            :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}`"
                                            alt=""
                                        />
                                    </button>
                                </div>
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item
                                            key="3"
                                            class="border-b border-neutral-200"
                                        >
                                            <div class="font-semibold">
                                                {{ $page.props.auth.user.name }}
                                            </div>
                                            <div class="text-gray-500 text-sm">
                                                {{
                                                    $page.props.auth.user.email
                                                }}
                                            </div>
                                        </a-menu-item>
                                        <a-menu-item key="1" @click="logout">
                                            <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                type="button"
                                            >
                                                Logout
                                            </Link>
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </div>
                    </div>
                </a-layout-header>
                <a-layout-content>
                    <div class="max-w-7xl mx-auto p-4 py-4">
                        <slot />
                    </div>
                </a-layout-content>
                <!-- drawer -->
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
                                        Batch Number:
                                        {{ notification.data.batch_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Expiry Date:
                                        {{ notification.data.expiry_date }}
                                    </div>
                                    <div
                                        class="text-sm text-amber-500 cursor-pointer"
                                        @click="
                                            handleMarkAsRead(notification.id)
                                        "
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
                                        Batch Number:
                                        {{ notification.data.batch_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Remaining Stock:
                                        {{ notification.data.remaining_stock }}
                                    </div>
                                    <div
                                        class="text-sm text-amber-500 cursor-pointer"
                                        @click="
                                            handleMarkAsRead(notification.id)
                                        "
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
                                    <div class="font-medium text-gray-500">
                                        {{ notification.data.customer_name }}
                                        has new order
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Email:
                                        {{ notification.data.customer_email }}
                                    </div>
                                    <div
                                        class="text-sm text-amber-500 cursor-pointer"
                                        @click="
                                            handleMarkAsRead(notification.id)
                                        "
                                    >
                                        Mark as Read
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a-drawer>
            </a-layout>
        </a-layout>
    </a-config-provider>
</template>

<style>
.trigger {
    font-size: 18px;
    line-height: 64px;
    padding: 0 24px;
    cursor: pointer;
    transition: color 0.3s;
}

.trigger:hover {
    color: #1890ff;
}

.logo {
    margin-top: 20px;
    padding: 10px;
}

.site-layout .site-layout-background {
    background: #fff;
}
</style>
