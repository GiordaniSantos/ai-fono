<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    exercicio: {
        type: Object,
        required: true,
    },
    pacientes: {
        type: Array,
        default: () => [],
    },
    categorias: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    nome: props.exercicio.nome,
    categoria_id: props.exercicio.categoria_id ?? '',
    paciente_id: props.exercicio.paciente_id ?? '',
    descricao: props.exercicio.descricao ?? '',
});

const submit = () => {
    form.patch(route('exercicios.update', props.exercicio.id));
};
</script>

<template>
    <Head :title="`Editar - ${exercicio.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('exercicios.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Editar Exercício</h2>
            </div>
        </template>

        <div class="">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="nome" value="Nome do Exercício *" />
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
                            <InputLabel for="categoria_id" value="Categoria (Opcional)" />
                            <select
                                id="categoria_id"
                                v-model="form.categoria_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            >
                                <option value="">-- Sem Categoria --</option>
                                <option
                                    v-for="categoria in categorias"
                                    :key="categoria.id"
                                    :value="categoria.id"
                                >
                                    {{ categoria.nome }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.categoria_id" />
                        </div>

                        <div>
                            <InputLabel for="paciente_id" value="Paciente Destinatário (Opcional)" />
                            <select
                                id="paciente_id"
                                v-model="form.paciente_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            >
                                <option value="">-- Exercício Geral (Toda a clínica) --</option>
                                <option
                                    v-for="paciente in pacientes"
                                    :key="paciente.id"
                                    :value="paciente.id"
                                >
                                    {{ paciente.nome }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.paciente_id" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="descricao" value="Descrição e Roteiro de Execução" />
                        <textarea
                            id="descricao"
                            rows="5"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            v-model="form.descricao"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.descricao" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('exercicios.index')"
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