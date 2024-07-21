<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div class="w-full flex flex-col justify-center items-center">
            <p class="font-bold text-3xl text-[#1C486F]">
                Sign in to your Account
            </p>
            <p class="text-[#ADADAD] font-semibold">
                Start your grocery with us!
            </p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <a-form name="basic" layout="vertical" @submit.prevent="submit">
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

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex flex-col gap-2 items-center mt-4">
                <a-button
                    :loading="form.processing"
                    size="large"
                    htmlType="submit"
                    type="primary"
                    block
                >
                    Sign In
                </a-button>
                <div class="flex gap-1.5">
                    <p class="font-semibold">Dont have an account?</p>
                    <Link
                        class="font-semibold text-amber-500"
                        :href="route('register')"
                        >Sign up</Link
                    >
                </div>
            </div>
        </a-form>
    </GuestLayout>
</template>
