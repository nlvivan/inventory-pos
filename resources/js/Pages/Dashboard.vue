<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import SalesIcon from "@/Components/SalesIcon.vue";

import { IconReportMoney } from "@tabler/icons-vue";
import OrderData from "@/Components/OrderData.vue";

const props = defineProps({
    totalSalesThisDay: "",
    totalSalesThisMonth: "",
    totalSalesThisWeek: "",
    totalSalesThisYear: "",
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <p class="font-bold text-2xl">Dashboard</p>
                <div class="w-full grid grid-cols-4 gap-6">
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisDay }}
                                </span>
                                <br />
                                <span class="text-sm">Total Sales Today</span>
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisWeek }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Week</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisMonth }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Month</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                    <a-card :bordered="false">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-bold">
                                    ₱ {{ props.totalSalesThisYear }}
                                </span>
                                <br />
                                <span class="text-sm"
                                    >Total Sales This Year</span
                                >
                            </div>
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full border border-blue-500"
                            >
                                <SalesIcon />
                            </div>
                        </div>
                    </a-card>
                </div>
                <OrderData class="mt-6" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
