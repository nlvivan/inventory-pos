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
} from "@ant-design/icons-vue";
import {
    IconDashboard,
    IconTicket,
    IconMap,
    IconUsersGroup,
    IconCategory,
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
        icon: IconDashboard,
    },
    {
        label: "Categories",
        path: route("categories.index"),
        icon: IconCategory,
    },
    {
        label: "Products",
        path: route("products.index"),
        icon: IconCategory,
    },
    {
        label: "Product Returns",
        path: route("product-returns.index"),
        icon: IconCategory,
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
                        <img
                            src="/storage/assets/IMG_5472 (1).png"
                            class="w-8"
                        />
                        <img
                            src="/storage/assets/IMG_5472.png"
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
                        <div>
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
                    <div class="max-w-7xl mx-auto p-5 py-10">
                        <slot />
                    </div>
                </a-layout-content>
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
