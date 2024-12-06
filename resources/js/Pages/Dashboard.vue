<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import SalesIcon from "@/Components/SalesIcon.vue";
import { ref, computed } from "vue";

import { IconReportMoney } from "@tabler/icons-vue";
import OrderData from "@/Components/OrderData.vue";
import dayjs from "dayjs";
import { useFormatDate } from "@/Composables/useFormatDate";
import { watchDebounced } from "@vueuse/core";

const {
    dayRangeDate,
    weekRangeDate,
    monthRangeDate,
    getUserTimezone,
    formatGraphDates,
    formatCategoryDates,
    formatDateRange,
} = useFormatDate();

const props = defineProps({
    totalSales: "",
    topSales: "",
    filters: Object,
    nearlyExpiredProducts: "",
    productNearlyOutOfStock: "",
    filters: Object,
});

const dateRange = ref(
    props.filters?.from
        ? [dayjs(props.filters?.from), dayjs(props.filters?.to)]
        : [
              dayjs(dayjs().startOf("month").format("YYYY/MM/DD")),
              dayjs(dayjs().format("YYYY/MM/DD")),
          ]
);

const nearlyExpiredProductsColumns = [
    {
        title: "Product Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Batch Number",
        dataIndex: ["production_batch", "batch_number"],
        key: "expiry_date",
    },
    {
        title: "Expiry Date",
        dataIndex: ["production_batch", "expiration_date"],
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
        title: "Batch Number",
        dataIndex: ["product", "production_batch", "batch_number"],
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
        title: "Products",
        dataIndex: ["product", "name"],
        key: "product",
    },
    {
        title: "Sold Products",
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

const search = ref();

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

watchDebounced(search, handleChangeFilter, { debounce: 300 });

function handleChangeFilter() {
    router.reload(
        {
            data: {
                from:
                    dateRange.value !== null && dateRange.value.length
                        ? formatDateRange(dateRange.value[0])
                        : formatDateRange(
                              dayjs().startOf("month").format("YYYY/MM/DD")
                          ),
                to:
                    dateRange.value !== null && dateRange.value.length
                        ? formatDateRange(dateRange.value[1])
                        : formatDateRange(dayjs().format("YYYY/MM/DD")),
            },
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}

const formattedDateRange = computed(() => {
    const from =
        dateRange.value !== null && dateRange.value.length
            ? formatDateRange(dateRange.value[0])
            : formatDateRange(dayjs().startOf("month").format("YYYY/MM/DD"));
    const to =
        dateRange.value !== null && dateRange.value.length
            ? formatDateRange(dateRange.value[1])
            : formatDateRange(dayjs().format("YYYY/MM/DD"));
    return { from, to };
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />
        <div class="">
            <div class="">
                <div class="mt-2 bg-white p-1.5" :title="false">
                    <div class="flex justify-between items-center mb-2">
                        <!-- Date Range Picker centered -->
                        <div class="flex-grow flex justify-center">
                            <a-range-picker
                                v-model:value="dateRange"
                                @change="handleChangeFilter"
                            />
                        </div>

                        <!-- Print PDF button aligned to the right -->
                        <div class="flex-shrink-0">
                            <a
                                :href="
                                    route('admin.dashboard.print-pdf', {
                                        from: formattedDateRange.from,
                                        to: formattedDateRange.to,
                                    })
                                "
                                target="_blank"
                            >
                                <a-button danger> Print as PDF </a-button>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a-card title="Total Sales" class="mt-2">
                            <div
                                class="flex justify-center items-center text-center w-full h-40"
                            >
                                <h1 class="text-2xl font-semibold">
                                    ₱ {{ props.totalSales }}
                                </h1>
                            </div>
                        </a-card>

                        <a-card title="Product Top Sales" class="mt-2">
                            <a-table
                                :columns="columns"
                                :data-source="props.topSales.data"
                                :pagination="false"
                                size="small"
                            >
                                <template #bodyCell="{ column, record }">
                                    <template
                                        v-if="column.dataIndex === 'image'"
                                    >
                                        <img
                                            class="h-16 w-16 rounded-full"
                                            :src="
                                                record.product.image_url ??
                                                '/IMG_4359.jpg'
                                            "
                                        />
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex === 'total_price'
                                        "
                                    >
                                        ₱{{ record.total_price }}
                                    </template>
                                </template>
                            </a-table>
                        </a-card>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-2">
                <div>
                    <a-card title="Nearly Expired Products">
                        <a-table
                            :pagination="false"
                            :columns="nearlyExpiredProductsColumns"
                            :data-source="props.nearlyExpiredProducts"
                            :scroll="{ y: 1500 }"
                            size="small"
                        ></a-table>
                    </a-card>
                </div>
                <div>
                    <a-card title="Nearly Out of Stock Products">
                        <a-table
                            :pagination="false"
                            :columns="productNearlyOutOfStockColumns"
                            :data-source="props.productNearlyOutOfStock"
                            :scroll="{ y: 1500 }"
                            size="small"
                        ></a-table>
                    </a-card>
                </div>
            </div>
            <OrderData class="mt-6" />
        </div>
    </AuthenticatedLayout>
</template>
