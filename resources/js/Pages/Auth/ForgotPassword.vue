<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="AIFono - Recuperar Senha" />

        <div class="w-full max-w-sm">
            <div class="mb-6 text-sm text-center text-white/70 leading-relaxed">
                Esqueceu sua senha? Sem problemas. Informe seu endereço de e-mail e nós lhe enviaremos um link de redefinição para que você possa escolher uma nova.
            </div>

            <div
                v-if="status"
                class="mb-6 rounded-xl border border-green-500/20 bg-green-500/10 p-3 text-center text-sm font-medium text-green-400"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
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

                <div class="mt-8 flex justify-center">
                    <PrimaryButton
                        class="w-full justify-center !bg-blue-600 !hover:bg-blue-700 !text-white !font-bold !py-3 !rounded-full !text-base transition"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Enviar Link de Redefinição
                    </PrimaryButton>
                </div>

                <div class="text-center text-sm text-white/70 pt-4">
                    Lembrou a senha?
                    <Link
                        :href="route('login')"
                        class="font-semibold text-blue-500 hover:text-blue-400 hover:underline ms-1 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                    >
                        Voltar ao Login
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>