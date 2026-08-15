<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, KeyIcon } from '@heroicons/vue/24/outline';
import { maskPhone } from '@/Utils/mask';

const props = defineProps({
    paciente: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    nome: props.paciente.nome,
    data_nascimento: props.paciente.data_nascimento ? props.paciente.data_nascimento.substring(0, 10) : '',
    email: props.paciente.email ?? '',
    telefone: maskPhone(props.paciente.telefone ?? ''),
    diagnostico: props.paciente.diagnostico ?? '',
});

const submit = () => {
    form.patch(route('pacientes.update', props.paciente.id));
};
</script>

<template>
    <Head :title="`Editar - ${paciente.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('pacientes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Editar Paciente</h2>
            </div>
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-blue-600 p-2 text-white">
                        <KeyIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-blue-900">Código de Acesso do Paciente</div>
                        <div class="text-xs text-blue-700">Utilizado para o paciente se autenticar no aplicativo.</div>
                    </div>
                </div>
                <span class="rounded-xl border border-blue-200 bg-white px-3.5 py-1.5 font-mono text-base font-bold text-blue-700 shadow-sm">
                    {{ paciente.codigo_acesso }}
                </span>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="nome" value="Nome Completo *" />
                        <TextInput
                            id="nome"
                            type="text"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.nome"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.nome" />
                    </div>
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
                            />
                            <InputError class="mt-2" :message="form.errors.telefone" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="email" value="E-mail" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="diagnostico" value="Diagnóstico / Queixa Principal" />
                        <textarea
                            id="diagnostico"
                            rows="4"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.diagnostico"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.diagnostico" />
                    </div>

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
                            Salvar Alterações
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>