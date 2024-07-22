<script setup>
import CashierLayout from "@/Layouts/CashierLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";
import { watchDebounced } from "@vueuse/core";
import { DeleteOutlined, EyeOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import { useFormatDate } from "@/Composables/useFormatDate";
const { formatDate } = useFormatDate();

const props = defineProps({
    records: Array,
    filters: Object,
});

console.log(props.records);

const columns = [
    {
        title: "Order Number",
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
        dataIndex: "quantity",
        key: "quantity",
    },
    {
        title: "Total Amount",
        dataIndex: "total_amount",
        key: "total_amount",
    },
    {
        title: "Order Status",
        dataIndex: "order_status",
        key: "order_status",
    },
    {
        title: "Action",
        dataIndex: "action",
        key: "action",
    },
];

const search = ref(props?.filters?.search);
const current = ref(props.records.current_page);
const pageSize = ref(props.records.per_page);

const pagination = computed(() => ({
    total: props.records.total,
    current: props.records?.current_page,
    pageSize: props.records?.per_page,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: true,
}));

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
</script>
<template>
    <CashierLayout>
        <Head title="Home" />
        <div class="py-4">
            <div class="flex justify-between items-baseline">
                <p class="text-5xl text-[#1C486F] font-bold">
                    Hello
                    <span class="text-5xl text-amber-500 font-bold">{{
                        $page.props.auth.user?.name
                    }}</span>
                </p>
                <p>{{ new Date().toDateString() }}</p>
            </div>

            <div
                class="mt-4 rounded-xl w-full p-4"
                style="border: 2px solid #e4e4e4"
            >
                <p class="text-2xl text-[#1C486F] font-bold">Orders</p>
                <a-table
                    :columns="columns"
                    :data-source="props.records.data"
                    @change="handleTableChange"
                    :pagination="{ ...pagination }"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'quantity'">
                            {{ record.order_items.length }}
                        </template>
                        <template v-if="column.dataIndex === 'total_amount'">
                            ₱ {{ record.total_amount }}
                        </template>
                        <template v-if="column.dataIndex === 'created_at'">
                            {{ formatDate(record.created_at) }}
                        </template>
                        <template v-if="column.dataIndex === 'order_status'">
                            <a-tag
                                color="orange"
                                v-if="record.status === 'pending'"
                                >{{ record.status }}</a-tag
                            >
                            <a-tag
                                color="green"
                                v-if="record.status === 'paid'"
                                >{{ record.status }}</a-tag
                            >
                            <a-tag
                                color="red"
                                v-if="record.status === 'failed'"
                                >{{ record.status }}</a-tag
                            >
                        </template>
                        <template v-if="column.dataIndex === 'action'">
                            <Link
                                :href="route('cashier.orders.show', record.id)"
                            >
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
            </div>
        </div>
    </CashierLayout>
</template>
