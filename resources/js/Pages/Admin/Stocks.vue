<script setup>
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { watchDebounced } from "@vueuse/core";
import { message } from "ant-design-vue";
import {
    EditOutlined,
    DeleteOutlined,
    LoadingOutlined,
    PlusOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";

const props = defineProps({
    records: Object,
    filters: Object,
    categories: Array,
});

const imageUrl = ref("");
const fileList = ref([]);
const form = useForm({
    id: "",
    image_url: "",
    category_id: "",
    name: "",
    notes: "",
    price: "",
    sku: "",
    expiry_date: "",
    has_image_url: false,
});

const handleChange = (info) => {
    form.image_url = info.file;
    form.has_image_url = true;
    imageUrl.value = window.URL.createObjectURL(info.file);
    loading.value = false;
};

function removeImage() {
    imageUrl.value = "";
    form.image_url = "";
    form.has_image_url = false;
    fileList.value = [];
}

const expiryDate = ref(null);

const handleChangeDate = (value, dateString) => {
    console.log(value);
    form.expiry_date = value.format("YYYY-MM-DD HH:mm");
};

function filterOption(input, option) {
    return String(option.label).toLowerCase().indexOf(input.toLowerCase()) >= 0;
}

const columns = [
    {
        title: "Image",
        dataIndex: "image",
        key: "image",
        class: "w-32",
    },
    {
        title: "Name",
        dataIndex: ["product", "name"],
        key: "name",
    },
    {
        title: "Stock",
        dataIndex: "stock",
        key: "stock",
    },
    {
        title: "Critical Stock",
        dataIndex: "critical_stock",
        key: "critical_stock",
    },
    {
        title: "Action",
        dataIndex: "action",
        key: "action",
        class: "w-1 text-center",
    },
];

const current = ref(props.records.current_page);
const pageSize = ref(props.records.per_page);
const visibleDeleteConfirmation = ref(false);
const deleteInput = ref("");

const pagination = computed(() => ({
    total: props.records.meta.total,
    current: props.records.meta.current_page,
    pageSize: props.records.meta.per_page,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: true,
    pageSizeOptions: ["25", "50", "100"],
}));

const search = ref(props.filters.search);
const isEdit = ref(false);
const isCreateModalVisible = ref(false);
const selectedID = ref(null);
const loading = ref(false);

const showDeleteConfirmationModal = (id) => {
    selectedID.value = id;
    visibleDeleteConfirmation.value = true;
};

const cancelDeleteData = () => {
    deleteInput.value = "";
    selectedID.value = null;
    visibleDeleteConfirmation.value = false;
};

const showCreateModal = () => {
    form.errors = {};
    form.reset();
    isEdit.value = false;
    isCreateModalVisible.value = true;
};

const closeCreateModal = () => {
    form.errors = {};
    form.reset();
    expiryDate.value = null;
    isCreateModalVisible.value = false;
};

const handleTableChange = (event) => {
    current.value = event.current;
    pageSize.value = event.pageSize;

    router.reload(
        {
            data: {
                search: search.value,
                page: event.current,
                per_page: event.pageSize,
            },
        },
        {
            replace: true,
            preserveState: true,
        }
    );
};

const submitForm = () => {
    if (isEdit.value) {
        updateData();
    } else {
        createData();
    }
};

const createData = () => {
    form.post(route("products.store"), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("Product Created Sucessfully!");
        },
    });
};

const updateLoading = ref(false);

const updateData = () => {
    updateLoading.value = true;
    router.post(
        route("products.update", form.id),
        {
            _method: "put",
            name: form.name,
            image_url: form.image_url,
            notes: form.notes,
            category_id: form.category_id,
            price: form.price,
            sku: form.sku,
            expiry_date: form.expiry_date,
            has_image_url: form.has_image_url,
        },
        {
            onSuccess: () => {
                closeCreateModal();
                message.success("Product Updated Sucessfully!");
                form.reset();
                imageUrl.value = "";
                fileList.value = [];
                updateLoading.value = false;
            },
        }
    );
};

const addStockForm = useForm({
    id: "",
    stock: "",
});

const stockForm = useForm({
    id: "",
    stock: "",
    critical_stock: "",
});

const showEditModal = ref(false);
const editData = (data) => {
    stockForm.errors = {};
    stockForm.reset();
    stockForm.id = data.id;
    stockForm.stock = data.stock;
    stockForm.critical_stock = data.critical_stock;
    showEditModal.value = true;
};

const updateRecord = () => {
    stockForm.put(route("stocks.update", stockForm.id), {
        preserveState: false,
        preserveScroll: true,
        onSuccess: () => {
            message.success("Stock Updated Successfully");
            stockForm.reset();
            showEditModal.value = false;
            stockForm.errors = {};
        },
    });
};

