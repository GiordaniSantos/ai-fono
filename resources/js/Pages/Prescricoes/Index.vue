<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/alert';
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    ClipboardDocumentListIcon,
    UserIcon,
    TagIcon,
    CalendarIcon,
    ClockIcon,
    FunnelIcon,
    XMarkIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
import { CheckCircleIcon as CheckCircleSolid } from '@heroicons/vue/24/solid';

const props = defineProps({
    prescricoes: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const vigenciaFilter = ref('vigentes');
const execucaoFilter = ref('all');
const pacienteFilter = ref('all');
const activeModalImage = ref(null);

const pacientesDisponiveis = computed(() => {
    const map = new Map();
    props.prescricoes.forEach((p) => {
        if (p.paciente) map.set(p.paciente.id, p.paciente.nome);
    });
    return Array.from(map, ([id, nome]) => ({ id, nome })).sort((a, b) =>
        a.nome.localeCompare(b.nome)
    );
});

const filteredPrescricoes = computed(() => {
    return props.prescricoes.filter((p) => {
        if (vigenciaFilter.value === 'vigentes' && p.status_vigencia !== 'vigente') return false;
        if (vigenciaFilter.value === 'futuras' && p.status_vigencia !== 'futura') return false;
        if (vigenciaFilter.value === 'expiradas' && p.status_vigencia !== 'expirada') return false;

        if (execucaoFilter.value === 'feitas' && !p.realizada) return false;
        if (execucaoFilter.value === 'pendentes' && p.realizada) return false;

        if (pacienteFilter.value !== 'all' && String(p.paciente_id) !== String(pacienteFilter.value)) {
            return false;
        }

        if (search.value) {
            const term = search.value.toLowerCase();
            const matchesPaciente = p.paciente?.nome.toLowerCase().includes(term);
            const matchesExercicio = p.exercicio?.nome.toLowerCase().includes(term);
            const matchesCategoria = p.exercicio?.categoria?.nome.toLowerCase().includes(term);

            if (!matchesPaciente && !matchesExercicio && !matchesCategoria) {
                return false;
            }
        }

        return true;
    });
});

const hasActiveFilters = computed(() => {
    return (
        search.value !== '' ||
        vigenciaFilter.value !== 'vigentes' ||
        execucaoFilter.value !== 'all' ||
        pacienteFilter.value !== 'all'
    );
});

const clearFilters = () => {
    search.value = '';
    vigenciaFilter.value = 'vigentes';
    execucaoFilter.value = 'all';
    pacienteFilter.value = 'all';
};

const toggleRealizada = (prescricao) => {
    router.patch(
        route('prescricoes.toggle-realizada', prescricao.id),
        {},
        { preserveScroll: true }
    );
};

const deletePrescricao = async (prescricao) => {
    const { isConfirmed } = await confirmAction({
        title: 'Remover prescrição?',
        text: `A prescrição do exercício "${prescricao.exercicio?.nome}" para ${prescricao.paciente?.nome} será removida.`,
        confirmButtonText: 'Sim, excluir',
    });

    if (isConfirmed) {
        router.delete(route('prescricoes.destroy', prescricao.id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.substring(0, 10).split('-');
    return `${day}/${month}/${year}`;
};
</script>

<template>
    <Head title="Prescrições de Exercícios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Prescrições Clínicas</h2>
                    <p class="text-sm text-gray-500">
                        Acompanhe o cumprimento das atividades diárias e a vigência dos treinos.
                    </p>
                </div>

                <Link
                    :href="route('prescricoes.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Nova Prescrição
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-wrap items-center gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar paciente ou exercício..."
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div class="relative w-full sm:w-44">
                        <select
                            v-model="vigenciaFilter"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="all">Todas as Vigências</option>
                            <option value="vigentes">Apenas Vigentes (Ativas)</option>
                            <option value="futuras">Agendadas (Futuras)</option>
                            <option value="expiradas">Expiradas / Encerradas</option>
                        </select>
                    </div>

                    <div class="relative w-full sm:w-44">
                        <select
                            v-model="execucaoFilter"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="all">Status de Hoje: Todos</option>
                            <option value="feitas">Feitas Hoje</option>
                            <option value="pendentes">Pendentes Hoje</option>
                        </select>
                    </div>

                    <div class="relative w-full sm:w-48">
                        <select
                            v-model="pacienteFilter"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="all">Todos os Pacientes</option>
                            <option
                                v-for="paciente in pacientesDisponiveis"
                                :key="paciente.id"
                                :value="paciente.id"
                            >
                                {{ paciente.nome }}
                            </option>
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
                    Exibindo <span class="font-bold text-gray-800">{{ filteredPrescricoes.length }}</span> de {{ prescricoes.length }}
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div v-if="filteredPrescricoes.length === 0" class="py-12 text-center">
                    <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhuma prescrição encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <span v-if="hasActiveFilters">Tente alterar os filtros selecionados acima.</span>
                        <span v-else>Crie um plano de treino para os seus pacientes.</span>
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Paciente</th>
                                <th class="px-6 py-4">Exercício Prescrito</th>
                                <th class="px-6 py-4">Vigência do Plano</th>
                                <th class="px-6 py-4">Frequência</th>
                                <th class="px-6 py-4 text-center">Execução Hoje</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="p in filteredPrescricoes" :key="p.id" class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button
                                            v-if="p.paciente?.foto_url"
                                            type="button"
                                            @click="activeModalImage = p.paciente.foto_url"
                                            class="group relative h-9 w-9 shrink-0 overflow-hidden rounded-full border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            title="Ver foto do paciente"
                                        >
                                            <img
                                                :src="p.paciente.foto_url"
                                                :alt="p.paciente.nome"
                                                class="h-full w-full object-cover transition group-hover:scale-110"
                                            />
                                        </button>
                                        <div
                                            v-else
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 border border-slate-200 text-slate-400"
                                        >
                                            <UserIcon class="h-4.5 w-4.5 text-slate-400" />
                                        </div>

                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 truncate">
                                                {{ p.paciente?.nome || 'Paciente não identificado' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ p.exercicio?.nome }}
                                    </div>
                                    <div v-if="p.exercicio?.categoria" class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 border border-indigo-100">
                                            <TagIcon class="h-3 w-3" />
                                            {{ p.exercicio.categoria.nome }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span
                                            v-if="p.status_vigencia === 'vigente'"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Vigente
                                        </span>
                                        <span
                                            v-else-if="p.status_vigencia === 'futura'"
                                            class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-bold text-sky-700 border border-sky-200"
                                        >
                                            Inicia em {{ formatDate(p.data_inicio) }}
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-bold text-gray-600 border border-gray-200"
                                        >
                                            Expirada em {{ formatDate(p.data_fim) }}
                                        </span>

                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                            <CalendarIcon class="h-3.5 w-3.5 text-gray-400" />
                                            <span>{{ formatDate(p.data_inicio) }}</span>
                                            <span>até</span>
                                            <span v-if="p.data_fim">{{ formatDate(p.data_fim) }}</span>
                                            <span v-else class="italic">Contínuo</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 border border-amber-200">
                                        <ClockIcon class="h-3.5 w-3.5" />
                                        {{ p.frequencia_diaria }}x ao dia
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="toggleRealizada(p)"
                                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold shadow-sm transition"
                                        :class="[
                                            p.realizada
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100'
                                                : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200'
                                        ]"
                                        title="Clique para alternar o status de hoje"
                                    >
                                        <CheckCircleSolid v-if="p.realizada" class="h-4 w-4 text-emerald-600" />
                                        <XCircleIcon v-else class="h-4 w-4 text-slate-400" />
                                        {{ p.realizada ? 'Feito Hoje' : 'Pendente Hoje' }}
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('prescricoes.edit', p.id)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition"
                                            title="Editar Prescrição"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deletePrescricao(p)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 transition"
                                            title="Excluir Prescrição"
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

        <TransitionRoot as="template" :show="!!activeModalImage">
            <Dialog as="div" class="relative z-50" @close="activeModalImage = null">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="relative mx-auto max-w-lg overflow-hidden rounded-2xl bg-white p-4 shadow-2xl">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <h3 class="text-sm font-semibold text-gray-900">Foto do Paciente</h3>
                                <div class="flex items-center gap-3">
                                    <a
                                        :href="activeModalImage"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline"
                                    >
                                        <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                                        Abrir original
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                        @click="activeModalImage = null"
                                    >
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-center bg-gray-900 rounded-xl overflow-hidden p-2">
                                <img
                                    :src="activeModalImage"
                                    alt="Foto do Paciente Ampliada"
                                    class="max-h-[60vh] w-auto object-contain rounded-lg"
                                />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>
