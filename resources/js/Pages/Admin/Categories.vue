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
} from "@ant-design/icons-vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    records: Object,
    filters: Object,
});

const imageUrl = ref("");
const fileList = ref([]);
const form = useForm({
    id: "",
    image_url: "",
    has_image_url: false,
    name: "",
    description: "",
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

const columns = [
    {
        title: "Image",
        dataIndex: "image",
        key: "image",
        class: "w-32",
    },
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Description",
        dataIndex: "description",
        key: "description",
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
    form.post(route("categories.store"), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            closeCreateModal();
            message.success("Category Created Sucessfully!");
        },
    });
};

const updateLoading = ref(false);

const updateData = () => {
    updateLoading.value = true;
    router.post(
        route("categories.update", form.id),
        {
            _method: "put",
            name: form.name,
            image_url: form.image_url,
            description: form.description,
        },
        {
            onSuccess: () => {
                closeCreateModal();
                message.success("Category Updated Sucessfully!");
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
    form.delete(route("categories.destroy", selectedID.value), {
        preserveScroll: false,
        preserveState: true,
        onSuccess: () => {
            loading.value = false;
            cancelDeleteData();
            message.success("Category Deleted Sucessfully!");
        },
    });
};

const editData = (data) => {
    form.errors = {};
    form.reset();
    imageUrl.value = data.image_url;
    isCreateModalVisible.value = true;
    isEdit.value = true;
    form.description = data.description;
    form.id = data.id;
    form.name = data.name;
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
    <Head title="Categories" />
    <div>
        <p class="text-2xl font-bold">Categories</p>
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
                        >Create Category</a-button
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
            :title="isEdit ? 'Update Category' : 'Create Category'"
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
                        label="Image"
                        :validate-status="
                            form.errors.image_url ? 'error' : null
                        "
                        :help="form.errors.image_url"
                    >
                        <a-upload
                            v-model:file-list="fileList"
                            name="avatar"
                            list-type="picture-card"
                            class="avatar-uploader"
                            :accept="'image/jpeg,image/png,image/jpg'"
                            :show-upload-list="false"
                            @change="handleChange"
                            :before-upload="() => false"
                        >
                            <img
                                v-if="imageUrl"
                                :src="imageUrl"
                                alt="avatar"
                                class="h-16 w-16 object-contain rounded"
                            />
                            <div v-else>
                                <LoadingOutlined
                                    v-if="loading"
                                ></LoadingOutlined>
                                <PlusOutlined v-else></PlusOutlined>
                                <div class="ant-upload-text">Upload</div>
                            </div>
                        </a-upload>
                        <a
                            v-if="imageUrl"
                            @click="removeImage"
                            class="cursor-pointer text-sm"
                            >Remove Image</a
                        >
                    </a-form-item>
                </div>
                <div>
                    <a-form-item
                        label="Name"
                        :validate-status="form.errors.name ? 'error' : null"
                        :help="form.errors.name"
                    >
                        <a-input v-model:value="form.name" />
                    </a-form-item>
                    <a-form-item label="Description">
                        <a-textarea
                            v-model:value="form.description"
                        ></a-textarea>
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
                                >{{ isEdit ? "Update" : "Submit" }}</a-button
                            >
                        </div>
                    </a-form-item>
                </div>
            </a-form>
        </a-modal>
        <!-- Delete Confirmation -->
        <a-modal
            v-model:open="visibleDeleteConfirmation"
            title="Delete Category"
            @ok="deleteData"
            @cancel="cancelDeleteData"
        >
            <p>Are you sure you want to delete this Category?</p>
            <p>Type '<b>delete</b>' to proceed</p>
            <div class="mt-4">
                <a-input v-model:value="deleteInput"></a-input>
            </div>
            <template #footer>
                <a-button key="back" @click="cancelDeleteData">Cancel</a-button>
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
    </div>
</AuthenticatedLayout>
</template>
