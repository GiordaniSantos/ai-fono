<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    UsersIcon,
    SparklesIcon,
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    ArrowTrendingUpIcon,
    PlusIcon,
    ClockIcon,
    DevicePhoneMobileIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

// Estatísticas Fictícias
const stats = [
    {
        name: 'Pacientes Ativos',
        value: '38',
        change: '+4 este mês',
        changeType: 'positive',
        icon: UsersIcon,
        color: 'bg-blue-500',
    },
    {
        name: 'Exercícios Cadastrados',
        value: '124',
        change: 'Voz, Fala, Deglutição',
        changeType: 'neutral',
        icon: SparklesIcon,
        color: 'bg-indigo-500',
    },
    {
        name: 'Prescrições Ativas',
        value: '42',
        change: 'Sincronizadas com App',
        changeType: 'positive',
        icon: ClipboardDocumentListIcon,
        color: 'bg-sky-500',
    },
    {
        name: 'Taxa de Adesão / Treino',
        value: '84%',
        change: '+6.2% vs semana passada',
        changeType: 'positive',
        icon: CheckCircleIcon,
        color: 'bg-emerald-500',
    },
];

// Dados Fictícios de Adesão Semanal (Gráfico de Barras)
const weeklyAdherence = [
    { day: 'Seg', pct: 85, done: 34, total: 40 },
    { day: 'Ter', pct: 92, done: 37, total: 40 },
    { day: 'Qua', pct: 78, done: 31, total: 40 },
    { day: 'Qui', pct: 88, done: 35, total: 40 },
    { day: 'Sex', pct: 95, done: 38, total: 40 },
    { day: 'Sáb', pct: 65, done: 26, total: 40 },
    { day: 'Dom', pct: 60, done: 24, total: 40 },
];

// Categorias de Exercícios mais Prescritos
const topCategories = [
    { name: 'Motricidade Orofacial', total: 45, color: 'bg-blue-500' },
    { name: 'Articulação e Fala', total: 32, color: 'bg-indigo-500' },
    { name: 'Voz e Respiração', total: 28, color: 'bg-sky-400' },
    { name: 'Deglutição (Disfagia)', total: 19, color: 'bg-emerald-500' },
];

// Atividades Recentes do Aplicativo
const recentActivities = [
    {
        id: 1,
        paciente: 'Arthur Gabriel Silva',
        exercicio: 'Vibração Labial Contínua (3x 30s)',
        data: 'Há 12 minutos',
        status: 'Concluído',
        statusColor: 'text-emerald-700 bg-emerald-50 ring-emerald-600/20',
        score: 'Feedback de áudio: Excelente',
    },
    {
        id: 2,
        paciente: 'Larissa Manoela Souza',
        exercicio: 'Fortalecimento de Língua no Palato',
        data: 'Há 45 minutos',
        status: 'Concluído',
        statusColor: 'text-emerald-700 bg-emerald-50 ring-emerald-600/20',
        score: 'Concluiu 4/4 repetições',
    },
    {
        id: 3,
        paciente: 'Lucas Menezes Santos',
        exercicio: 'Técnica de Bochecha Inflada',
        data: 'Há 3 horas',
        status: 'Incompleto',
        statusColor: 'text-amber-700 bg-amber-50 ring-amber-600/20',
        score: 'Interrompeu na metade da série',
    },
    {
        id: 4,
        paciente: 'Matheus Fontoura',
        exercicio: 'Elevação de Laringe com Deglutição',
        data: 'Ontem às 19:40',
        status: 'Concluído',
        statusColor: 'text-emerald-700 bg-emerald-50 ring-emerald-600/20',
        score: 'Feedback de vídeo: Postura correta',
    },
];

// Pacientes que precisam de atenção (não treinaram nos últimos 3 dias)
const attentionPatients = [
    { name: 'Beatriz Vasconcelos', daysWithoutTraining: 4, appCode: 'BT92K1' },
    { name: 'Guilherme Peixoto', daysWithoutTraining: 3, appCode: 'GP44M0' },
];
</script>

