<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    ArrowLeftIcon,
    ClockIcon,
    CalendarIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    prescricao: {
        type: Object,
        required: true,
    },
    pacientes: {
        type: Array,
        default: () => [],
    },
    exercicios: {
        type: Array,
        default: () => [],
    },
});

const buscaExercicio = ref('');

const form = useForm({
    paciente_id: props.prescricao.paciente_id,
    exercicios: props.prescricao.exercicios ? props.prescricao.exercicios.map((ex) => ex.id) : [],
    data_inicio: props.prescricao.data_inicio ? props.prescricao.data_inicio.substring(0, 10) : '',
    data_fim: props.prescricao.data_fim ? props.prescricao.data_fim.substring(0, 10) : '',
    frequencia_diaria: props.prescricao.frequencia_diaria || 1,
    realizada: Boolean(props.prescricao.realizada),
});

const exerciciosFiltrados = computed(() => {
    let lista = props.exercicios;

    if (form.paciente_id) {
        lista = lista.filter(
            (ex) => ex.paciente_id === null || String(ex.paciente_id) === String(form.paciente_id)
        );
    }

    if (buscaExercicio.value.trim()) {
        const termo = buscaExercicio.value.toLowerCase();
        lista = lista.filter((ex) => {
            const matchesNome = ex.nome.toLowerCase().includes(termo);
            const matchesCategoria = ex.categoria?.nome.toLowerCase().includes(termo);
            return matchesNome || matchesCategoria;
        });
    }

    return lista;
});

const selecionarTodos = () => {
    form.exercicios = exerciciosFiltrados.value.map((e) => e.id);
};

const limparSelecao = () => {
    form.exercicios = [];
};

const frequenciasRapidas = [1, 2, 3, 4, 5];

const submit = () => {
    form.patch(route('prescricoes.update', props.prescricao.id));
};
</script>

<template>
    <Head title="Editar Prescrição" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('prescricoes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Editar Prescrição</h2>
            </div>
        </template>

        <div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="paciente_id" value="Paciente *" />
                        <select
                            id="paciente_id"
                            v-model="form.paciente_id"
                            required
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
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

                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <InputLabel value="Exercícios Prescritos (Múltipla Seleção) *" />
                                <p class="text-xs text-gray-500">
                                    Marque ou desmarque exercícios para ajustar esta prescrição.
                                </p>
                            </div>

                            <div class="flex items-center gap-2 text-xs">
                                <button
                                    type="button"
                                    @click="selecionarTodos"
                                    class="font-semibold text-blue-600 hover:underline"
                                >
                                    Marcar todos exibidos
                                </button>
                                <span class="text-gray-300">|</span>
                                <button
                                    type="button"
                                    @click="limparSelecao"
                                    class="font-semibold text-gray-500 hover:underline"
                                >
                                    Desmarcar todos
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
                            </div>
                            <input
                                v-model="buscaExercicio"
                                type="text"
                                placeholder="Filtrar exercícios pelo nome ou categoria..."
                                class="block w-full rounded-xl border border-gray-300 bg-white py-2 pl-9 pr-3 text-xs text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <div class="max-h-64 overflow-y-auto rounded-xl border border-gray-200 bg-white p-3 space-y-1.5 shadow-inner">
                            <div
                                v-if="exerciciosFiltrados.length === 0"
                                class="py-6 text-center text-xs text-gray-400 italic"
                            >
                                Nenhum exercício encontrado para os critérios selecionados.
                            </div>

                            <label
                                v-for="exercicio in exerciciosFiltrados"
                                :key="exercicio.id"
                                class="flex items-center justify-between gap-3 p-2.5 rounded-lg hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-200 transition"
                                :class="{ 'bg-blue-50/60 border-blue-100': form.exercicios.includes(exercicio.id) }"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <input
                                        type="checkbox"
                                        :value="exercicio.id"
                                        v-model="form.exercicios"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-gray-900 block truncate">
                                            {{ exercicio.nome }}
                                        </span>
                                        <span v-if="exercicio.categoria" class="text-xs text-gray-500">
                                            {{ exercicio.categoria.nome }}
                                        </span>
                                    </div>
                                </div>

                                <span
                                    class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md"
                                    :class="exercicio.paciente_id ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600'"
                                >
                                    {{ exercicio.paciente_id ? 'Exclusivo' : 'Geral' }}
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span>
                                Selecionados: <strong class="text-blue-600">{{ form.exercicios.length }}</strong> de {{ exercicios.length }} exercícios
                            </span>
                        </div>

                        <InputError :message="form.errors.exercicios" />
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
                                <p class="mt-1 text-xs text-gray-500">Deixe em branco para tempo indeterminado.</p>
                                <InputError class="mt-2" :message="form.errors.data_fim" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5 space-y-3">
                        <div class="flex items-center gap-2">
                            <ClockIcon class="h-5 w-5 text-amber-600" />
                            <div>
                                <h3 class="text-sm font-bold text-amber-950">Frequência de Execução Diária</h3>
                                <p class="text-xs text-amber-800">Quantas vezes ao dia o paciente deve praticar esta atividade?</p>
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

                    <div class="flex items-center gap-3 pt-2">
                        <input
                            id="realizada"
                            type="checkbox"
                            v-model="form.realizada"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <label for="realizada" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Marcar como executada <strong>no dia de hoje</strong>
                        </label>
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
                            :class="{ 'opacity-25': form.processing || form.exercicios.length === 0 }"
                            :disabled="form.processing || form.exercicios.length === 0"
                        >
                            Salvar Alterações
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>