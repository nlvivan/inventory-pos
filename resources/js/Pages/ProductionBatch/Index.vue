<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { message } from "ant-design-vue";
import { EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import dayjs from "dayjs";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    records: Object,
    filters: Object,
});

const form = useForm({
    batch_number: "",
    production_date: "",
    expiration_date: "",
});

const columns = [
    {
        title: "Batch Number",
        dataIndex: "batch_number",
        key: "batch_number",
    },
    {
        title: "Production Date",
        dataIndex: "production_date",
        key: "production_date",
    },
    {
        title: "Expiration Date",
        dataIndex: "expiration_date",
        key: "expiration_date",
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
    productionDate.value = null;
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
    form.post(route("production-batches.store"), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("Production Batch Created Sucessfully!");
        },
    });
};

const updateData = () => {
    form.put(route("production-batches.update", form.id), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("Production Batch Updated Sucessfully!");
        },
    });
};

const deleteData = () => {
    loading.value = true;
    form.delete(route("production-batches.destroy", selectedID.value), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            loading.value = false;
            cancelDeleteData();
            message.success("Category Deleted Sucessfully!");
        },
    });
};

const productionDate = ref(null);
const expirationDate = ref(null);

const editData = (data) => {
    form.errors = {};
    form.reset();
    isCreateModalVisible.value = true;
    isEdit.value = true;

    for (const [key, value] of Object.entries(data)) {
        form[key] = value;
    }

    productionDate.value = dayjs(data.production_date);
    expirationDate.value = dayjs(data.expiration_date);
};

const handleChangeDate = (value, dateString) => {
    form.production_date = value.format("YYYY-MM-DD HH:mm");
};

const handleChangeExpirationDate = (value, dateString) => {
    form.expiration_date = value.format("YYYY-MM-DD HH:mm");
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
        <Head title="Production Batches" />
        <div>
            <p class="text-2xl font-bold">Production Batches</p>
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
                            >Create Production Batch</a-button
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
                                <template v-if="column.dataIndex === 'action'">
                                    <div class="flex gap-2">
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
                :title="
                    isEdit
                        ? 'Update Production Batch'
                        : 'Create Production Batch'
                "
                :footer="false"
                size="md"
            >
                <a-form
                    :label-col="{ span: 24 }"
                    :wrapper-col="{ span: 24 }"
                    @submit.prevent="submitForm"
                >
                    <div>
                        <a-form-item
                            label="Batch Number"
                            :validate-status="
                                form.errors.batch_number ? 'error' : null
                            "
                            :help="form.errors.batch_number"
                        >
                            <a-input
                                placeholder="Batch Number"
                                v-model:value="form.batch_number"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Production Date"
                            :validate-status="
                                form.errors.production_date ? 'error' : null
                            "
                            :help="form.errors.production_date"
                        >
                            <a-date-picker
                                v-model:value="expirationDate"
                                placeholder="Production Date"
                                @change="handleChangeDate"
                                style="width: 100%"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Expiration Date"
                            :validate-status="
                                form.errors.expiration_date ? 'error' : null
                            "
                            :help="form.errors.expiration_date"
                        >
                            <a-date-picker
                                v-model:value="productionDate"
                                placeholder="Production Date"
                                @change="handleChangeExpirationDate"
                                style="width: 100%"
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
                                    :loading="form.processing"
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
                title="Delete Production Batch"
                @ok="deleteData"
                @cancel="cancelDeleteData"
            >
                <p>Are you sure you want to delete this Production Batch?</p>
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
                        :loading="form.processing"
                        :disabled="deleteInput !== 'delete'"
                    >
                        Delete
                    </a-button>
                </template>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
