<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {
    MagnifyingGlassIcon,
    UserIcon,
    CalendarIcon,
    ChatBubbleBottomCenterTextIcon,
    SparklesIcon,
    XMarkIcon,
    PaperAirplaneIcon,
    TagIcon,
    MusicalNoteIcon,
    VideoCameraIcon,
    ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
import {
    StarIcon as StarIconSolid,
    CheckCircleIcon as CheckCircleSolid,
} from '@heroicons/vue/24/solid';

const props = defineProps({
    execucoes: {
        type: Array,
        default: () => [],
    },
    pacientes: {
        type: Array,
        default: () => [],
    },
    metricas: {
        type: Object,
        default: () => ({
            total_execucoes: 0,
            com_midia: 0,
            com_feedback: 0,
            media_satisfacao: 0,
            grafico_semanal: [],
        }),
    },
    filtros: {
        type: Object,
        default: () => ({
            paciente_id: '',
            data_inicio: '',
            data_fim: '',
        }),
    },
});

const search = ref('');
const selectedPaciente = ref(props.filtros.paciente_id || '');
const dataInicio = ref(props.filtros.data_inicio || '');
const dataFim = ref(props.filtros.data_fim || '');
const apenasComMidia = ref(false);

const isModalOpen = ref(false);
const selectedExecucao = ref(null);
const feedbackTexto = ref('');
const enviandoFeedback = ref(false);

const pacienteAtual = computed(() => {
    return props.pacientes.find((p) => String(p.id) === String(selectedPaciente.value)) || null;
});

const aplicarFiltros = () => {
    router.get(
        route('execucoes.index'),
        {
            paciente_id: selectedPaciente.value || undefined,
            data_inicio: dataInicio.value || undefined,
            data_fim: dataFim.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

watch([selectedPaciente, dataInicio, dataFim], () => {
    aplicarFiltros();
});

const clearFilters = () => {
    search.value = '';
    selectedPaciente.value = '';
    dataInicio.value = '';
    dataFim.value = '';
    apenasComMidia.value = false;
    aplicarFiltros();
};

const hasActiveFilters = computed(() => {
    return (
        search.value !== '' ||
        selectedPaciente.value !== '' ||
        dataInicio.value !== '' ||
        dataFim.value !== '' ||
        apenasComMidia.value
    );
});

const filteredExecucoes = computed(() => {
    return props.execucoes.filter((execucao) => {
        if (apenasComMidia.value && !execucao.arquivo_url) return false;

        if (search.value) {
            const term = search.value.toLowerCase();
            const matchesPaciente = execucao.paciente?.nome.toLowerCase().includes(term);
            const matchesExercicio = execucao.prescricao?.exercicio?.nome.toLowerCase().includes(term);
            const matchesCategoria = execucao.prescricao?.exercicio?.categoria?.nome.toLowerCase().includes(term);

            if (!matchesPaciente && !matchesExercicio && !matchesCategoria) {
                return false;
            }
        }

        return true;
    });
});

const maxDiaTotal = computed(() => {
    if (!props.metricas.grafico_semanal || props.metricas.grafico_semanal.length === 0) return 1;
    const max = Math.max(...props.metricas.grafico_semanal.map((d) => d.total));
    return max > 0 ? max : 1;
});

const abrirModal = (execucao) => {
    selectedExecucao.value = execucao;
    feedbackTexto.value = execucao.comentario_fono || '';
    isModalOpen.value = true;
};

const fecharModal = () => {
    isModalOpen.value = false;
};

const salvarFeedback = () => {
    if (!selectedExecucao.value) return;

    enviandoFeedback.value = true;
    router.patch(
        route('execucoes.comentario', selectedExecucao.value.id),
        {
            comentario_fono: feedbackTexto.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                if (selectedExecucao.value) {
                    selectedExecucao.value.comentario_fono = feedbackTexto.value;
                }
                fecharModal();
            },
            onFinish: () => {
                enviandoFeedback.value = false;
            },
        }
    );
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.substring(0, 10).split('-');
    return `${day}/${month}/${year}`;
};

const getSatisfacaoLabel = (nivel) => {
    const labels = {
        1: { text: 'Muito Difícil', color: 'text-red-600 bg-red-50 border-red-200' },
        2: { text: 'Difícil', color: 'text-orange-600 bg-orange-50 border-orange-200' },
        3: { text: 'Regular / Neutro', color: 'text-amber-600 bg-amber-50 border-amber-200' },
        4: { text: 'Fácil', color: 'text-blue-600 bg-blue-50 border-blue-200' },
        5: { text: 'Muito Fácil / Excelente', color: 'text-emerald-600 bg-emerald-50 border-emerald-200' },
    };
    return labels[nivel] || { text: 'Não avaliado', color: 'text-gray-500 bg-gray-50 border-gray-200' };
};
</script>

<template>
    <Head title="Histórico de Execuções e Treinos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Histórico de Treinos & Gravações</h2>
                    <p class="text-sm text-gray-500">
                        Avalie as gravações em áudio/vídeo enviadas pelos pacientes e forneça feedbacks clínicos.
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                            Filtrar por Paciente
                        </label>
                        <select
                            v-model="selectedPaciente"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">Todos os Pacientes (Geral)</option>
                            <option
                                v-for="paciente in pacientes"
                                :key="paciente.id"
                                :value="paciente.id"
                            >
                                {{ paciente.nome }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                            A partir de:
                        </label>
                        <input
                            type="date"
                            v-model="dataInicio"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                            Até:
                        </label>
                        <input
                            type="date"
                            v-model="dataFim"
                            class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                            Busca Rápida
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Exercício ou categoria..."
                                class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-gray-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 select-none">
                        <input
                            type="checkbox"
                            v-model="apenasComMidia"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        Exibir apenas treinos com gravações (Áudio / Vídeo)
                    </label>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="clearFilters"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-gray-800 transition"
                    >
                        <XMarkIcon class="h-4 w-4" />
                        Limpar todos os filtros
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-blue-100 bg-gradient-to-tr from-blue-50 to-indigo-50/40 p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-900">
                                {{ pacienteAtual ? `Treinos de ${pacienteAtual.nome}` : 'Total de Treinos Realizados' }}
                            </span>
                            <SparklesIcon class="h-5 w-5 text-blue-600" />
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-blue-950">{{ metricas.total_execucoes }}</span>
                            <span class="text-xs text-blue-700 font-medium">sessões registradas</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 block">Satisfação Média</span>
                            <div class="mt-2 flex items-center gap-1.5">
                                <span class="text-2xl font-bold text-gray-900">{{ metricas.media_satisfacao }}</span>
                                <div class="flex text-amber-400">
                                    <StarIconSolid class="h-4 w-4" />
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-400">Escala de 1 a 5</span>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 block">Com Gravações</span>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-indigo-600">{{ metricas.com_midia }}</span>
                                <span class="text-xs text-gray-400">/ {{ metricas.total_execucoes }}</span>
                            </div>
                            <span class="text-[10px] text-gray-400">{{ metricas.com_feedback }} avaliados</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Frequência Semanal de Treinos</h3>
                            <p class="text-xs text-gray-500">Distribuição dos exercícios praticados ao longo da semana.</p>
                        </div>
                        <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 border border-indigo-100">
                            Segunda a Domingo
                        </span>
                    </div>

                    <div class="grid grid-cols-7 gap-3 pt-6 items-end h-45">
                        <div
                            v-for="dia in metricas.grafico_semanal"
                            :key="dia.nome"
                            class="flex flex-col items-center gap-2 h-full justify-end group"
                        >
                            <span class="text-[11px] font-bold text-gray-600 transition group-hover:text-blue-600">
                                {{ dia.total }}
                            </span>
                            <div class="w-full max-w-[36px] bg-slate-100 rounded-xl overflow-hidden flex flex-col justify-end h-28 relative">
                                <div
                                    class="w-full bg-gradient-to-t from-blue-600 to-indigo-500 rounded-xl transition-all duration-500 group-hover:from-blue-700 group-hover:to-indigo-600"
                                    :style="{ height: `${(dia.total / maxDiaTotal) * 100}%` }"
                                ></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-600">
                                {{ dia.nome }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div v-if="filteredExecucoes.length === 0" class="py-16 text-center">
                    <ChatBubbleBottomCenterTextIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum treino encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        <span v-if="hasActiveFilters">Tente alterar os termos ou o paciente selecionado.</span>
                        <span v-else>As execuções de exercícios enviadas pelos pacientes aparecerão aqui.</span>
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Data do Treino</th>
                                <th class="px-6 py-4">Paciente</th>
                                <th class="px-6 py-4">Exercício Praticado</th>
                                <th class="px-6 py-4">Gravação</th>
                                <th class="px-6 py-4 text-center">Percepção</th>
                                <th class="px-6 py-4">Feedback Clínico</th>
                                <th class="px-6 py-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="e in filteredExecucoes" :key="e.id" class="hover:bg-gray-50/80 transition">
                                <!-- Data -->
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-700 font-semibold">
                                        <CalendarIcon class="h-4 w-4 text-gray-400" />
                                        {{ formatDate(e.data_execucao) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            v-if="e.paciente?.foto_url"
                                            class="h-8 w-8 rounded-full overflow-hidden border border-gray-200 shadow-sm shrink-0"
                                        >
                                            <img :src="e.paciente.foto_url" class="h-full w-full object-cover" />
                                        </div>
                                        <div
                                            v-else
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 border border-slate-200 text-slate-400 shrink-0"
                                        >
                                            <UserIcon class="h-4 w-4 text-slate-400" />
                                        </div>
                                        <span class="font-medium text-gray-900 truncate max-w-[150px]">
                                            {{ e.paciente?.nome }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 truncate max-w-xs">
                                        {{ e.prescricao?.exercicio?.nome }}
                                    </div>
                                    <div v-if="e.prescricao?.exercicio?.categoria" class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 border border-indigo-100">
                                            <TagIcon class="h-3 w-3" />
                                            {{ e.prescricao.exercicio.categoria.nome }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        v-if="e.arquivo_url"
                                        type="button"
                                        @click="abrirModal(e)"
                                        class="inline-flex items-center gap-1.5 rounded-xl border px-2.5 py-1.5 text-xs font-semibold shadow-sm transition hover:scale-105"
                                        :class="[
                                            e.tipo_arquivo === 'video'
                                                ? 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100'
                                                : 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100'
                                        ]"
                                    >
                                        <VideoCameraIcon v-if="e.tipo_arquivo === 'video'" class="h-4 w-4" />
                                        <MusicalNoteIcon v-else class="h-4 w-4" />
                                        {{ e.tipo_arquivo === 'video' ? 'Vídeo Gravado' : 'Áudio Gravado' }}
                                    </button>
                                    <span v-else class="text-xs text-gray-400 italic">Sem gravação</span>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-semibold"
                                        :class="getSatisfacaoLabel(e.nivel_satisfacao).color"
                                    >
                                        <StarIconSolid v-if="e.nivel_satisfacao > 0" class="h-3.5 w-3.5 text-amber-400" />
                                        {{ getSatisfacaoLabel(e.nivel_satisfacao).text }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 max-w-xs">
                                    <div v-if="e.comentario_fono" class="flex items-start gap-1.5">
                                        <CheckCircleSolid class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" />
                                        <p class="text-xs text-gray-700 truncate" :title="e.comentario_fono">
                                            {{ e.comentario_fono }}
                                        </p>
                                    </div>
                                    <span v-else class="text-xs text-amber-600 font-medium italic">
                                        Aguardando feedback
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button
                                        type="button"
                                        @click="abrirModal(e)"
                                        class="inline-flex items-center gap-1 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 border border-blue-200 shadow-sm hover:bg-blue-100 transition"
                                    >
                                        <ChatBubbleBottomCenterTextIcon class="h-4 w-4" />
                                        {{ e.comentario_fono ? 'Ver / Editar' : 'Avaliar' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <TransitionRoot as="template" :show="isModalOpen">
            <Dialog as="div" class="relative z-50" @close="fecharModal">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-10">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="relative mx-auto max-w-2xl overflow-hidden rounded-2xl bg-white p-6 shadow-2xl space-y-5">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <div>
                                    <DialogTitle class="text-base font-bold text-gray-900 flex items-center gap-2">
                                        <span>Avaliação do Treino</span>
                                        <span
                                            v-if="selectedExecucao?.arquivo_url"
                                            class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold"
                                            :class="[
                                                selectedExecucao?.tipo_arquivo === 'video'
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-teal-100 text-teal-700'
                                            ]"
                                        >
                                            <VideoCameraIcon v-if="selectedExecucao?.tipo_arquivo === 'video'" class="h-3.5 w-3.5" />
                                            <MusicalNoteIcon v-else class="h-3.5 w-3.5" />
                                            {{ selectedExecucao?.tipo_arquivo === 'video' ? 'Vídeo' : 'Áudio' }}
                                        </span>
                                    </DialogTitle>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Treino de <strong>{{ selectedExecucao?.paciente?.nome }}</strong> em {{ formatDate(selectedExecucao?.data_execucao) }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="fecharModal"
                                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                                >
                                    <XMarkIcon class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-3.5 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-900 text-sm">
                                        {{ selectedExecucao?.prescricao?.exercicio?.nome }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 font-semibold"
                                        :class="getSatisfacaoLabel(selectedExecucao?.nivel_satisfacao).color"
                                    >
                                        Autoavaliação: {{ getSatisfacaoLabel(selectedExecucao?.nivel_satisfacao).text }}
                                    </span>
                                </div>
                                <p v-if="selectedExecucao?.prescricao?.exercicio?.categoria" class="text-gray-500">
                                    Categoria: {{ selectedExecucao.prescricao.exercicio.categoria.nome }}
                                </p>
                            </div>

                            <div v-if="selectedExecucao?.arquivo_url" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                                        <VideoCameraIcon v-if="selectedExecucao.tipo_arquivo === 'video'" class="h-4 w-4 text-purple-600" />
                                        <MusicalNoteIcon v-else class="h-4 w-4 text-teal-600" />
                                        Reprodução da Gravação
                                    </span>

                                    <a
                                        :href="selectedExecucao.arquivo_url"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition"
                                    >
                                        <ArrowTopRightOnSquareIcon class="h-3.5 w-3.5" />
                                        Abrir original
                                    </a>
                                </div>

                                <div
                                    v-if="selectedExecucao.tipo_arquivo === 'video'"
                                    class="overflow-hidden rounded-2xl bg-black shadow-inner flex items-center justify-center max-h-80"
                                >
                                    <video
                                        :key="selectedExecucao.id"
                                        :src="selectedExecucao.arquivo_url"
                                        controls
                                        playsinline
                                        class="w-full max-h-80 object-contain rounded-2xl"
                                    >
                                        Seu navegador não suporta a tag de vídeo.
                                    </video>
                                </div>

                                <div
                                    v-else
                                    class="rounded-2xl border border-teal-200 bg-gradient-to-r from-teal-50 to-emerald-50/50 p-5 space-y-3 shadow-sm"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 text-white shadow-sm shrink-0">
                                            <MusicalNoteIcon class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-teal-950">Gravação de Voz</h4>
                                            <p class="text-xs text-teal-700">Ouça a execução e avalie a clareza e fonética do paciente.</p>
                                        </div>
                                    </div>
                                    <audio
                                        :key="selectedExecucao.id"
                                        :src="selectedExecucao.arquivo_url"
                                        controls
                                        class="w-full rounded-lg"
                                    >
                                        Seu navegador não suporta a tag de áudio.
                                    </audio>
                                </div>
                            </div>

                            <div
                                v-else
                                class="rounded-2xl border border-gray-200 border-dashed p-6 text-center text-xs text-gray-400 italic bg-gray-50/50"
                            >
                                Este treino foi marcado como realizado sem anexo de áudio ou vídeo.
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Feedback do Fonoaudiólogo (Visível no App do Paciente)
                                </label>
                                <textarea
                                    v-model="feedbackTexto"
                                    rows="4"
                                    class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 font-sans"
                                    placeholder="Escreva suas orientações, pontos de atenção ou elogios para o paciente..."
                                ></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-4">
                                <button
                                    type="button"
                                    @click="fecharModal"
                                    class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition"
                                >
                                    Fechar
                                </button>
                                <button
                                    type="button"
                                    @click="salvarFeedback"
                                    :disabled="enviandoFeedback"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition disabled:opacity-50"
                                >
                                    <PaperAirplaneIcon class="h-4 w-4" />
                                    {{ enviandoFeedback ? 'Salvando...' : 'Salvar Feedback' }}
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>