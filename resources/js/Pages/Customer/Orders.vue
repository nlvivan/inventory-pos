<script>
import CustomerNavLayout from "./CustomerNavLayout.vue";

export default {
    layout: CustomerNavLayout,
};
</script>

<script setup>
import { ref } from "vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { useFormatDate } from "@/Composables/useFormatDate";
import { EyeOutlined } from "@ant-design/icons-vue";
import { watchDebounced } from "@vueuse/core";
const { formatDate } = useFormatDate();

const props = defineProps({
    orders: Object,
    filters: Object,
});

const search = ref(props.filters.search);

const columns = [
    {
        title: "Order number",
        dataIndex: "order_number",
        key: "order_number",
    },
    {
        title: "Date and Time",
        dataIndex: "created_at",
        key: "created_at",
    },
    {
        title: "Item Quantity",
        dataIndex: "item_quantity",
        key: "item_quantity",
    },
    {
        title: "Total Amount",
        dataIndex: "total_amount",
        key: "total_amount",
    },
    {
        title: "Order Status",
        dataIndex: "status",
        key: "status",
    },
    {
        title: "Action",
        dataIndex: "action",
        key: "action",
        class: "w-1",
    },
];

const handleTableChange = (event) => {
    current.value = event.current;
    pageSize.value = event.pageSize;

    router.reload(
        {
            data: {
                search: search.value,
                page: event.current,
                per_page: event.pageSize,
            },
        },
        {
            replace: true,
            preserveState: true,
        }
    );
};

watchDebounced(
    search,
    () => {
        router.reload(
            {
                data: {
                    search: search.value,
                },
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    },
    { debounce: 300 }
);
</script>

<template>
    <div>
        <Head title="Order History" />

        <a-card class="w-full" title="Order History">
            <div class="w-full flex justify-end mb-4">
                <a-input-search
                    class="w-1/4"
                    v-model:value="search"
                    :allowClear="true"
                    placeholder="Search"
                />
            </div>
            <a-table
                :columns="columns"
                :data-source="props.orders.data"
                @change="handleTableChange"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'item_quantity'">
                        {{ record.order_items.length }}
                    </template>
                    <template v-if="column.dataIndex === 'total_amount'">
                        ₱ {{ record.total_amount }}
                    </template>
                    <template v-if="column.dataIndex === 'created_at'">
                        {{ formatDate(record.created_at) }}
                    </template>
                    <template v-if="column.dataIndex === 'action'">
                        <Link :href="route('customer.orders.show', record.id)">
                            <a-button type="primary" class="rounded-full">
                                <template #icon>
                                    <EyeOutlined />
                                </template>
                                View
                            </a-button>
                        </Link>
                    </template>
                </template>
            </a-table>
        </a-card>
    </div>
</template>
