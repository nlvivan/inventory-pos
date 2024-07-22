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
    record: Object,
    filters: Object,
});

console.log(props.record);
</script>
<template>
    <CashierLayout>
        <Head title="Invoice" />
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

            <div class="flex w-full justify-end">
                <button
                    class="px-4 py-2 rounded-lg font-semibold text-amber-500"
                    style="border: 2px solid #f59e0b"
                >
                    Print as PDF
                </button>
            </div>
            <div
                class="mt-4 rounded-xl w-full p-4"
                style="border: 2px solid #e4e4e4"
            >
                <div class="flex justify-between items-baseline">
                    <p class="text-2xl text-[#1C486F] font-bold">
                        Order Invoice
                    </p>
                </div>
                <div>
                    <table class="w-full">
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Seller Name: 3 Lito's Store
                            </td>
                            <td class="text-[#1C486F] text-sm p-2">
                                Order Number: {{ props.record.order_number }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Seller Address: Carmen Davao del Norte
                            </td>
                            <td class="text-[#1C486F] text-sm p-2">
                                Order Issued:
                                {{ formatDate(props.record.created_at) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2"></td>
                            <td class="text-[#1C486F] text-sm p-2">
                                Order Pick-up Date and Time:
                                <span class="text-[#1C486F] text-sm">{{
                                    record?.pick_up_date_time
                                        ? record?.pick_up_date_time
                                        : "N/A"
                                }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Customer Name:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.name
                                        ? record.customer?.name
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm p-2">
                                Order Payment Method: On-site
                            </td>
                        </tr>

                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Customer Address:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.address
                                        ? record.customer.address
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm p-2"></td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Customer Email:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.email
                                        ? record.customer.email
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm p-2"></td>
                        </tr>
                        <tr>
                            <td class="text-[#1C486F] text-sm p-2">
                                Customer Phone:
                                <span class="text-[#1C486F] text-sm">{{
                                    record.customer?.phone
                                        ? record.customer.phone
                                        : "N/A"
                                }}</span>
                            </td>
                            <td class="text-[#1C486F] text-sm p-2"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div
                class="mt-4 rounded-xl w-full p-4"
                style="border: 2px solid #e4e4e4"
            >
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
                            v-for="(item, index) in props.record?.order_items"
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
                                <p class="text-sky-800 text-2xl font-semibold">
                                    Grand Total:
                                </p>
                            </td>
                            <td class="p-4 text-sky-800 text-2xl font-semibold">
                                <p class="text-sky-800 text-2xl font-semibold">
                                    ₱ {{ props.record?.total_amount }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CashierLayout>
</template>