<template>
    <Head title="Dashboard - AIFono" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Visão Geral da Clínica</h2>
                    <p class="text-sm text-gray-500">Acompanhe o engajamento dos seus pacientes e prescrições do app.</p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('pacientes.create')"
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <PlusIcon class="h-4 w-4 stroke-2" />
                        Cadastrar Paciente
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- 1. CARDS DE MÉTRICAS / KPIS -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-gray-300"
                >
                    <div class="flex items-center justify-between">
                        <div :class="[stat.color, 'flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-md']">
                            <component :is="stat.icon" class="h-6 w-6" />
                        </div>
                        <span
                            :class="[
                                stat.changeType === 'positive' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 bg-gray-50',
                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold'
                            ]"
                        >
                            <ArrowTrendingUpIcon v-if="stat.changeType === 'positive'" class="h-3 w-3" />
                            {{ stat.change }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-500">{{ stat.name }}</h3>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ stat.value }}</p>
                    </div>
                </div>
            </div>

            <!-- 2. SEÇÃO DE GRÁFICOS E METAS -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Gráfico de Adesão Semanal ao App (2 colunas) -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Adesão aos Treinos no Aplicativo</h3>
                            <p class="text-xs text-gray-500">Porcentagem de pacientes que concluíram os exercícios diários prescritos.</p>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span> Treinos Realizados
                            </span>
                        </div>
                    </div>

                    <!-- Barras do Gráfico -->
                    <div class="mt-8 flex h-52 items-end justify-between gap-3 border-b border-gray-100 pb-3 px-2 sm:gap-6">
                        <div
                            v-for="item in weeklyAdherence"
                            :key="item.day"
                            class="group relative flex flex-1 flex-col items-center gap-2"
                        >
                            <!-- Tooltip no Hover -->
                            <div class="pointer-events-none absolute -top-10 opacity-0 transition group-hover:opacity-100 z-10">
                                <div class="rounded-lg bg-gray-900 px-2 py-1 text-[11px] font-semibold text-white shadow">
                                    {{ item.done }}/{{ item.total }} ({{ item.pct }}%)
                                </div>
                            </div>

                            <!-- Barra de Progresso -->
                            <div class="w-full max-w-[42px] bg-gray-100 rounded-xl overflow-hidden h-40 flex items-end">
                                <div
                                    class="w-full rounded-xl bg-blue-600 transition-all duration-500 group-hover:bg-blue-700"
                                    :style="{ height: `${item.pct}%` }"
                                ></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600">{{ item.day }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                        <span>Meta clínica: 80% de conclusão semanal</span>
                        <span class="font-semibold text-emerald-600">Média atual: 81.1%</span>
                    </div>
                </div>

                <!-- Distribuição por Especialidade / Foco -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Distribuição por Área</h3>
                        <p class="text-xs text-gray-500">Exercícios mais prescritos por área de atuação fonoaudiológica.</p>

                        <div class="mt-6 space-y-4">
                            <div v-for="cat in topCategories" :key="cat.name">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-gray-700">{{ cat.name }}</span>
                                    <span class="font-bold text-gray-900">{{ cat.total }} prescrições</span>
                                </div>
                                <div class="mt-1.5 h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                                    <div
                                        :class="[cat.color, 'h-full rounded-full']"
                                        :style="{ width: `${(cat.total / 50) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                        <div class="flex items-center gap-3">
                            <DevicePhoneMobileIcon class="h-6 w-6 text-blue-600 shrink-0" />
                            <div>
                                <h4 class="text-xs font-bold text-blue-900">Sincronização Ativa</h4>
                                <p class="text-[11px] text-blue-700">Todos os novos exercícios prescritos entram instantaneamente na fila do app do paciente.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. FEED DE ATIVIDADE DO APP & ALERTAS -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Feed de Treinos Recentes (2 colunas) -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Atividades Recentes no App</h3>
                            <p class="text-xs text-gray-500">Execuções de exercícios registradas em tempo real pelos pacientes.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Ao Vivo
                        </span>
                    </div>

                    <div class="mt-6 divide-y divide-gray-100">
                        <div
                            v-for="act in recentActivities"
                            :key="act.id"
                            class="flex flex-col gap-2 py-3.5 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50/50 px-2 rounded-xl transition"
                        >
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 font-bold text-xs">
                                    {{ act.paciente.charAt(0) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ act.paciente }}</h4>
                                    <p class="text-xs text-gray-600">{{ act.exercicio }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ act.score }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                                <span :class="[act.statusColor, 'inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold ring-1 ring-inset']">
                                    {{ act.status }}
                                </span>
                                <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                    <ClockIcon class="h-3 w-3" />
                                    {{ act.data }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Painel de Atenção / Inatividade -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900">Atenção Necessária</h3>
                    <p class="text-xs text-gray-500">Pacientes com prescrição ativa sem registros no app há mais de 3 dias.</p>

                    <div class="mt-5 space-y-3">
                        <div
                            v-for="p in attentionPatients"
                            :key="p.appCode"
                            class="rounded-xl border border-amber-200 bg-amber-50/60 p-3.5 flex items-center justify-between"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-amber-950">{{ p.name }}</h4>
                                <p class="text-[11px] text-amber-800">Sem treinar há {{ p.daysWithoutTraining }} dias</p>
                                <span class="text-[10px] font-mono font-semibold text-amber-900/60">Cód: {{ p.appCode }}</span>
                            </div>

                            <Link
                                :href="route('pacientes.index')"
                                class="rounded-lg bg-amber-200/60 p-1.5 text-amber-900 hover:bg-amber-200 transition"
                                title="Ver Prontuário"
                            >
                                <ChevronRightIcon class="h-4 w-4 stroke-2" />
                            </Link>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Total de prescrições no mês:</span>
                            <span class="font-bold text-gray-900">86</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <span class="text-gray-500">Conclusões com sucesso:</span>
                            <span class="font-bold text-emerald-600">72</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>