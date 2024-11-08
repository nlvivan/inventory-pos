<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SalesIcon from "@/Components/SalesIcon.vue";
import { ref } from "vue";

import { IconReportMoney } from "@tabler/icons-vue";
import OrderData from "@/Components/OrderData.vue";

const props = defineProps({
    totalSales: "",
    topSales: "",
    filters: Object,
    nearlyExpiredProducts: "",
    productNearlyOutOfStock: "",
});

const nearlyExpiredProductsColumns = [
    {
        title: "Product Name",
        dataIndex: "name",
        key: "name",
    },

    {
        title: "Expiry Date",
        dataIndex: "expiry_date",
        key: "expiry_date",
    },
];

const productNearlyOutOfStockColumns = [
    {
        title: "Product Name",
        dataIndex: ["product", "name"],
        key: "name",
    },

    {
        title: "Stock",
        dataIndex: "stock",
        key: "stock",
    },
];

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

const selectedFilter = ref(props.filters.top_sales_filter ?? "this_month");
const selectedSalesFilter = ref(props.filters.sales_filter ?? "today");

const options = [
    {
        label: "Today",
        value: "today",
    },
    {
        label: "This Week",
        value: "this_week",
    },

    {
        label: "This Month",
        value: "this_month",
    },

    {
        label: "This Year",
        value: "this_year",
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
                <a-card>
                    <template #title>
                        <span class="font-bold">Sales</span>

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
                    <div class="grid grid-cols-2 gap-2">
                        <a-card title="Total Sales" class="mt-6">
                            <h1 class="text-8xl font-semibold text-center">
                                ₱ {{ props.totalSales }}
                            </h1>
                        </a-card>
                        <a-card title="Product Top Sales" class="mt-6">
                            <a-table
                                :columns="columns"
                                :data-source="props.topSales.data"
                                :pagination="false"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template
                                        v-if="column.dataIndex === 'image'"
                                    >
                                        <img
                                            class="h-16 w-16 rounded-full"
                                            :src="
                                                record.product.image_url ??
                                                '/storage/IMG_4359.jpg'
                                            "
                                        />
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex === 'total_price'
                                        "
                                    >
                                        ₱ {{ record.total_price }}
                                    </template>
                                </template>
                            </a-table>
                        </a-card>
                    </div>
                </a-card>

                <div class="grid grid-cols-2 gap-2 mt-6">
                    <div>
                        <a-card title="Nearly Expired Products">
                            <a-table
                                :pagination="false"
                                :columns="nearlyExpiredProductsColumns"
                                :data-source="props.nearlyExpiredProducts"
                            ></a-table>
                        </a-card>
                    </div>
                    <div>
                        <a-card title="Nearly Out of Stock Products">
                            <a-table
                                :pagination="false"
                                :columns="productNearlyOutOfStockColumns"
                                :data-source="props.productNearlyOutOfStock"
                            ></a-table>
                        </a-card>
                    </div>
                </div>

                <OrderData class="mt-6" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
