<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="AIFono - Login" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="w-full max-w-sm space-y-4">

            <div class="space-y-4">
                <div>
                    <InputLabel for="email" value="Email" class="text-white/70" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="seu.email@exemplo.com"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Senha" class="text-white/70" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <div class="block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-700 bg-gray-900 text-blue-600 focus:ring-blue-500" />
                        <span class="ms-2 text-sm text-white/70">Lembrar de mim</span>
                    </label>
                </div>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-white/70 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Esqueceu a senha?
                </Link>
            </div>

            <div class="mt-8 flex justify-center">
                <PrimaryButton
                    class="w-full justify-center !bg-blue-600 !hover:bg-blue-700 !text-white !font-bold !py-3 !rounded-full !text-base transition"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Entrar com Email
                </PrimaryButton>
            </div>

            <div class="text-center text-sm text-white/70 pt-2">
                Não tem uma conta?
                <Link
                    :href="route('register')"
                    class="font-semibold text-blue-500 hover:text-blue-400 hover:underline ms-1 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                >
                    Cadastre-se
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>