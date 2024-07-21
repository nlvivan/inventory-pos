<script setup>
import CashierLayout from "@/Layouts/CashierLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";
import { watchDebounced } from "@vueuse/core";
import { DeleteOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";

const props = defineProps({
    records: Array,
    filters: Object,
});

const openQuantityModal = ref(false);
const openCashModal = ref(false);
const cash = ref("");
const change = ref("");
const quantity = ref("");
const addedProduct = ref({});

const orderItems = reactive([]);

const addItem = (product) => {
    addedProduct.value = product;
    openQuantityModal.value = true;
};

const handleOk = () => {
    const existingProductIndex = orderItems.findIndex(
        (item) => item.id === addedProduct.value.id
    );

    if (existingProductIndex != -1) {
        orderItems[existingProductIndex].quantity += quantity.value;
        orderItems[existingProductIndex].subTotal =
            orderItems[existingProductIndex].quantity *
            addedProduct.value.price;
    } else {
        addedProduct.value.quantity = quantity.value;
        addedProduct.value.subTotal =
            addedProduct.value.quantity * addedProduct.value.price;

        orderItems.push(addedProduct.value);
    }

    addedProduct.value = null;
    quantity.value = "";
    openQuantityModal.value = false;
};

const totalAmount = computed(() => {
    return orderItems.reduce((sum, item) => sum + item.subTotal, 0);
});

const changeAmount = computed(() => {
    return cash.value ? cash.value - totalAmount.value : "";
});

const removeItem = (orderId) => {
    orderItems.splice(
        orderItems.findIndex(function (item) {
            return item.id === orderId;
        }),
        1
    );
};

const search = ref(props?.filters?.search);

const searchItem = () => {
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
};

const submitOrders = () => {
    router.post(
        route("cashier.orders.store"),
        {
            orderItems: orderItems,
            cash: cash.value,
        },
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                message.success("Order has been successfully submitted");
            },
        }
    );
};
</script>
<template>
    <CashierLayout>
        <Head title="Cashier" />
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

            <div class="w-full h-full grid grid-cols-3 gap-4">
                <div
                    class="px-6 py-4 col-span-2 rounded-lg h-screen"
                    style="border: 2px solid #e4e4e4"
                >
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm">Item/s List</span>
                        <span class="text-sm"
                            >{{ orderItems.length }} Items Added</span
                        >
                    </div>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="w-full bg-[#FFF8EB]">
                                <th class="py-2 px-4 text-left">Item</th>
                                <th class="py-2 px-4 text-left">Price</th>
                                <th class="py-2 px-4 text-left">Quantity</th>
                                <th class="py-2 px-4 text-left">Sub-Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="orderItem in orderItems"
                                :key="orderItem.id"
                            >
                                <td class="py-2 px-4 text-left">
                                    {{ orderItem.name }}
                                </td>
                                <td class="py-2 px-4 text-left">
                                    ₱ {{ orderItem.price }}
                                </td>
                                <td class="py-2 px-4 text-left">
                                    {{ orderItem.quantity }}
                                </td>
                                <td class="py-2 px-4 text-left">
                                    <div class="flex gap-2 items-baseline">
                                        <p>₱ {{ orderItem.subTotal }}</p>
                                        <a-button
                                            type="danger"
                                            class="text-red-500"
                                            @click="removeItem(orderItem.id)"
                                            shape="circle"
                                        >
                                            <template #icon>
                                                <DeleteOutlined />
                                            </template>
                                        </a-button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Add your items here -->
                        </tbody>
                    </table>
                    <a-button
                        size="large"
                        type="primary"
                        v-if="orderItems.length > 0"
                        class="float-right mt-4"
                        @click="openCashModal = true"
                        >Submit Orders</a-button
                    >
                </div>
                <div class="col-span-1">
                    <div class="flex flex-col gap-8">
                        <a-input
                            size="large"
                            class="py-4"
                            v-model:value="totalAmount"
                        />
                        <a-input
                            allowClear
                            v-model:value="search"
                            size="large"
                            class="py-4"
                            placeholder="Search Anything..."
                        />
                        <a-button
                            size="large"
                            type="primary"
                            class=""
                            @click="searchItem"
                            >Search</a-button
                        >
                        <div
                            class="p-2 h-64 rounded-md"
                            style="border: 2px solid #e4e4e4"
                        >
                            <div
                                v-for="product in props.records.data"
                                :key="product.id"
                                class="bg-[#FFF8EB] p-4"
                            >
                                <div class="flex gap-2 items-center w-full">
                                    <img
                                        :src="
                                            product.image_url ??
                                            '/storage/IMG_4359.jpg'
                                        "
                                        class="w-12 h-12"
                                    />
                                    <div class="w-full">
                                        <p class="font-semibold">
                                            {{ product.name }}
                                        </p>
                                        <div
                                            class="flex gap-4 items-baseline justify-between w-full"
                                        >
                                            <p class="text-amber-500">
                                                ₱ {{ product.price }}
                                            </p>
                                            <p class="text-[#2369A6]">
                                                Stock:
                                                {{ product?.stock?.stock }}
                                            </p>
                                            <a-button
                                                type="primary"
                                                @click="addItem(product)"
                                                >Add Item</a-button
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quantity Modal -->
            <a-modal
                v-model:open="openQuantityModal"
                title="Enter Quantity"
                @ok="handleOk"
            >
                <a-input-number v-model:value="quantity" class="w-full" />
            </a-modal>

            <!-- Cash Modal -->

            <a-modal
                v-model:open="openCashModal"
                title="Purchase"
                @ok="submitOrders"
            >
                <a-form layout="vertical">
                    <a-form-item label="Cash">
                        <a-input-number v-model:value="cash" class="w-full" />
                    </a-form-item>
                    <a-form-item label="Change">
                        <a-input-number
                            v-model:value="changeAmount"
                            class="w-full"
                        />
                    </a-form-item>
                </a-form>
            </a-modal>
        </div>
    </CashierLayout>
</template>
