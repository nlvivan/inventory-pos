<script setup>
import { computed, ref } from "vue";
import CustomerLayout from "@/Layouts/CutomerLayout.vue";
import { Carousel, Navigation, Slide } from "vue3-carousel";
import {
    AppstoreOutlined,
    FireOutlined,
    PercentageOutlined,
    NotificationOutlined,
    HomeOutlined,
    ShoppingCartOutlined,
    UserOutlined,
    CloseOutlined,
} from "@ant-design/icons-vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { notification, message } from "ant-design-vue";

const props = defineProps({
    records: Array,
});

const selectedSchedule = ref("");

const totalPrice = computed(() => {
    return props.records.data.reduce((acc, curr) => {
        return acc + curr.product.price * curr.quantity;
    }, 0);
});

const form = useForm({
    records: props.records.data,
});

const placeOrder = () => {
    router.post(
        route("checkout.placeOrder"),
        {
            selectedSchedule: selectedSchedule.value,
        },
        {
            onError: (err) => {
                message.error(err.selectedSchedule);
            },
        }
    );
};
</script>

<template>
    <CustomerLayout>
        <Head title="Carts" />

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4 mt-12">
            <div class="w-full grid grid-cols-3 gap-2">
                <div class="col-span-2">
                    <div
                        style="
                            border: 1px solid #ccc;
                            border-radius: 5px;
                            padding: 10px;
                        "
                    >
                        <p class="text-2xl font-semibold">Pick-up Schedule</p>
                        <hr style="border: 1px solid #ccc" />
                        <p class="text-lg font-semibold text-amber-500">
                            Morning Schedule
                        </p>
                        <div class="grid grid-cols-4 gap-2">
                            <div
                                @click="selectedSchedule = '8AM-10AM'"
                                class="rounded-lg border p-4 text-center cursor-pointer"
                                :class="
                                    selectedSchedule === '8AM-10AM'
                                        ? 'text-amber-500'
                                        : 'text-gray-600'
                                "
                                :style="
                                    selectedSchedule === '8AM-10AM'
                                        ? 'border: 1px solid #F59E0B'
                                        : 'border: 1px solid #ccc'
                                "
                            >
                                8:00 AM - 10:00 AM
                            </div>
                            <div
                                @click="selectedSchedule = '10AM-12PM'"
                                class="rounded-lg border p-4 text-center cursor-pointer"
                                :class="
                                    selectedSchedule === '10AM-12PM'
                                        ? 'text-amber-500'
                                        : 'text-gray-600'
                                "
                                :style="
                                    selectedSchedule === '10AM-12PM'
                                        ? 'border: 1px solid #F59E0B'
                                        : 'border: 1px solid #ccc'
                                "
                            >
                                10:00 AM - 12:00 PM
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-lg font-semibold text-amber-500">
                                Afternoon Schedule
                            </p>
                            <div class="grid grid-cols-4 gap-2">
                                <div
                                    @click="selectedSchedule = '12PM-1PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '12PM-1PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '12PM-1PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    12:00 PM - 1:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '1PM-2PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '1PM-2PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '1PM-2PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    1:00 PM - 2:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '2PM-3PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '2PM-3PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '2PM-3PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    2:00 PM - 3:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '3PM-4PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '3PM-4PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '3PM-4PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    3:00 PM - 4:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '4PM-5PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '4PM-5PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '4PM-5PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    4:00 PM - 5:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '5PM-6PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '5PM-6PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '5PM-6PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    5:00 PM - 6:00 PM
                                </div>
                                <div
                                    @click="selectedSchedule = '6PM-7PM'"
                                    class="rounded-lg border p-4 text-center cursor-pointer"
                                    :class="
                                        selectedSchedule === '6PM-7PM'
                                            ? 'text-amber-500'
                                            : 'text-gray-600'
                                    "
                                    :style="
                                        selectedSchedule === '6PM-7PM'
                                            ? 'border: 1px solid #F59E0B'
                                            : 'border: 1px solid #ccc'
                                    "
                                >
                                    6:00 PM - 7:00 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    style="
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        padding: 10px;
                    "
                >
                    <p class="text-2xl">Order Summary</p>

                    <div v-for="item in props.records.data" :key="item.id">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <img
                                        class="w-10 h-10"
                                        :src="
                                            item.product.image_url ??
                                            '/storage/IMG_4359.jpg'
                                        "
                                        alt=""
                                    />
                                </div>
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ item.product.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ item.product.description }}
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-400">
                                {{ item.quantity }} x
                                <span class="text-amber-500"
                                    >₱{{ item.product.price }}</span
                                >
                            </span>
                        </div>
                    </div>
                    <hr />
                    <table class="w-full">
                        <tr>
                            <td class="text-left text-gray-500 p-2">
                                Total Price
                            </td>
                            <td
                                class="text-left text-gray-500 p-2"
                                text-gray-400
                            >
                                ₱ {{ totalPrice }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left text-gray-500 p-2">
                                Discount
                            </td>
                            <td class="text-left text-gray-500 p-2">₱ 0</td>
                        </tr>
                        <tr>
                            <td class="text-left text-gray-500 p-2">Tax</td>
                            <td class="text-left text-gray-500 p-2">₱ 0</td>
                        </tr>
                    </table>
                    <hr class="w-full text-gray-500" />
                    <table class="w-full mb-12">
                        <tr>
                            <th class="text-left text-gray-500 p-2 text-lg">
                                Subtotal
                            </th>
                            <th class="text-left text-gray-500 p-2 text-lg">
                                ₱ {{ totalPrice }}
                            </th>
                        </tr>
                    </table>

                    <a-button
                        type="primary"
                        block
                        size="large"
                        @click="placeOrder"
                        >Place Order</a-button
                    >
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
