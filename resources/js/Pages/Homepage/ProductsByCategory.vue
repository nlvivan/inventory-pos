<script setup>
import { ref } from "vue";
import CustomerLayout from "@/Layouts/CutomerLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Navigation, Slide } from "vue3-carousel";
import { notification } from "ant-design-vue";

const props = defineProps({
    records: Object,
    category: Object,
    filters: Object,
});

const current = ref(props.records.meta.current_page);
const search = ref(props.filters.search)

const onChange = (page) => {
    router.reload(
        {
            data: {
                search: search.value,
                page: page,
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
    <CustomerLayout>
        <Head title="Product Details" />
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-4 mt-6 mb-12">
            <p class="text-2xl font-semibold text-[#1C486F]">
                {{ props.category.name }}
            </p>
            <div v-if="props.records.data.length > 0">
                <div class="grid grid-cols-4 gap-2">
                    <div
                        v-for="product in props.records.data"
                        :key="product.id"
                    >
                        <div>
                            <div
                                class="flex flex-col justify-center py-4 items-center rounded-lg"
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
                                        Stock: {{ product.stock.stock }}
                                    </p>
                                </div>
                                <div class="mt-4 w-full p-2 px-12">
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
                    </div>
                </div>
                <div class="flex justify-center mt-6">
                    <a-pagination
                        v-model:current="current"
                        :total="props.records.meta.total"
                        :pageSize="props.records.meta.per_page"
                        @change="onChange"
                    />
                </div>
            </div>
            <div class="w-full" v-else>
                <a-empty />
            </div>
        </div>
    </CustomerLayout>
</template>
