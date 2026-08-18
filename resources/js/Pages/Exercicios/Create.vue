<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {
    ArrowLeftIcon,
    SparklesIcon,
    ArrowPathIcon,
    XMarkIcon,
    CheckIcon,
    UserIcon,
    DocumentTextIcon,
    ChatBubbleBottomCenterTextIcon,
    BookOpenIcon,
    ListBulletIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pacientes: {
        type: Array,
        default: () => [],
    },
    categorias: {
        type: Array,
        default: () => [],
    },
    selected_paciente_id: {
        type: [String, Number],
        default: '',
    },
});

const form = useForm({
    nome: '',
    categoria_id: '',
    paciente_id: props.selected_paciente_id || '',
    descricao: '',
});

const tiposConteudo = [
    { id: 'frases', label: 'Frases', icon: ChatBubbleBottomCenterTextIcon },
    { id: 'trava_linguas', label: 'Trava-línguas', icon: DocumentTextIcon },
    { id: 'historinha', label: 'História Curta', icon: BookOpenIcon },
    { id: 'lista_palavras', label: 'Lista de Palavras', icon: ListBulletIcon },
];

const isModalOpen = ref(false);
const loadingIa = ref(false);
const erroIa = ref('');
const respostaGerada = ref('');

const modalPrompt = ref({
    objetivo: '',
    idade: '',
    diagnostico: '',
    interesse: '',
    tipoConteudo: 'frases',
    categoriaNome: '',
});

const pacienteAtual = computed(() => {
    return props.pacientes.find((p) => String(p.id) === String(form.paciente_id)) || null;
});

const calcularIdade = (dataNasc) => {
    if (!dataNasc) return '';
    const cleanDate = dataNasc.substring(0, 10);
    const [ano, mes, dia] = cleanDate.split('-').map(Number);
    const hoje = new Date();
    let idade = hoje.getFullYear() - ano;
    const mesAtual = hoje.getMonth() + 1;
    const diaAtual = hoje.getDate();

    if (mesAtual < mes || (mesAtual === mes && diaAtual < dia)) {
        idade--;
    }
    return idade >= 0 ? idade : '';
};

const interessesDisponiveis = computed(() => {
    if (!pacienteAtual.value || !pacienteAtual.value.interesses) {
        return [];
    }
    if (Array.isArray(pacienteAtual.value.interesses)) {
        return pacienteAtual.value.interesses;
    }
    if (typeof pacienteAtual.value.interesses === 'string') {
        try {
            return JSON.parse(pacienteAtual.value.interesses);
        } catch {
            return [];
        }
    }
    return [];
});

const abrirModalIa = () => {
    const cat = props.categorias.find((c) => String(c.id) === String(form.categoria_id));

    modalPrompt.value = {
        objetivo: form.nome || '',
        idade: pacienteAtual.value ? calcularIdade(pacienteAtual.value.data_nascimento) : '',
        diagnostico: pacienteAtual.value?.diagnostico || '',
        interesse: '',
        tipoConteudo: 'frases',
        categoriaNome: cat ? cat.nome : '',
    };

    respostaGerada.value = '';
    erroIa.value = '';
    isModalOpen.value = true;
};

const fecharModal = () => {
    isModalOpen.value = false;
    loadingIa.value = false;
};

const selecionarInteresse = (item) => {
    modalPrompt.value.interesse = modalPrompt.value.interesse === item ? '' : item;
};

const gerarComIa = async () => {
    if (!modalPrompt.value.objetivo.trim()) {
        erroIa.value = 'Informe o objetivo do exercício antes de gerar.';
        return;
    }

    loadingIa.value = true;
    erroIa.value = '';

    try {
        const response = await axios.post(route('exercicios.gerar-ia'), {
            objetivo: modalPrompt.value.objetivo,
            paciente_id: form.paciente_id || null,
            categoria_id: form.categoria_id || null,
            idade: modalPrompt.value.idade || null,
            diagnostico: modalPrompt.value.diagnostico || null,
            interesse: modalPrompt.value.interesse || null,
            tipo_conteudo: modalPrompt.value.tipoConteudo,
        });

        if (response.data.sucesso && response.data.descricao) {
            respostaGerada.value = response.data.descricao;
        } else {
            erroIa.value = 'Não foi possível gerar o exercício.';
        }
    } catch (error) {
        erroIa.value = error.response?.data?.mensagem || 'Erro ao comunicar com a Inteligência Artificial.';
    } finally {
        loadingIa.value = false;
    }
};

const aceitarSugestao = () => {
    if (respostaGerada.value) {
        form.descricao = respostaGerada.value;
        if (!form.nome && modalPrompt.value.objetivo) {
            form.nome = modalPrompt.value.objetivo;
        }
    }
    fecharModal();
};

const submit = () => {
    form.post(route('exercicios.store'));
};
</script>

