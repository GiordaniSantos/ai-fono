<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    SparklesIcon,
    DevicePhoneMobileIcon,
    ComputerDesktopIcon,
    ArrowTrendingUpIcon,
    CheckCircleIcon,
    BoltIcon,
    AcademicCapIcon,
    ChartBarIcon,
    UserGroupIcon,
    ArrowRightIcon,
    QuestionMarkCircleIcon,
    ClockIcon,
    DocumentTextIcon,
    ShieldCheckIcon,
    ChevronDownIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    laravelVersion: {
        type: String,
        default: '',
    },
    phpVersion: {
        type: String,
        default: '',
    },
});

// Simulador interativo da IA na Landing Page
const selectedTarget = ref('fonema_r');
const selectedAge = ref('6 anos');
const selectedTheme = ref('Dinossauros');

const generatedSample = ref({
    title: 'A Missão do T-Rex Rápido',
    type: 'Trava-língua e Leitura Lúdica',
    text: 'O T-Rex Rato correu para a Rocha Redonda. Na subida rápida, rugiu bem forte: "Rrrrroooar!" sem tropeçar no rabo!',
    phoneme: 'Fonema /r/ vibrante múltiplo',
});

const faqs = ref([
    {
        q: 'Como o paciente acessa seus exercícios no aplicativo?',
        a: 'Cada paciente cadastrado na plataforma recebe um código de acesso exclusivo de 6 dígitos. Basta baixar o app e digitar o código — sem necessidade de login complexo com senhas ou e-mails.',
        open: false,
    },
    {
        q: 'Como a Inteligência Artificial auxilia na criação de exercícios?',
        a: 'A IA cruza o objetivo fonoaudiológico (ex: fonema alvo, motricidade, deglutição), a idade do paciente e seus interesses pessoais (como heróis, esportes ou animais), gerando em segundos textos, roteiros e histórias personalizadas que garantem maior engajamento.',
        open: false,
    },
    {
        q: 'Como o fonoaudiólogo acompanha se o paciente realmente treinou em casa?',
        a: 'O aplicativo registra as execuções, horários e feedbacks de cada atividade. O especialista visualiza tudo em tempo real no seu painel web com gráficos de adesão e histórico de frequência.',
        open: false,
    },
    {
        q: 'Por que a prática diária no app é superior ao papel impresso?',
        a: 'A neuroplasticidade exige repetição constante e correta. O app fornece instruções multimídia claras, lembretes de treino e validação do esforço, eliminando a insegurança de realizar movimentos errados longe da clínica.',
        open: false,
    },
]);
</script>

