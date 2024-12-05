<script setup>
import { ref } from "vue";
import CustomerLayout from "@/Layouts/CutomerLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Navigation, Slide } from "vue3-carousel";
import { notification } from "ant-design-vue";

const props = defineProps({
    product: Object,
    products: Array,
});

const form = useForm({
    product_id: props.product.data?.id,
    quantity: 1,
});

const submitProduct = () => {
    form.post(route("cart.store"), {
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
</script>
<template>
    <CustomerLayout>
        <Head title="Product Details" />
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4 mt-12 mb-12">
            <div class="w-full grid grid-cols-2 gap-2">
                <div
                    class="p-4 flex justify-center items-center rounded-lg"
                    style="
                        border-style: solid;
                        border-color: #adadad40;
                        border-width: 1px;
                        overflow: hidden; /* Prevents any overflow from the image */
                    "
                >
                    <img
                        :src="
                            props.product.data?.image_url ??
                            '/storage/IMG_4359.jpg'
                        "
                        style="
                            max-width: 100%; /* Ensures the image never exceeds the width of the container */
                            max-height: 100%; /* Ensures the image never exceeds the height of the container */
                            object-fit: contain; /* Ensures the image maintains its aspect ratio while fitting within the container */
                        "
                    />
                </div>

                <div class="p-4">
                    <p class="font-semibold">{{ props.product.data?.name }}</p>
                    <div>
                        <table class="w-full">
                            <tr>
                                <td>
                                    <p class="text-sm text-[#1C486F]">
                                        SKU:
                                        <span class="font-bold">{{
                                            props?.product?.data?.sku
                                        }}</span>
                                    </p>
                                </td>
                                <td>
                                    <p class="text-sm text-[#1C486F]">
                                        Available:
                                        <span
                                            class="font-bold text-amber-500"
                                            >{{
                                                props?.product?.data?.stock
                                                    ?.stock
                                            }}</span
                                        >
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="text-sm text-[#1C486F]">
                                        Description:
                                        <span class="font-bold">{{
                                            props?.product?.data?.notes
                                        }}</span>
                                    </p>
                                </td>
                                <td>
                                    <p class="text-sm text-[#1C486F]">
                                        Category:
                                        <span class="font-bold">{{
                                            props?.product?.data?.category?.name
                                        }}</span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <p class="text-2xl mt-4 font-bold text-amber-500">
                            ₱ {{ props.product.data?.price }}
                        </p>
                        <div class="mt-4">
                            <form class="max-w-xs">
                                <div
                                    class="relative flex items-center max-w-[8rem]"
                                >
                                    <button
                                        @click="form.quantity--"
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
                                        v-model="form.quantity"
                                    />
                                    <button
                                        @click="form.quantity++"
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
                            <a-button
                                type="primary"
                                size="large"
                                class="mt-12"
                                @click="submitProduct"
                            >
                                ADD TO CART
                            </a-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-semibold text-[#1C486F]">
                    Related Products
                </p>
                <Carousel
                    v-bind="settings"
                    :breakpoints="breakpoints"
                    v-if="props.products.data.length > 0"
                >
                    <Slide
                        v-for="product in props.products.data"
                        :key="product.id"
                    >
                        <div class="carousel__item">
                            <div
                                class="flex flex-col justify-center items-center p-6 rounded-lg"
                                style="
                                    border-style: solid;
                                    border-color: #adadad40;
                                    border-width: 1px;
                                "
                            >
                                <img
                                    :src="
                                        product.image_url ??
                                        '/storage/IMG_4359.jpg'
                                    "
                                    class="w-24 h-24"
                                    alt="category image"
                                />
                                <div class="mt-4 text-left flex flex-col gap-1">
                                    <span class="text-md text-[#ADADAD]">
                                        {{ product.category?.name }}
                                    </span>
                                    <span
                                        class="text-lg font-semibold text-[#1C486F]"
                                    >
                                        {{ product.name }}
                                    </span>
                                    <span
                                        class="text-amber-500 text-lg font-semibold mt-6"
                                    >
                                        ₱ {{ product.price }}
                                    </span>
                                    <p class="text-sm text-[#1C486F] mt-2">
                                        Stock: {{ product?.stock?.stock }}
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <Link
                                        :href="
                                            route(
                                                'home.product.details',
                                                product.id
                                            )
                                        "
                                    >
                                        <a-button type="primary" block>
                                            <template #icon>
                                                <ShoppingCartOutlined />
                                            </template>
                                            Add to Cart
                                        </a-button>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </Slide>

                    <template #addons>
                        <Navigation />
                    </template>
                </Carousel>
                <a-empty v-else description="No Related Products" />
            </div>
        </div>
    </CustomerLayout>
</template>