<template>
    <Head title="Novo Exercício" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('exercicios.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Novo Exercício</h2>
            </div>
        </template>

        <div class="">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nome do Exercício -->
                    <div>
                        <InputLabel for="nome" value="Nome do Exercício *" />
                        <TextInput
                            id="nome"
                            type="text"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.nome"
                            required
                            autofocus
                            placeholder="Ex: Fonema /r/ vibrante"
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
                        <div class="flex items-center justify-between">
                            <InputLabel for="descricao" value="Descrição e Roteiro de Execução *" />
                            <button
                                type="button"
                                @click="abrirModalIa"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50/80 px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-100 hover:text-indigo-800"
                            >
                                <SparklesIcon class="h-4 w-4 text-indigo-600" />
                                Gerar com Assistente IA
                            </button>
                        </div>

                        <textarea
                            id="descricao"
                            rows="6"
                            class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-sans"
                            v-model="form.descricao"
                            required
                            placeholder="Descreva o roteiro da atividade ou clique em 'Gerar com Assistente IA'..."
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
                            Cadastrar Exercício
                        </PrimaryButton>
                    </div>
                </form>
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
                    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" />
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
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 text-white shadow-md">
                                        <SparklesIcon class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <DialogTitle class="text-base font-bold text-gray-900">
                                            Assistente de Criação por IA
                                        </DialogTitle>
                                        <p class="text-xs text-gray-500">
                                            Selecione o formato e os parâmetros do texto terapêutico.
                                        </p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="fecharModal"
                                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                                >
                                    <XMarkIcon class="h-5 w-5" />
                                </button>
                            </div>

                            <div v-if="pacienteAtual" class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-3 text-xs text-blue-900">
                                <UserIcon class="h-5 w-5 text-blue-600 shrink-0" />
                                <div>
                                    <span class="font-bold">{{ pacienteAtual.nome }}</span>
                                    <span v-if="modalPrompt.idade"> • {{ modalPrompt.idade }} anos</span>
                                    <span v-if="modalPrompt.diagnostico"> • {{ modalPrompt.diagnostico }}</span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <InputLabel value="Objetivo Terapêutico / Alvo *" />
                                    <TextInput
                                        type="text"
                                        class="mt-1 block w-full rounded-xl text-sm"
                                        v-model="modalPrompt.objetivo"
                                        placeholder="Ex: Treino do fonema /r/ vibrante"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Formato do Conteúdo Texto *" />
                                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                        <button
                                            v-for="tipo in tiposConteudo"
                                            :key="tipo.id"
                                            type="button"
                                            @click="modalPrompt.tipoConteudo = tipo.id"
                                            :class="[
                                                modalPrompt.tipoConteudo === tipo.id
                                                    ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-500/20 font-bold'
                                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 font-medium',
                                                'flex flex-col items-center justify-center gap-1.5 rounded-xl border p-2.5 text-xs transition'
                                            ]"
                                        >
                                            <component :is="tipo.icon" class="h-4 w-4" />
                                            <span>{{ tipo.label }}</span>
                                        </button>
                                    </div>
                                </div>

                                <div v-if="!pacienteAtual" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel value="Idade do Paciente (Opcional)" />
                                        <TextInput
                                            type="number"
                                            class="mt-1 block w-full rounded-xl text-sm"
                                            v-model="modalPrompt.idade"
                                            placeholder="Ex: 6"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Diagnóstico / Foco (Opcional)" />
                                        <TextInput
                                            type="text"
                                            class="mt-1 block w-full rounded-xl text-sm"
                                            v-model="modalPrompt.diagnostico"
                                            placeholder="Ex: Ceceio anterior"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel value="Tema de Interesse Lúdico (Opcional)" />

                                    <div v-if="interessesDisponiveis.length > 0" class="mt-2 space-y-1.5">
                                        <span class="text-[11px] font-semibold text-gray-600 block">
                                            Interesses do prontuário:
                                        </span>
                                        <div class="flex flex-wrap gap-1.5">
                                            <button
                                                v-for="interesse in interessesDisponiveis"
                                                :key="interesse"
                                                type="button"
                                                @click="selecionarInteresse(interesse)"
                                                :class="[
                                                    modalPrompt.interesse === interesse
                                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                                                        : 'bg-white text-gray-700 border-indigo-200 hover:bg-indigo-50',
                                                    'inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-semibold transition'
                                                ]"
                                            >
                                                <CheckIcon v-if="modalPrompt.interesse === interesse" class="h-3.5 w-3.5" />
                                                {{ interesse }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <TextInput
                                            type="text"
                                            class="block w-full rounded-xl text-sm"
                                            v-model="modalPrompt.interesse"
                                            placeholder="Ou digite outro tema (ex: Futebol, Dinossauros)..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button
                                    type="button"
                                    @click="gerarComIa"
                                    :disabled="loadingIa"
                                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition disabled:opacity-50"
                                >
                                    <ArrowPathIcon v-if="loadingIa" class="h-4 w-4 animate-spin" />
                                    <SparklesIcon v-else class="h-4 w-4" />
                                    {{ loadingIa ? 'Gerando Conteúdo...' : (respostaGerada ? 'Gerar Novamente' : 'Gerar com IA') }}
                                </button>
                            </div>

                            <p v-if="erroIa" class="text-xs font-medium text-red-600">
                                {{ erroIa }}
                            </p>

                            <div v-if="respostaGerada" class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-900 flex items-center gap-1.5">
                                        <CheckIcon class="h-4 w-4 text-emerald-600" />
                                        Resultado da IA:
                                    </span>
                                    <span class="text-[11px] text-gray-500">Revise antes de aceitar</span>
                                </div>

                                <div class="rounded-xl bg-white p-3.5 border border-indigo-100 text-xs text-gray-800 whitespace-pre-line leading-relaxed max-h-60 overflow-y-auto font-sans shadow-inner">
                                    {{ respostaGerada }}
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        @click="respostaGerada = ''"
                                        class="rounded-xl px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition"
                                    >
                                        Descartar
                                    </button>
                                    <button
                                        type="button"
                                        @click="aceitarSugestao"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition"
                                    >
                                        <CheckIcon class="h-4 w-4" />
                                        Aceitar e Preencher
                                    </button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>
