<script setup>
import apexChart from "vue3-apexcharts";
import { ref, onMounted } from "vue";
import { useFormatDate } from "@/Composables/useFormatDate";
import { useGraphCategories } from "@/Composables/useGraphCategories";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const {
    dayRangeDate,
    weekRangeDate,
    monthRangeDate,
    getUserTimezone,
    formatGraphDates,
    formatCategoryDates,
} = useFormatDate();

const page = usePage();
const selectedFilter = ref("last_7");
const userTimezone = getUserTimezone();
const { last_year, last_30, last_7 } = useGraphCategories();
let categories = ref([]);
const data = ref([]);
const incomingData = ref([]);
const outgoingData = ref([]);
const currentFilter = ref(null);
const chartOptions = ref({});
const series = ref([]);
const salesTotal = ref([]);
const loading = ref(false);
const currentReseller = page?.props?.currentReseller?.id;
const currentAccount = page?.props?.currentAccount?.id;
const tickAmount = ref(7);

const options = [
    {
        label: "Last 7 days",
        value: "last_7",
    },
    {
        label: "Last 30 days",
        value: "last_30",
    },

    {
        label: "This Year",
        value: "last_year",
    },
];

async function getData(filterBy = "last_7") {
    try {
        loading.value = true;
        const response = await axios.get(
            `/admin/dashboard/sales?filter=${filterBy}`
        );
        data.value = response.data;

        setCategories(data.value, filterBy);
        loading.value = true;

        series.value = [
            {
                name: "Sales",
                data: salesTotal.value.map((arr) => arr.total),
            },
        ];

        chartOptions.value = {
            chart: {
                id: "outgoing-fax-activity",
                type: "area",
                zoom: {
                    enabled: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
            },
            toolbar: {
                show: false,
            },
            markers: {
                size: 2,
            },
            colors: ["#1691EA"],
            xaxis: {
                categories: categories.value,
                labels: {
                    formatter: function (value) {
                        const date = new Date(value);
                        if (selectedFilter.value === "last_30") {
                            return formatCategoryDates(date, "last_30");
                        }
                        return value;
                    },
                },
                tickAmount: tickAmount.value,
            },

            tooltip: {
                x: {
                    formatter: function (index) {
                        if (
                            selectedFilter.value === "last_7" ||
                            selectedFilter.value === "last_30"
                        ) {
                            const date = new Date(categories.value[index - 1]);
                            return formatGraphDates(date);
                        }

                        return categories.value[index - 1];
                    },
                },
            },
            grid: {
                padding: {
                    left: 20,
                    right: 20,
                },
            },
            fill: {
                opacity: 0.4,
                type: "solid",
            },
        };
    } catch (e) {
        console.log(e.message);
    } finally {
        loading.value = false;
    }
}

const setCategories = (salesData, filterBy) => {
    currentFilter.value = filterBy;
    if (filterBy == "last_30") {
        const { category, salesGraphData } = last_30(salesData, userTimezone);
        categories.value = category;
        salesTotal.value = salesGraphData;
        tickAmount.value = 2;
        return;
    }
    if (filterBy == "last_year") {
        const { category, salesGraphData } = last_year(salesData, userTimezone);
        console.log(salesGraphData);
        categories.value = category;
        salesTotal.value = salesGraphData;
        tickAmount.value = 12;
        return;
    }
    const { category, salesGraphData } = last_7(salesData, userTimezone);
    categories.value = category;
    salesTotal.value = salesGraphData;
    tickAmount.value = 7;
    return;
};

onMounted(() => {
    getData();
});
</script>

<template>
    <div>
        <a-card :bordered="false" :loading="loading">
            <template #title>
                <span class="font-bold">Sales Data</span>

                <a-select
                    ref="select"
                    class="text-xs float-right font-bold"
                    name="reseller"
                    style="width: 140px"
                    :options="options"
                    v-model:value="selectedFilter"
                    @change="getData"
                >
                </a-select>
            </template>
            <div>
                <apexChart
                    width="100%"
                    height="310"
                    type="area"
                    :options="chartOptions"
                    :series="series"
                />
            </div>
        </a-card>
    </div>
</template>
