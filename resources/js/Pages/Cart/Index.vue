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
import { notification } from "ant-design-vue";

const props = defineProps({
    records: Array,
});

const totalPrice = computed(() => {
    return props.records.data.reduce((acc, curr) => {
        return acc + curr.product.price * curr.quantity;
    }, 0);
});

const form = useForm({
    records: props.records.data,
});

const updateCarts = () => {
    form.post(route("cart.updateCarts"), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            notification.success({
                message: "Product Added to Cart",
                description: "Your product has been added to your cart.",
            });
        },
    });
};

const removeCart = (record) => {
    form.delete(route("cart.destroy", record.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            notification.success({
                message: "Product Removed from Cart",
                description: "Your product has been removed from your cart.",
            });
        },
    });
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
                        <p class="text-2xl font-semibold">My Cart</p>
                        <hr style="border: 1px solid #ccc" />
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="w-full bg-[#FFF8EB]">
                                    <td class="py-2 px-4 text-left">Item</td>
                                    <td class="py-2 px-4 text-left">Price</td>
                                    <td class="py-2 px-4 text-left">
                                        Quantity
                                    </td>
                                    <td class="py-2 px-4 text-left">
                                        Sub-Total
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="record in form.records"
                                    :key="record.id"
                                >
                                    <td class="py-2 px-4 text-left">
                                        <div class="flex gap-2 items-center">
                                            <a-button
                                                shape="circle"
                                                @click="removeCart(record)"
                                            >
                                                <template #icon>
                                                    <CloseOutlined />
                                                </template>
                                            </a-button>
                                            <img
                                                :src="
                                                    record.product.image_url ??
                                                    '/storage/IMG_4359.jpg'
                                                "
                                                class="w-24 h-24"
                                                alt="category image"
                                            />
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-left">
                                        ₱ {{ record.product.price }}
                                    </td>
                                    <td class="py-2 px-4 text-left">
                                        <form class="max-w-xs">
                                            <div
                                                class="relative flex items-center max-w-[8rem]"
                                            >
                                                <button
                                                    @click="record.quantity--"
                                                    type="button"
                                                    id="decrement-button"
                                                    data-input-counter-decrement="quantity-input"
                                                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none"
                                                >
                                                    <svg
                                                        class="w-3 h-3 text-gray-900"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 18 2"
                                                    >
                                                        <path
                                                            stroke="currentColor"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M1 1h16"
                                                        />
                                                    </svg>
                                                </button>
                                                <input
                                                    type="text"
                                                    id="quantity-input"
                                                    data-input-counter
                                                    aria-describedby="helper-text-explanation"
                                                    class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5"
                                                    v-model="record.quantity"
                                                />
                                                <button
                                                    @click="record.quantity++"
                                                    type="button"
                                                    id="increment-button"
                                                    data-input-counter-increment="quantity-input"
                                                    class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none"
                                                >
                                                    <svg
                                                        class="w-3 h-3 text-gray-900"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 18 18"
                                                    >
                                                        <path
                                                            stroke="currentColor"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 1v16M1 9h16"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="py-2 px-4 text-left">
                                        <div class="flex gap-2 items-baseline">
                                            <p>
                                                ₱
                                                {{
                                                    record.quantity *
                                                    record.product.price
                                                }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr />
                        <div class="w-full flex justify-between mt-4">
                            <Link :href="route('home.index')">
                                <a-button type="default" size="large"
                                    >Back To Shop</a-button
                                >
                            </Link>

                            <a-button
                                :disabled="props.records.data.length === 0"
                                type="primary"
                                size="large"
                                @click="updateCarts"
                            >
                                Update Carts
                            </a-button>
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
                    <p class="text-2xl">Order Details</p>
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

                    <Link :href="route('checkout.index')" class="">
                        <a-button
                            type="primary"
                            block
                            size="large"
                            :disabled="props.records.data.length === 0"
                            >Proceed to Checkout</a-button
                        >
                    </Link>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
