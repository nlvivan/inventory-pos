<script setup>
import CashierLayout from "@/Layouts/CashierLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";
import { watchDebounced } from "@vueuse/core";
import { DeleteOutlined, EyeOutlined } from "@ant-design/icons-vue";
import { message, notification } from "ant-design-vue";
import { useFormatDate } from "@/Composables/useFormatDate";
const { formatDate } = useFormatDate();

const props = defineProps({
    record: Object,
    filters: Object,
    errors: Object,
});

const form = useForm({
    cash: "",
});

const openPayOrderModal = ref(false);

const changeAmount = computed(() => {
    return form.cash ? form.cash - totalAmount.value : "";
});

const totalAmount = computed(() => {
    return props.record.order_items.reduce(
        (sum, item) => sum + item.total_price,
        0
    );
});

const payOrder = () => {
    form.post(route("cashier.orders.pay", props.record.id), {
        onSuccess: () => {
            notification.success({
                title: "Order Paid Successfully",
                message: "Order has been paid successfully",
            });
            form.reset();
            form.errors = {};
            router.get(route("cashier.orders.invoice", props.record.id));
        },
    });
};

const handleCancelPayment = () => {
    form.reset();
    form.errors = {};
    openPayOrderModal.value(false);
};
</script>
<template>
    <CashierLayout>
        <Head title="Order" />
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
                <div class="flex justify-between items-baseline">
                    <p class="text-2xl text-[#1C486F] font-bold">
                        Order Details - {{ props.record.order_number }}
                    </p>
                    <div class="flex gap-2">
                        <button
                            @click="openPayOrderModal = true"
                            v-if="props.record.status !== 'paid'"
                            class="px-4 py-2 rounded-lg font-semibold text-green-500"
                            style="border: 2px solid #31d038"
                        >
                            Pay Order
                        </button>
                        <Link
                            :href="
                                route('cashier.orders.invoice', props.record.id)
                            "
                        >
                            <button
                                class="px-4 py-2 rounded-lg font-semibold text-amber-500"
                                style="border: 2px solid #f59e0b"
                            >
                                View Order Invoice
                            </button>
                        </Link>
                    </div>
                </div>
                <div>
                    <table class="w-full">
                        <tr>
                            <td class="text-[#1C486F] text-sm">
                                Customer Name:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.name
                                        ? record.customer?.name
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm">
                                Order Number:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.order_number
                                }}</span>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-[#1C486F] text-sm">
                                Customer Address:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.address
                                        ? record.customer.address
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm">
                                Order Date Issued:
                                <span class="text-[#1C486F] text-sm">{{
                                    formatDate(record.created_at)
                                }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm">
                                Customer Email:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.email
                                        ? record.customer.email
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm">
                                Order Pick-up Date and Time:
                                <span class="text-[#1C486F] text-sm">{{
                                    record?.pick_up_date_time
                                        ? record?.pick_up_date_time
                                        : "N/A"
                                }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm">
                                Customer Phone:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.phone
                                        ? record.customer.phone
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm">
                                Order Payment Method:
                                <span class="text-[#1C486F] text-sm">{{
                                    record?.payment_method ?? "On Site"
                                }}</span>
                            </td>
                        </tr>
                    </table>
                    <table class="mt-8 w-full text-left p-4">
                        <thead class="bg-[#FFF8EB]">
                            <th class="p-4 text-sky-800">No.</th>
                            <th class="p-4 text-sky-800">Item</th>
                            <th class="p-4 text-sky-800">Price</th>
                            <th class="p-4 text-sky-800">Quantity</th>
                            <th class="p-4 text-sky-800">Sub-Total</th>
                        </thead>
                        <tbody class="text-left">
                            <tr
                                v-for="(item, index) in props.record
                                    ?.order_items"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td class="p-4 text-sky-800">
                                    {{ item?.product?.name }}
                                </td>
                                <td class="p-4 text-sky-800">
                                    ₱ {{ item?.product?.price }}
                                </td>
                                <td class="p-4 text-sky-800">
                                    {{ item?.quantity }}
                                </td>
                                <td class="p-4 text-sky-800">
                                    ₱ {{ item?.total_price }}
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="p-4">
                                    <p
                                        class="text-sky-800 text-2xl font-semibold"
                                    >
                                        Grand Total:
                                    </p>
                                </td>
                                <td
                                    class="p-4 text-sky-800 text-2xl font-semibold"
                                >
                                    <p
                                        class="text-sky-800 text-2xl font-semibold"
                                    >
                                        ₱ {{ props.record?.total_amount }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pay order Modal -->
        <a-modal
            v-model:open="openPayOrderModal"
            title="Pay Order"
            :footer="false"
        >
            <a-form layout="vertical" @submit="payOrder">
                <a-form-item
                    label="Cash"
                    :validate-status="props.errors.cash ? 'error' : null"
                    :help="props.errors.cash"
                >
                    <a-input-number v-model:value="form.cash" class="w-full" />
                </a-form-item>
                <a-form-item label="Change">
                    <a-input-number
                        v-model:value="changeAmount"
                        class="w-full"
                    />
                </a-form-item>
                <a-form-item>
                    <div class="w-full flex justify-end gap-2">
                        <a-button type="default" @click="handleCancelPayment">
                            Cancel
                        </a-button>
                        <a-button type="primary" @click="payOrder">
                            Pay Order
                        </a-button>
                    </div>
                </a-form-item>
            </a-form>
        </a-modal>
    </CashierLayout>
</template>
