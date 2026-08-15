<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="AIFono - Cadastro" />

        <form @submit.prevent="submit" class="w-full max-w-sm space-y-4">
            <div class="space-y-4">
                <div>
                    <InputLabel for="name" value="Nome Completo" class="text-white/70" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Seu nome completo"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" class="text-white/70" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="seu.email@exemplo.com"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Senha" class="text-white/70" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirmar Senha"
                        class="text-white/70"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-700 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <PrimaryButton
                    class="w-full justify-center !bg-blue-600 !hover:bg-blue-700 !text-white !font-bold !py-3 !rounded-full !text-base transition"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Criar Conta
                </PrimaryButton>
            </div>

            <div class="text-center text-sm text-white/70 pt-2">
                Já tem uma conta?
                <Link
                    :href="route('login')"
                    class="font-semibold text-blue-500 hover:text-blue-400 hover:underline ms-1 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                >
                    Entrar
                </Link>
            </div>

            <div class="mt-8 text-center text-xs text-white/50 px-4">
                Ao se cadastrar, você concorda com os Termos e Condições
            </div>
        </form>
    </GuestLayout>
</template>