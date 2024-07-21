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

const search = ref(props?.filters?.search);
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
        </div>
    </CashierLayout>
</template>
