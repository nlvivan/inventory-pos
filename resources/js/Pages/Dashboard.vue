<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SalesIcon from "@/Components/SalesIcon.vue";
import { ref } from "vue";

import { IconReportMoney } from "@tabler/icons-vue";
import OrderData from "@/Components/OrderData.vue";

const props = defineProps({
    totalSalesThisDay: "",
    totalSalesThisMonth: "",
    totalSalesThisWeek: "",
    totalSalesThisYear: "",
    topSales: "",
    filters: Object,
});

const columns = [
    {
        title: "Image",
        dataIndex: "image",
        key: "image",
    },
    {
        title: "Product",
        dataIndex: ["product", "name"],
        key: "product",
    },
    {
        title: "Quantity",
        dataIndex: "total_quantity",
        key: "quantity",
    },
    {
        title: "Total Price",
        dataIndex: "total_price",
        key: "total_price",
    },
];

const selectedFilter = ref(props.filters.topSalesFilter ?? "this_month");

const options = [
    {
        label: "This Month",
        value: "this_month",
    },
    {
        label: "This Week",
        value: "this_week",
    },

    {
        label: "Today",
        value: "today",
    },
];

function filterTopSaleshandler() {
    router.reload(
        {
            data: {
                top_sales_filter: selectedFilter.value || undefined,
            },
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <p class="font-bold text-2xl">Dashboard</p>
                <div class="w-full grid grid-cols-4 gap-6">
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisDay }}
                                </span>
                                <br />
                                <span class="text-sm">Total Sales Today</span>
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisWeek }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Week</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisMonth }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Month</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisYear }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Year</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                </div>
                <a-card class="mt-6">
                    <template #title>
                        <span class="font-bold">Top Sales</span>

                        <a-select
                            ref="select"
                            class="text-xs float-right font-bold"
                            name="reseller"
                            style="width: 140px"
                            :options="options"
                            v-model:value="selectedFilter"
                            @change="filterTopSaleshandler"
                        >
                        </a-select>
                    </template>
                    <a-table
                        :columns="columns"
                        :data-source="props.topSales.data"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'image'">
                                <img
                                    class="h-16 w-16 rounded-full"
                                    :src="
                                        record.product.image_url ??
                                        '/storage/IMG_4359.jpg'
                                    "
                                />
                            </template>
                            <template v-if="column.dataIndex === 'total_price'">
                                ₱ {{ record.total_price }}
                            </template>
                        </template>
                    </a-table>
                </a-card>

                <OrderData class="mt-6" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
