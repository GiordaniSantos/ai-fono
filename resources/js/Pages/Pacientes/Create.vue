<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { maskPhone } from '@/Utils/mask';

const form = useForm({
    nome: '',
    data_nascimento: '',
    email: '',
    telefone: '',
    diagnostico: '',
});

const submit = () => {
    form.post(route('pacientes.store'));
};
</script>

<template>
    <Head title="Novo Paciente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('pacientes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Novo Paciente</h2>
            </div>
        </template>

        <div class="">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <InputLabel for="nome" value="Nome Completo *" />
                        <TextInput
                            id="nome"
                            type="text"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.nome"
                            required
                            autofocus
                            placeholder="Ex: João da Silva"
                        />
                        <InputError class="mt-2" :message="form.errors.nome" />
                    </div>

                    <!-- Linha dupla: Data de Nascimento & Telefone -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="data_nascimento" value="Data de Nascimento *" />
                            <TextInput
                                id="data_nascimento"
                                type="date"
                                class="mt-1 block w-full rounded-xl"
                                v-model="form.data_nascimento"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.data_nascimento" />
                        </div>

                        <div>
                            <InputLabel for="telefone" value="Telefone / WhatsApp" />
                            <TextInput
                                id="telefone"
                                type="text"
                                class="mt-1 block w-full rounded-xl"
                                v-model="form.telefone"
                                @input="form.telefone = maskPhone($event.target.value)"
                                maxlength="15"
                                placeholder="(00) 00000-0000"
                            />
                            <InputError class="mt-2" :message="form.errors.telefone" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="E-mail (opcional)" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.email"
                            placeholder="paciente@exemplo.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Diagnóstico / Observações -->
                    <div>
                        <InputLabel for="diagnostico" value="Diagnóstico / Queixa Principal" />
                        <textarea
                            id="diagnostico"
                            rows="4"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.diagnostico"
                            placeholder="Descreva o quadro clínico, objetivos terapêuticos ou observações iniciais..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.diagnostico" />
                    </div>

                    <!-- Rodapé de Ações -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('pacientes.index')"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition"
                        >
                            Cancelar
                        </Link>
                        <PrimaryButton
                            class="!rounded-xl !bg-blue-600 !px-6 !py-2.5 !font-semibold hover:!bg-blue-700"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Cadastrar Paciente
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>