<template>
    <Head title="AIFONO - Reabilitação Fonoaudiológica Inteligente" />

    <div class="min-h-screen bg-slate-900 text-slate-100 selection:bg-blue-600 selection:text-white">
        <header class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-900/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-500/25">
                        <SparklesIcon class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-white">AI<span class="text-blue-500">FONO</span></span>
                        <span class="ml-2 hidden rounded-full bg-blue-500/10 px-2 py-0.5 text-[10px] font-semibold text-blue-400 ring-1 ring-blue-500/20 sm:inline-block">
                            Clínica & App
                        </span>
                    </div>
                </div>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 md:flex">
                    <a href="#solucao" class="transition hover:text-white">A Solução</a>
                    <a href="#ia" class="transition hover:text-white">Inteligência Artificial</a>
                    <a href="#app" class="transition hover:text-white">O Aplicativo</a>
                    <a href="#faq" class="transition hover:text-white">Dúvidas</a>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500"
                        >
                            <ComputerDesktopIcon class="h-4 w-4" />
                            Acessar Painel
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-blue-600/30 transition hover:from-blue-500 hover:to-indigo-500"
                        >
                            Criar Conta
                            <ArrowRightIcon class="h-4 w-4" />
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-32">
            <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-tr from-blue-600/20 via-indigo-500/10 to-transparent blur-3xl"></div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-xs font-semibold text-blue-400 backdrop-blur">
                    <AcademicCapIcon class="h-4 w-4" />
                    Reabilitação Baseada em Neuroplasticidade & Dados Reais
                </div>

                <h1 class="mx-auto mt-6 max-w-4xl text-4xl font-extrabold tracking-tight text-white sm:text-6xl sm:leading-[1.15]">
                    Superando as folhas de papel com <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-teal-300 bg-clip-text text-transparent">estímulo diário e IA</span>.
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-base text-slate-300 sm:text-lg sm:leading-relaxed">
                    Sessões clínicas ocorrem 1 a 2 vezes por semana, mas a reorganização neural exige prática diária. O <strong>AIFONO</strong> conecta o fonoaudiólogo ao cotidiano do paciente através de prescrições inteligentes e um aplicativo interativo de acompanhamento domiciliar.
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Link
                        v-if="canRegister && !$page.props.auth?.user"
                        :href="route('register')"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-xl shadow-blue-500/25 transition hover:bg-blue-500 sm:w-auto"
                    >
                        Começar Gratuitamente
                        <ArrowRightIcon class="h-5 w-5" />
                    </Link>
                    <a
                        href="#ia"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-700 bg-slate-800/80 px-6 py-3.5 text-base font-semibold text-slate-200 transition hover:bg-slate-700 sm:w-auto"
                    >
                        <SparklesIcon class="h-5 w-5 text-blue-400" />
                        Ver Gerador de Exercícios por IA
                    </a>
                </div>

                <div class="relative mt-16 rounded-2xl border border-slate-800 bg-slate-950/60 p-4 shadow-2xl backdrop-blur-xl lg:mt-20 lg:p-8">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-center">
                        <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900 p-5 text-left shadow-inner lg:col-span-8">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-3 w-3 rounded-full bg-red-500/80"></div>
                                    <div class="h-3 w-3 rounded-full bg-amber-500/80"></div>
                                    <div class="h-3 w-3 rounded-full bg-emerald-500/80"></div>
                                    <span class="ml-2 text-xs font-mono text-slate-500">painel.aifono.com.br</span>
                                </div>
                                <span class="rounded-md bg-blue-500/20 px-2 py-0.5 text-[11px] font-semibold text-blue-400">Painel do Fonoaudiólogo</span>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3">
                                <div class="rounded-lg bg-slate-800/60 p-3">
                                    <div class="text-xs text-slate-400">Pacientes Ativos</div>
                                    <div class="text-lg font-bold text-white">38</div>
                                </div>
                                <div class="rounded-lg bg-slate-800/60 p-3">
                                    <div class="text-xs text-slate-400">Adesão Domiciliar</div>
                                    <div class="text-lg font-bold text-emerald-400">84%</div>
                                </div>
                                <div class="rounded-lg bg-slate-800/60 p-3">
                                    <div class="text-xs text-slate-400">Exercícios por IA</div>
                                    <div class="text-lg font-bold text-indigo-400">Instantâneo</div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-lg bg-slate-950/50 p-3 text-xs">
                                <div class="flex items-center justify-between font-semibold text-slate-300">
                                    <span>Prescrição: Arthur (6 anos)</span>
                                    <span class="text-blue-400">Código: AR9012</span>
                                </div>
                                <p class="mt-1 text-slate-400">3 exercícios diários • Foco: Fonema /r/ + Motricidade Orofacial</p>
                            </div>
                        </div>

                        <div class="mx-auto w-full max-w-xs rounded-3xl border-4 border-slate-700 bg-slate-950 p-4 text-left shadow-2xl lg:col-span-4">
                            <div class="mx-auto h-4 w-24 rounded-full bg-slate-800"></div>
                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-slate-400">Olá, Arthur! 👋</div>
                                    <div class="text-sm font-bold text-white">Treino de Hoje</div>
                                </div>
                                <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-400">2/3 Concluídos</span>
                            </div>

                            <div class="mt-4 space-y-2.5">
                                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-2.5 flex items-center justify-between">
                                    <div class="text-xs text-slate-200">
                                        <div class="font-semibold">Vibração Labial</div>
                                        <div class="text-[10px] text-emerald-400">Concluído com sucesso</div>
                                    </div>
                                    <CheckCircleIcon class="h-5 w-5 text-emerald-400" />
                                </div>

                                <div class="rounded-xl border border-blue-500/30 bg-blue-500/10 p-2.5 flex items-center justify-between">
                                    <div class="text-xs text-slate-200">
                                        <div class="font-semibold">O T-Rex na Rocha</div>
                                        <div class="text-[10px] text-blue-400">Pronto para iniciar</div>
                                    </div>
                                    <div class="rounded-full bg-blue-600 p-1 text-white">
                                        <ArrowRightIcon class="h-3 w-3" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="solucao" class="border-t border-slate-800 bg-slate-950/60 py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-blue-400">O Desafio Clínico</h2>
                    <p class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">
                        O maior obstáculo da fonoaudiologia não é o diagnóstico. É a continuidade.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="rounded-2xl border border-red-500/20 bg-gradient-to-b from-red-500/5 to-transparent p-8">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500/10 text-red-400">
                                <DocumentTextIcon class="h-6 w-6" />
                            </div>
                            <h3 class="text-xl font-bold text-red-200">Método Tradicional com Impressos</h3>
                        </div>
                        <ul class="mt-6 space-y-4 text-sm text-slate-400">
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 font-bold">✕</span>
                                <span><strong>Folhas estáticas esquecidas:</strong> O paciente depende da memória sobre como executar o movimento prescrito.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 font-bold">✕</span>
                                <span><strong>Insegurança e abandono:</strong> Medo de praticar de forma incorreta sem supervisão leva à desistência.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 font-bold">✕</span>
                                <span><strong>Relato subjetivo:</strong> O profissional descobre se o paciente treinou apenas na sessão seguinte.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 font-bold">✕</span>
                                <span><strong>Materiais genéricos:</strong> Atividades desinteressantes que não engajam crianças e adultos.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-emerald-500/30 bg-gradient-to-b from-emerald-500/10 to-transparent p-8">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400">
                                <BoltIcon class="h-6 w-6" />
                            </div>
                            <h3 class="text-xl font-bold text-emerald-300">A Abordagem AIFONO</h3>
                        </div>
                        <ul class="mt-6 space-y-4 text-sm text-slate-300">
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span><strong>Estímulo diário e guiado:</strong> Instruções claras no app ativam a neuroplasticidade com repetições constantes.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span><strong>Validação do esforço:</strong> Gamificação e acompanhamento de tarefas reduzem o medo de errar.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span><strong>Dados objetivos para ajuste clínico:</strong> O especialista vê métricas reais de frequência e adesão.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span><strong>Hiperpersonalização com IA:</strong> Exercícios gerados sob medida para a idade e temas de interesse do paciente.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="ia" class="py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-6">
                        <div class="inline-flex items-center gap-2 rounded-lg bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-400 ring-1 ring-indigo-500/20">
                            <SparklesIcon class="h-4 w-4" />
                            Adeus ao Bloqueio Criativo
                        </div>
                        <h2 class="mt-4 text-3xl font-extrabold text-white sm:text-4xl">
                            Crie planos terapêuticos sob medida em poucos segundos.
                        </h2>
                        <p class="mt-4 text-slate-300 text-sm leading-relaxed sm:text-base">
                            Elaborar atividades personalizadas manualmente consome horas da rotina do especialista. Com nossos modelos de IA integrados, basta apontar a queixa clínica e as preferências do paciente para gerar trava-línguas, narrativas e exercícios fonéticos altamente aderentes.
                        </p>

                        <div class="mt-8 space-y-3">
                            <div class="flex items-center gap-3 text-sm text-slate-200">
                                <CheckCircleIcon class="h-5 w-5 text-blue-400" />
                                <span>Economia de até 70% do tempo de preparação de materiais</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-200">
                                <CheckCircleIcon class="h-5 w-5 text-blue-400" />
                                <span>Atividades lúdicas que conectam crianças e adolescentes</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-200">
                                <CheckCircleIcon class="h-5 w-5 text-blue-400" />
                                <span>Foco do profissional 100% voltado para a análise clínica</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-950 p-6 shadow-2xl lg:col-span-6">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Simulador de Prompt IA</span>
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div class="rounded-lg bg-slate-900 p-2.5 border border-slate-800">
                                    <span class="text-slate-500 block">Objetivo</span>
                                    <span class="font-bold text-white">Fonema /r/</span>
                                </div>
                                <div class="rounded-lg bg-slate-900 p-2.5 border border-slate-800">
                                    <span class="text-slate-500 block">Idade</span>
                                    <span class="font-bold text-white">6 anos</span>
                                </div>
                                <div class="rounded-lg bg-slate-900 p-2.5 border border-slate-800">
                                    <span class="text-slate-500 block">Interesse</span>
                                    <span class="font-bold text-indigo-400">Dinossauros</span>
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl border border-indigo-500/30 bg-indigo-950/30 p-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-indigo-300">{{ generatedSample.title }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ generatedSample.type }}</span>
                                </div>
                                <p class="mt-3 text-sm italic text-slate-200 leading-relaxed">
                                    "{{ generatedSample.text }}"
                                </p>
                                <div class="mt-3 flex items-center justify-between border-t border-indigo-900/50 pt-2 text-[11px] text-indigo-400">
                                    <span>Alvo Clínico: {{ generatedSample.phoneme }}</span>
                                    <span class="font-semibold text-emerald-400">✓ Pronto para Prescrever</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="app" class="border-t border-slate-800 bg-slate-950/40 py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-blue-400">Ecossistema Conectado</h2>
                    <p class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">
                        Tudo o que o consultório e o paciente precisam.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-8 shadow-sm transition hover:border-slate-700">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400">
                            <ComputerDesktopIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold text-white">Plataforma do Terapeuta</h3>
                        <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                            Gestão simplificada de pacientes, prontuários, armazenamento de exames e laudos, e prescrição ágil com auxílio de Inteligência Artificial.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-8 shadow-sm transition hover:border-slate-700">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-400">
                            <DevicePhoneMobileIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold text-white">Aplicativo do Paciente</h3>
                        <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                            Acesso por código simples de 6 dígitos. Exercícios ilustrados, contadores de repetição, feedback instantâneo e lembretes para manter o ritmo diário.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-8 shadow-sm transition hover:border-slate-700">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                            <ChartBarIcon class="h-6 w-6" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold text-white">Métricas de Reabilitação</h3>
                        <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                            Acompanhe gráficos de adesão semanal, identifique pacientes em risco de inatividade e tome decisões clínicas baseadas em fatos e números reais.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="py-20 lg:py-28">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-blue-400">Tire Suas Dúvidas</h2>
                    <p class="mt-2 text-3xl font-extrabold text-white">Perguntas Frequentes</p>
                </div>

                <div class="mt-12 space-y-4">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 transition"
                    >
                        <button
                            @click="faq.open = !faq.open"
                            type="button"
                            class="flex w-full items-center justify-between text-left font-semibold text-white focus:outline-none"
                        >
                            <span>{{ faq.q }}</span>
                            <ChevronDownIcon
                                :class="[faq.open ? 'rotate-180 text-blue-400' : 'text-slate-500', 'h-5 w-5 transition-transform']"
                            />
                        </button>
                        <p v-if="faq.open" class="mt-3 text-sm text-slate-300 leading-relaxed">
                            {{ faq.a }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden border-t border-slate-800 bg-gradient-to-b from-blue-950/40 to-slate-900 py-16 lg:py-24 text-center">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Pronto para modernizar o acompanhamento dos seus pacientes?
                </h2>
                <p class="mx-auto mt-4 max-w-xl text-slate-300 text-sm sm:text-base">
                    Junte-se à nova era da fonoaudiologia baseada em neuroplasticidade contínua, inteligência artificial e engajamento real.
                </p>

                <div class="mt-8 flex justify-center gap-4">
                    <Link
                        v-if="canRegister && !$page.props.auth?.user"
                        :href="route('register')"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-blue-500/30 transition hover:bg-blue-500"
                    >
                        Cadastre-se Gratuitamente
                        <ArrowRightIcon class="h-5 w-5" />
                    </Link>
                    <Link
                        v-else-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-blue-500/30 transition hover:bg-blue-500"
                    >
                        Ir para o Painel
                    </Link>
                </div>
            </div>
        </section>

        <footer class="border-t border-slate-800/80 bg-slate-950 py-10 text-xs text-slate-500 text-center">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-2 font-semibold text-slate-400">
                    <SparklesIcon class="h-4 w-4 text-blue-500" />
                    AIFONO • Plataforma de Apoio e Reabilitação Fonoaudiológica
                </div>
                <div>
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }}) • Desenvolvido para TCC
                </div>
            </div>
        </footer>
    </div>
</template>