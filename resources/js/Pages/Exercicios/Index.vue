<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/alert';
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    SparklesIcon,
    UserIcon,
    GlobeAltIcon,
    FunnelIcon,
    TagIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    exercicios: {
        type: Array,
        default: () => [],
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

const search = ref('');
const selectedPaciente = ref('all');
const selectedCategoria = ref('all');

const filteredExercicios = computed(() => {
    return props.exercicios.filter((exercicio) => {
        if (selectedPaciente.value === 'general') {
            if (exercicio.paciente_id !== null) return false;
        } else if (selectedPaciente.value !== 'all') {
            if (String(exercicio.paciente_id) !== String(selectedPaciente.value)) return false;
        }

        if (selectedCategoria.value !== 'all') {
            if (String(exercicio.categoria_id) !== String(selectedCategoria.value)) return false;
        }

        if (search.value) {
            const term = search.value.toLowerCase();
            const matchesName = exercicio.nome.toLowerCase().includes(term);
            const matchesDesc = exercicio.descricao && exercicio.descricao.toLowerCase().includes(term);
            const matchesPaciente = exercicio.paciente && exercicio.paciente.nome.toLowerCase().includes(term);
            const matchesCategoria = exercicio.categoria && exercicio.categoria.nome.toLowerCase().includes(term);

            if (!matchesName && !matchesDesc && !matchesPaciente && !matchesCategoria) {
                return false;
            }
        }

        return true;
    });
});

const clearFilters = () => {
    search.value = '';
    selectedPaciente.value = 'all';
    selectedCategoria.value = 'all';
};

const hasActiveFilters = computed(() => {
    return search.value !== '' || selectedPaciente.value !== 'all' || selectedCategoria.value !== 'all';
});

const deleteExercicio = async (exercicio) => {
    const { isConfirmed } = await confirmAction({
        title: `Remover "${exercicio.nome}"?`,
        text: 'O exercício será excluído permanentemente da sua biblioteca.',
        confirmButtonText: 'Sim, excluir',
    });

    if (isConfirmed) {
        router.delete(route('exercicios.destroy', exercicio.id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.split('T')[0].split('-');
    return `${day}/${month}/${year}`;
};
</script>

<template>
    <Head title="Exercícios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Biblioteca de Exercícios</h2>
                    <p class="text-sm text-gray-500">Cadastre e gerencie os exercícios gerais ou direcionados aos seus pacientes.</p>
                </div>

                <Link
                    :href="route('exercicios.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Novo Exercício
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar por título ou descrição..."
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div class="relative w-full sm:max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <TagIcon class="h-4 w-4 text-gray-400" />
                        </div>
                        <select
                            v-model="selectedCategoria"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-8 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="all">Todas as Categorias</option>
                            <option
                                v-for="cat in categorias"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.nome }}
                            </option>
                        </select>
                    </div>

                    <div class="relative w-full sm:max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <FunnelIcon class="h-4 w-4 text-gray-400" />
                        </div>
                        <select
                            v-model="selectedPaciente"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-8 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="all">Todos os Destinatários</option>
                            <option value="general">Apenas Biblioteca Geral</option>
                            <optgroup v-if="pacientes.length > 0" label="Pacientes Específicos">
                                <option
                                    v-for="paciente in pacientes"
                                    :key="paciente.id"
                                    :value="paciente.id"
                                >
                                    {{ paciente.nome }}
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="clearFilters"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-800 transition"
                    >
                        <XMarkIcon class="h-4 w-4" />
                        Limpar filtros
                    </button>
                </div>

                <div class="text-xs font-medium text-gray-500">
                    Exibindo <span class="font-bold text-gray-800">{{ filteredExercicios.length }}</span> de {{ exercicios.length }} exercícios
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div v-if="filteredExercicios.length === 0" class="py-12 text-center">
                    <SparklesIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum exercício encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <span v-if="hasActiveFilters">Tente alterar os termos da busca ou os filtros aplicados.</span>
                        <span v-else>Comece cadastrando um novo exercício para sua clínica.</span>
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Exercício</th>
                                <th class="px-6 py-4">Categoria</th>
                                <th class="px-6 py-4">Destinatário</th>
                                <th class="px-6 py-4">Data de Cadastro</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="exercicio in filteredExercicios" :key="exercicio.id" class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ exercicio.nome }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-sm mt-0.5">
                                        {{ exercicio.descricao || 'Sem descrição informada' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        v-if="exercicio.categoria"
                                        class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700"
                                    >
                                        {{ exercicio.categoria.nome }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400 italic">
                                        Sem categoria
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        v-if="exercicio.paciente"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-purple-200 bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700"
                                    >
                                        <UserIcon class="h-3.5 w-3.5" />
                                        {{ exercicio.paciente.nome }}
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"
                                    >
                                        <GlobeAltIcon class="h-3.5 w-3.5" />
                                        Biblioteca Geral (Todos)
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ formatDate(exercicio.created_at) }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('exercicios.edit', exercicio.id)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition"
                                            title="Editar"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button
                                            @click="deleteExercicio(exercicio)"
                                            type="button"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 transition"
                                            title="Excluir"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>