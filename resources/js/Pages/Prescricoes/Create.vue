<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    ArrowLeftIcon,
    ClockIcon,
    CalendarIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pacientes: {
        type: Array,
        default: () => [],
    },
    exercicios: {
        type: Array,
        default: () => [],
    },
    selected_paciente_id: {
        type: [String, Number],
        default: '',
    },
    selected_exercicio_id: {
        type: [String, Number],
        default: '',
    },
});

const hoje = new Date().toISOString().substring(0, 10);

const form = useForm({
    paciente_id: props.selected_paciente_id || '',
    exercicio_id: props.selected_exercicio_id || '',
    data_inicio: hoje,
    data_fim: '',
    frequencia_diaria: 1,
});

const exerciciosFiltrados = computed(() => {
    if (!form.paciente_id) {
        return props.exercicios;
    }
    return props.exercicios.filter(
        (ex) => ex.paciente_id === null || String(ex.paciente_id) === String(form.paciente_id)
    );
});

const frequenciasRapidas = [1, 2, 3, 4, 5];

const submit = () => {
    form.post(route('prescricoes.store'));
};
</script>

<template>
    <Head title="Nova Prescrição" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('prescricoes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Prescrever Exercício</h2>
            </div>
        </template>

        <div class="">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="paciente_id" value="Paciente Destinatário *" />
                        <select
                            id="paciente_id"
                            v-model="form.paciente_id"
                            required
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="" disabled>-- Selecione o Paciente --</option>
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

                    <div>
                        <InputLabel for="exercicio_id" value="Exercício da Biblioteca *" />
                        <select
                            id="exercicio_id"
                            v-model="form.exercicio_id"
                            required
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="" disabled>-- Selecione o Exercício --</option>
                            <option
                                v-for="exercicio in exerciciosFiltrados"
                                :key="exercicio.id"
                                :value="exercicio.id"
                            >
                                {{ exercicio.nome }}
                                {{ exercicio.categoria ? ` (${exercicio.categoria.nome})` : '' }}
                                {{ exercicio.paciente_id ? ' [Exclusivo do paciente]' : ' [Geral]' }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.exercicio_id" />
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <CalendarIcon class="h-4 w-4" />
                            Período de Vigência no Aplicativo
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="data_inicio" value="Data de Início *" />
                                <TextInput
                                    id="data_inicio"
                                    type="date"
                                    class="mt-1 block w-full rounded-xl"
                                    v-model="form.data_inicio"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.data_inicio" />
                            </div>

                            <div>
                                <InputLabel for="data_fim" value="Data Final da Vigência (Opcional)" />
                                <TextInput
                                    id="data_fim"
                                    type="date"
                                    class="mt-1 block w-full rounded-xl"
                                    v-model="form.data_fim"
                                    :min="form.data_inicio"
                                />
                                <p class="mt-1 text-xs text-gray-500">Deixe em branco se a prescrição for contínua.</p>
                                <InputError class="mt-2" :message="form.errors.data_fim" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5 space-y-3">
                        <div class="flex items-center gap-2">
                            <ClockIcon class="h-5 w-5 text-amber-600" />
                            <div>
                                <h3 class="text-sm font-bold text-amber-950">Meta de Execução Diária</h3>
                                <p class="text-xs text-amber-800">
                                    O paciente receberá esta meta diária no app. Ao virar o dia, a tarefa é resetada para o próximo treino.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <button
                                v-for="freq in frequenciasRapidas"
                                :key="freq"
                                type="button"
                                @click="form.frequencia_diaria = freq"
                                :class="[
                                    form.frequencia_diaria === freq
                                        ? 'bg-amber-600 text-white border-amber-600 shadow-sm font-bold'
                                        : 'bg-white text-gray-700 border-amber-200 hover:bg-amber-100 font-medium',
                                    'rounded-xl border px-4 py-2 text-xs transition'
                                ]"
                            >
                                {{ freq }}x ao dia
                            </button>

                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-xs text-amber-900 font-semibold">Outro valor:</span>
                                <TextInput
                                    type="number"
                                    min="1"
                                    max="20"
                                    class="w-20 rounded-xl bg-white text-center text-sm"
                                    v-model="form.frequencia_diaria"
                                />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.frequencia_diaria" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('prescricoes.index')"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition"
                        >
                            Cancelar
                        </Link>
                        <PrimaryButton
                            class="!rounded-xl !bg-blue-600 !px-6 !py-2.5 !font-semibold hover:!bg-blue-700"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Criar Prescrição
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