const showAddStockModal = ref(false);

const addStock = (data) => {
    addStockForm.id = data.id;
    showAddStockModal.value = true;
};

const closeAddStockModal = () => {
    addStockForm.reset();
    addStockForm.errors = {};
};

const sumbitAddStock = () => {
    addStockForm.post(route("stocks.addStock", addStockForm.id), {
        preserveState: false,
        preserveScroll: true,
        onSuccess: () => {
            message.success("Stock Added successfully");
            addStockForm.reset();
            showAddStockModal.value = false;
            addStockForm.errors = {};
        },
    });
};

watchDebounced(
    search,
    () => {
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
    },
    { debounce: 300 }
);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Products" />
        <div>
            <p class="text-2xl font-bold">Stocks</p>
            <div class="w-full">
                <a-card :bordered="false" class="table-container">
                    <div class="flex justify-between table-header-action">
                        <div class="flex gap-2">
                            <a-input-search
                                v-model:value="search"
                                :allowClear="true"
                                placeholder="Search"
                            />
                        </div>
                    </div>

                    <div class="w-full">
                        <a-table
                            :columns="columns"
                            :data-source="props.records.data"
                            @change="handleTableChange"
                            :pagination="{ ...pagination }"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'image'">
                                    <img
                                        class="h-16 w-16 rounded-full"
                                        :src="
                                            record.product.image_url ??
                                            '/storage/IMG_4359.jpg'
                                        "
                                    />
                                </template>
                                <template v-if="column.dataIndex === 'price'">
                                    ₱ {{ record.price }}
                                </template>
                                <template v-if="column.dataIndex === 'stock'">
                                    {{ record.stock ?? 0 }}
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <div class="flex gap-2">
                                        <a-tooltip title="Add Stock">
                                            <a-button
                                                @click="addStock(record)"
                                                shape="circle"
                                            >
                                                <template #icon>
                                                    <AppstoreAddOutlined />
                                                </template>
                                            </a-button>
                                        </a-tooltip>
                                        <a-tooltip title="Edit">
                                            <a-button
                                                @click="editData(record)"
                                                shape="circle"
                                            >
                                                <template #icon>
                                                    <EditOutlined />
                                                </template>
                                            </a-button>
                                        </a-tooltip>
                                    </div>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </a-card>
            </div>
            <!-- Add Stock Modal -->
            <a-modal
                v-model:open="showAddStockModal"
                title="Add Stock"
                :footer="false"
            >
                <a-form layout="vertical" @submit="sumbitAddStock">
                    <a-form-item
                        label="Quantity"
                        :validate-status="
                            addStockForm.errors.stock ? 'error' : null
                        "
                        :help="addStockForm.errors.stock"
                    >
                        <a-input-number
                            v-model:value="addStockForm.stock"
                            class="w-full"
                        />
                    </a-form-item>
                    <a-form-item :wrapper-col="{ offset: 16, span: 4 }">
                        <div class="flex gap-2">
                            <a-button
                                type="default"
                                :loading="addStockForm.processing"
                                @click="closeAddStockModal"
                                >Cancel</a-button
                            >
                            <a-button
                                :loading="addStockForm.processing"
                                type="primary"
                                html-type="submit"
                                >Submit</a-button
                            >
                        </div>
                    </a-form-item>
                </a-form>
            </a-modal>

            <a-modal
                v-model:open="showEditModal"
                title="Update Stock"
                :footer="false"
            >
                <a-form layout="vertical" @submit="updateRecord">
                    <a-form-item
                        label="Stock"
                        :validate-status="
                            stockForm.errors.stock ? 'error' : null
                        "
                        :help="stockForm.errors.stock"
                    >
                        <a-input-number
                            v-model:value="stockForm.stock"
                            class="w-full"
                        />
                    </a-form-item>
                    <a-form-item
                        label="Critical Stock"
                        :validate-status="
                            stockForm.errors.critical_stock ? 'error' : null
                        "
                        :help="stockForm.errors.critical_stock"
                    >
                        <a-input-number
                            v-model:value="stockForm.critical_stock"
                            class="w-full"
                        />
                    </a-form-item>
                    <a-form-item :wrapper-col="{ offset: 16, span: 4 }">
                        <div class="flex gap-2">
                            <a-button
                                type="default"
                                :loading="addStockForm.processing"
                                @click="closeAddStockModal"
                                >Cancel</a-button
                            >
                            <a-button
                                :loading="addStockForm.processing"
                                type="primary"
                                html-type="submit"
                                >Submit</a-button
                            >
                        </div>
                    </a-form-item>
                </a-form>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
