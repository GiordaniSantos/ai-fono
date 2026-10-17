<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    UsersIcon,
    SparklesIcon,
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    PlusIcon,
    ClockIcon,
    DevicePhoneMobileIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Array,
        default: () => []
    },
    weeklyAdherence: {
        type: Array,
        default: () => []
    },
    topCategories: {
        type: Array,
        default: () => []
    },
    recentActivities: {
        type: Array,
        default: () => []
    },
    attentionPatients: {
        type: Array,
        default: () => []
    },
});

const getIconComponent = (iconName) => {
    const icons = {
        UsersIcon,
        SparklesIcon,
        ClipboardDocumentListIcon,
        CheckCircleIcon
    };
    return icons[iconName] || SparklesIcon;
};

const mediaAdesaoText = computed(() => {
    if (!props.weeklyAdherence || props.weeklyAdherence.length === 0) return '0.0';
    const total = props.weeklyAdherence.reduce((acc, curr) => acc + curr.pct, 0);
    return (total / props.weeklyAdherence.length).toFixed(1);
});
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
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-gray-300"
                >
                    <div class="flex items-center justify-between">
                        <div :class="[stat.color, 'flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-md']">
                            <component :is="getIconComponent(stat.icon)" class="h-6 w-6" />
                        </div>
                        <span
                            :class="[
                                stat.changeType === 'positive' ? 'text-emerald-600 bg-emerald-50' : (stat.changeType === 'negative' ? 'text-red-600 bg-red-50' : 'text-gray-600 bg-gray-50'),
                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] uppercase tracking-wider font-bold'
                            ]"
                        >
                            <ArrowTrendingUpIcon v-if="stat.changeType === 'positive'" class="h-3 w-3" />
                            <ArrowTrendingDownIcon v-if="stat.changeType === 'negative'" class="h-3 w-3" />
                            {{ stat.change }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-500">{{ stat.name }}</h3>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ stat.value }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2 flex flex-col justify-between">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Adesão aos Treinos Diários</h3>
                            <p class="text-xs text-gray-500">Últimos 7 dias em relação à meta de exercícios prescritos.</p>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span> Treinos Realizados
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 flex h-52 items-end justify-between gap-3 border-b border-gray-100 pb-3 px-2 sm:gap-6">
                        <div
                            v-for="item in weeklyAdherence"
                            :key="item.day"
                            class="group relative flex flex-1 flex-col items-center gap-2"
                        >
                            <div class="pointer-events-none absolute -top-10 opacity-0 transition group-hover:opacity-100 z-10">
                                <div class="rounded-lg bg-gray-900 px-2 py-1 text-[11px] font-semibold text-white shadow whitespace-nowrap">
                                    {{ item.done }}/{{ item.total }} ({{ item.pct }}%)
                                </div>
                            </div>

                            <div class="w-full max-w-[42px] bg-gray-100 rounded-xl overflow-hidden h-40 flex items-end">
                                <div
                                    class="w-full rounded-xl bg-blue-600 transition-all duration-1000 ease-out group-hover:bg-blue-700"
                                    :style="{ height: `${item.pct}%` }"
                                ></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600">{{ item.day }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                        <span>Meta sugerida: 80% de conclusão</span>
                        <span class="font-bold" :class="Number(mediaAdesaoText) >= 70 ? 'text-emerald-600' : 'text-amber-600'">
                            Média atual: {{ mediaAdesaoText }}%
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Distribuição por Categoria</h3>
                        <p class="text-xs text-gray-500">Categorias de exercícios mais prescritas por você.</p>

                        <div v-if="topCategories.length > 0" class="mt-6 space-y-4">
                            <div v-for="cat in topCategories" :key="cat.name">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-gray-700 truncate mr-2">{{ cat.name }}</span>
                                    <span class="font-bold text-gray-900 whitespace-nowrap">{{ cat.total }} x</span>
                                </div>
                                <div class="mt-1.5 h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                                    <div
                                        :class="[cat.color, 'h-full rounded-full transition-all duration-1000']"
                                        :style="{ width: `${cat.pct}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="mt-10 text-center text-gray-400 text-sm">
                            <p>Nenhuma prescrição registrada ainda.</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                        <div class="flex items-center gap-3">
                            <DevicePhoneMobileIcon class="h-6 w-6 text-blue-600 shrink-0" />
                            <div>
                                <h4 class="text-xs font-bold text-blue-900">Sincronização Ativa</h4>
                                <p class="text-[11px] text-blue-700">Novos exercícios entram instantaneamente na fila do app do paciente.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Atividades Recentes no App</h3>
                            <p class="text-xs text-gray-500">Execuções e gravações registradas em tempo real.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Ao Vivo
                        </span>
                    </div>

                    <div v-if="recentActivities.length > 0" class="mt-6 divide-y divide-gray-100">
                        <div
                            v-for="act in recentActivities"
                            :key="act.id"
                            class="flex flex-col gap-2 py-3.5 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50/50 px-2 rounded-xl transition"
                        >
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 font-bold text-xs uppercase">
                                    {{ act.paciente ? act.paciente.charAt(0) : 'U' }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ act.paciente }}</h4>
                                    <p class="text-xs text-gray-600">{{ act.exercicio }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">{{ act.score }}</p>
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
                    
                    <div v-else class="py-10 text-center text-gray-400 text-sm italic">
                        Nenhum treino registrado recentemente pelos pacientes.
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Atenção Necessária</h3>
                        <p class="text-xs text-gray-500">Pacientes com prescrição ativa sem treinar há mais de 3 dias.</p>

                        <div v-if="attentionPatients.length > 0" class="mt-5 space-y-3">
                            <div
                                v-for="p in attentionPatients"
                                :key="p.appCode"
                                class="rounded-xl border border-amber-200 bg-amber-50/60 p-3.5 flex items-center justify-between"
                            >
                                <div>
                                    <h4 class="text-xs font-bold text-amber-950 truncate max-w-[150px]">{{ p.name }}</h4>
                                    <p class="text-[11px] text-amber-800">
                                        {{ typeof p.daysWithoutTraining === 'number' ? `Sem treinar há ${p.daysWithoutTraining} dias` : p.daysWithoutTraining }}
                                    </p>
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
                        
                        <div v-else class="mt-10 text-center text-emerald-600 text-sm font-semibold">
                            🎉 Todos os pacientes em dia!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>