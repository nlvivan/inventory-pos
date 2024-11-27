<script setup>
import { ref } from "vue";
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
} from "@ant-design/icons-vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";

import "vue3-carousel/dist/carousel.css";

const props = defineProps({
    categories: Array,
    products: Array,
});

const settings = {
    itemsToShow: 1,
    snapAlign: "center",
};

const breakpoints = {
    // 700px and up
    700: {
        itemsToShow: 3.5,
        snapAlign: "center",
    },
    // 1024 and up
    1024: {
        itemsToShow: 5,
        snapAlign: "start",
    },
};

const email = ref("");
</script>

<template>
    <CustomerLayout>
        <Head title="Home" />
        <div class="bg-image mt-6 flex flex-col justify-center items-center">
            <p class="text-4xl font-bold text-[#1C486F]">Your One Stop Shop</p>
            <p class="text-4xl font-bold text-[#1C486F]">Grocery Store</p>
            <p class="text-lg text-[#838383]"></p>
            <a-input-search
                v-model:value="value"
                placeholder="Enter your email address"
                class="w-1/4"
                size="large"
                @search="onSearch"
            >
                <template #enterButton>
                    <a-button type="primary">Subscribe</a-button>
                </template>
            </a-input-search>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4 mt-4">
            <p class="text-2xl font-semibold text-[#1C486F]">
                Explore Categories
            </p>
            <Carousel v-bind="settings" :breakpoints="breakpoints">
                <Slide
                    v-for="(category, index) in categories.data"
                    :key="category.id"
                >
                    <Link
                        :href="route('home.productsByCategory', category.id)"
                        class="no-underline"
                    >
                        <div class="carousel__item">
                            <div
                                class="flex flex-col justify-center items-center p-6 rounded-lg"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-[#F3F7FC]'
                                        : ' bg-[#FFF8EB]'
                                "
                            >
                                <img
                                    :src="
                                        category.image_url ??
                                        '/storage/IMG_4359.jpg'
                                    "
                                    class="w-12 h-12"
                                    alt="category image"
                                />
                                <p
                                    class="text-lg font-semibold text-[#1C486F] no-underline"
                                >
                                    {{ category.name }}
                                </p>
                            </div>
                        </div></Link
                    >
                </Slide>

                <template #addons>
                    <Navigation />
                </template>
            </Carousel>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4 mt-4">
            <p class="text-2xl font-semibold text-[#1C486F]">New Products</p>
            <Carousel v-bind="settings" :breakpoints="breakpoints">
                <Slide v-for="product in props.products.data" :key="product.id">
                    <div
                        class="carousel__item flex flex-col justify-between p-4 rounded-lg border border-gray-300 shadow-md w-64 h-96"
                    >
                        <!-- Product Image -->
                        <div class="flex justify-center">
                            <img
                                :src="
                                    product.image_url ?? '/storage/IMG_4359.jpg'
                                "
                                class="w-24 h-24 object-cover"
                                alt="product image"
                            />
                        </div>

                        <!-- Product Details -->
                        <div class="mt-4 text-left flex flex-col gap-1 h-32">
                            <span class="text-sm text-gray-400 truncate">
                                {{ product.category?.name }}
                            </span>
                            <span
                                class="text-lg font-semibold text-blue-900 truncate"
                            >
                                {{ product.name }}
                            </span>
                            <span
                                class="text-lg font-semibold text-amber-500 mt-2"
                            >
                                ₱ {{ product.price }}
                            </span>
                            <p class="text-sm text-blue-900">
                                Stock: {{ product?.stock?.stock }}
                            </p>
                        </div>

                        <!-- Add to Cart Button -->
                        <div class="mt-auto w-full">
                            <Link
                                :href="
                                    route('home.product.details', product.id)
                                "
                                class="block"
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
                </Slide>

                <template #addons>
                    <Navigation />
                </template>
            </Carousel>
        </div>
    </CustomerLayout>
</template>

<style>
.bg-image {
    background-image: url("/storage/assets/HERO BANNER.png");
    height: 420px; /* You must set a specified height */
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Do not repeat the image */
    background-size: cover;
}
</style>
