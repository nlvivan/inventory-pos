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
    name: "",
    email: "",
    role: "",
});

const columns = [
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Email",
        dataIndex: "email",
        key: "email",
    },
    {
        title: "Role",
        dataIndex: "role",
        key: "role",
    },
    {
        title: "Action",
        dataIndex: "action",
        key: "action",
        class: "w-1 text-center",
    },
];

const rolesOptions = [
    {
        label: "Cashier",
        value: "cashier",
    },
    {
        label: "Customer",
        value: "customer",
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
    form.post(route("users.store"), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("New User has been created!");
        },
    });
};

const updateData = () => {
    form.put(route("users.update", form.id), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("User Updated Sucessfully!");
        },
    });
};

const deleteData = () => {
    loading.value = true;
    form.delete(route("users.destroy", selectedID.value), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            loading.value = false;
            cancelDeleteData();
            message.success("User deleted!");
        },
    });
};

const productionDate = ref(null);

const editData = (data) => {
    form.errors = {};
    form.reset();
    isCreateModalVisible.value = true;
    isEdit.value = true;

    for (const [key, value] of Object.entries(data)) {
        form[key] = value;
    }

    form.role = data.roles[0].name;
};

const handleChangeDate = (value, dateString) => {
    form.production_date = value.format("YYYY-MM-DD HH:mm");
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
        <Head title="Users" />
        <div>
            <p class="text-2xl font-bold">Users</p>
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
                            >Create User</a-button
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
                                <template v-if="column.dataIndex === 'role'">
                                    {{ record.roles[0].name }}
                                </template>
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
                :title="isEdit ? 'Update User' : 'Create User'"
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
                            label="Name"
                            :validate-status="form.errors.name ? 'error' : null"
                            :help="form.errors.name"
                        >
                            <a-input
                                placeholder="Name"
                                v-model:value="form.name"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Email"
                            :validate-status="
                                form.errors.email ? 'error' : null
                            "
                            :help="form.errors.email"
                        >
                            <a-input
                                placeholder="Email"
                                v-model:value="form.email"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Role"
                            :validate-status="form.errors.role ? 'error' : null"
                            :help="form.errors.role"
                        >
                            <a-select
                                ref="select"
                                name="reseller"
                                style="width: 100%"
                                :options="rolesOptions"
                                v-model:value="form.role"
                            >
                            </a-select>
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
                title="Delete IUser"
                @ok="deleteData"
                @cancel="cancelDeleteData"
            >
                <p>Are you sure you want to delete this user?</p>
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
