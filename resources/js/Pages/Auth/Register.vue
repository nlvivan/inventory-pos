<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    first_name: "",
    last_name: "",
    middle_name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="w-full flex flex-col justify-center items-center mt-40">
            <p class="font-bold text-3xl text-[#1C486F]">Create your Account</p>
            <p class="text-[#ADADAD] font-semibold">
                Begin your grocery journey now!
            </p>
        </div>

        <a-form name="basic" layout="vertical" @submit.prevent="submit">
            <div>
                <a-form-item
                    :validate-status="form.errors.first_name ? 'error' : null"
                    :help="form.errors.first_name"
                    label="First Name"
                >
                    <a-input size="large" v-model:value="form.first_name" />
                </a-form-item>
            </div>
            <div>
                <a-form-item
                    :validate-status="form.errors.middle_name ? 'error' : null"
                    :help="form.errors.middle_name"
                    label="Middle Name"
                >
                    <a-input size="large" v-model:value="form.middle_name" />
                </a-form-item>
            </div>
            <div>
                <a-form-item
                    :validate-status="form.errors.last_name ? 'error' : null"
                    :help="form.errors.last_name"
                    label="Last Name"
                >
                    <a-input size="large" v-model:value="form.last_name" />
                </a-form-item>
            </div>
            <div>
                <a-form-item
                    :validate-status="form.errors.email ? 'error' : null"
                    :help="form.errors.email"
                    label="Email"
                >
                    <a-input size="large" v-model:value="form.email" />
                </a-form-item>
            </div>

            <div class="">
                <a-form-item
                    :validate-status="form.errors.password ? 'error' : null"
                    :help="form.errors.password"
                    label="Password"
                >
                    <a-input-password
                        size="large"
                        v-model:value="form.password"
                    />
                </a-form-item>
            </div>

            <div class="">
                <a-form-item
                    :validate-status="
                        form.errors.password_confirmation ? 'error' : null
                    "
                    :help="form.errors.password_confirmation"
                    label="Confirm Password"
                >
                    <a-input-password
                        size="large"
                        v-model:value="form.password_confirmation"
                    />
                </a-form-item>
            </div>

            <div class="flex flex-col gap-2 items-center mt-4">
                <a-button
                    :loading="form.processing"
                    size="large"
                    htmlType="submit"
                    type="primary"
                    block
                >
                    Sign Up
                </a-button>
                <div class="flex gap-1.5">
                    <p class="font-semibold">Already have an account?</p>
                    <Link
                        class="font-semibold text-amber-500"
                        :href="route('login')"
                        >Sign In</Link
                    >
                </div>
            </div>
        </a-form>
    </GuestLayout>
</template>
