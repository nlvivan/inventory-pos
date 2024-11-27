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
    productionBatches: Array,
    products: Array,
});

const imageUrl = ref("");
const fileList = ref([]);
const form = useForm({
    id: "",
    product_id: "",
    count: "",
    reason: "",
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
        title: "Name",
        dataIndex: ["product", "name"],
        key: "name",
    },
    {
        title: "Stock return",
        dataIndex: "count",
        key: "count",
    },
    {
        title: "Reason",
        dataIndex: "reason",
        key: "reason",
    },
];

const current = ref(props.records.current_page);
const pageSize = ref(props.records.per_page);
const visibleDeleteConfirmation = ref(false);
const deleteInput = ref("");

const pagination = computed(() => ({
    total: props.records.total,
    current: props.records.current_page,
    pageSize: props.records.per_page,
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
    form.post(route("product-returns.store"), {
        onSuccess: () => {
            closeCreateModal();
            message.success("Product Return Created Sucessfully!");
        },
    });
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
            production_batch_id: form.production_batch_id,
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

const deleteData = () => {
    loading.value = true;
    form.delete(route("products.destroy", selectedID.value), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            loading.value = false;
            cancelDeleteData();
            message.success("Product Deleted Sucessfully!");
        },
    });
};

const editData = (data) => {
    form.errors = {};
    form.reset();
    imageUrl.value = data.image_url;
    isCreateModalVisible.value = true;
    isEdit.value = true;
    form.id = data.id;
    form.category_id = data.category_id;
    form.production_batch_id = data.production_batch_id;
    form.name = data.name;
    form.notes = data.notes;
    form.price = data.price;
    form.sku = data.sku;
    form.expiry_date = data.expiry_date;
    expiryDate.value = dayjs(data.expiry_date);
};

const addStockForm = useForm({
    product_id: "",
    stock: "",
});

const showAddStockModal = ref(false);

const addStock = (data) => {
    addStockForm.product_id = data.id;
    showAddStockModal.value = true;
};

const closeAddStockModal = () => {
    addStockForm.reset();
    addStockForm.errors = {};
};

const sumbitAddStock = () => {
    addStockForm.post(route("products.addStock", addStockForm.product_id), {
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
            <p class="text-2xl font-bold">Product Returns</p>
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
                        <a-button
                            type="primary"
                            class="rounded-[5px]"
                            @click="showCreateModal"
                            >Return Product</a-button
                        >
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
                                            record.image_url ??
                                            '/storage/IMG_4359.jpg'
                                        "
                                    />
                                </template>
                                <template v-if="column.dataIndex === 'price'">
                                    ₱ {{ record.price }}
                                </template>
                                <template v-if="column.dataIndex === 'stock'">
                                    {{ record.stock?.stock ?? 0 }}
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
                                        <a-tooltip title="Delete">
                                            <a-button
                                                @click="
                                                    showDeleteConfirmationModal(
                                                        record.id
                                                    )
                                                "
                                                shape="circle"
                                                :loading="
                                                    loading &&
                                                    selectedID === record.id
                                                "
                                            >
                                                <template #icon>
                                                    <DeleteOutlined />
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
            <!-- create or update modal -->
            <a-modal
                :maskClosable="true"
                v-model:open="isCreateModalVisible"
                title="Return Product"
                :footer="false"
                size="md"
            >
                <a-form
                    :label-col="{ span: 24 }"
                    :wrapper-col="{ span: 24 }"
                    @submit.prevent="submitForm"
                >
                    <div></div>
                    <div>
                        <a-form-item
                            label="Product"
                            :validate-status="
                                form.errors.product_id ? 'error' : null
                            "
                            :help="form.errors.product_id"
                        >
                            <a-select
                                ref="select"
                                show-search
                                allow-clear
                                v-model:value="form.product_id"
                                placeholder="Select Product"
                                style="width: 100%"
                                :options="
                                    props.products
                                        ?.map((product) => ({
                                            value: product.id,
                                            label: product.name,
                                        }))
                                        .sort((a, b) =>
                                            a.label.localeCompare(b.label)
                                        )
                                "
                                :filter-option="filterOption"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Stock Count"
                            :validate-status="
                                form.errors.count ? 'error' : null
                            "
                            :help="form.errors.count"
                        >
                            <a-input-number
                                v-model:value="form.count"
                                class="w-full"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Reason"
                            :validate-status="
                                form.errors.reason ? 'error' : null
                            "
                            :help="form.errors.reason"
                        >
                            <a-textarea
                                v-model:value="form.reason"
                                class="w-full"
                            />
                        </a-form-item>
                    </div>
                    <div class="w-full">
                        <a-form-item
                            :wrapper-col="{ offset: 4, span: 20 }"
                            class="mb-0"
                        >
                            <div class="flex justify-end gap-2">
                                <a-button key="back" @click="closeCreateModal"
                                    >Cancel</a-button
                                >
                                <a-button
                                    type="primary"
                                    htmlType="submit"
                                    :loading="form.processing || updateLoading"
                                    >{{
                                        isEdit ? "Update" : "Submit"
                                    }}</a-button
                                >
                            </div>
                        </a-form-item>
                    </div>
                </a-form>
            </a-modal>
            <!-- Delete Confirmation -->
            <a-modal
                v-model:open="visibleDeleteConfirmation"
                title="Delete Product"
                @ok="deleteData"
                @cancel="cancelDeleteData"
            >
                <p>Are you sure you want to delete this product?</p>
                <p>Type '<b>delete</b>' to proceed</p>
                <div class="mt-4">
                    <a-input v-model:value="deleteInput"></a-input>
                </div>
                <template #footer>
                    <a-button key="back" @click="cancelDeleteData"
                        >Cancel</a-button
                    >
                    <a-button
                        key="submit"
                        type="primary"
                        danger
                        @click="deleteData"
                        :loading="form.processing || updateLoading"
                        :disabled="deleteInput !== 'delete'"
                    >
                        Delete
                    </a-button>
                </template>
            </a-modal>
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
        </div>
    </AuthenticatedLayout>
</template>
