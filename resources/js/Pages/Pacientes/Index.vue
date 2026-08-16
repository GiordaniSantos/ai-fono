<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { confirmAction } from '@/Utils/alert';
import { maskPhone } from '@/Utils/mask';
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
    ClipboardDocumentCheckIcon,
    ClipboardDocumentIcon,
    MagnifyingGlassIcon,
    UserIcon,
    PhotoIcon,
    XMarkIcon,
    ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pacientes: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const copiedId = ref(null);
const activeModalImage = ref(null);

const filteredPacientes = computed(() => {
    if (!search.value) return props.pacientes;
    const term = search.value.toLowerCase();
    return props.pacientes.filter(
        (p) =>
            p.nome.toLowerCase().includes(term) ||
            p.codigo_acesso.toLowerCase().includes(term) ||
            (p.email && p.email.toLowerCase().includes(term))
    );
});

const copyCode = (code, id) => {
    navigator.clipboard.writeText(code);
    copiedId.value = id;
    setTimeout(() => {
        copiedId.value = null;
    }, 2000);
};

const deletePaciente = async (paciente) => {
    const { isConfirmed } = await confirmAction({
        title: `Remover ${paciente.nome}?`,
        text: 'O paciente perderá o acesso com o código atual.',
        confirmButtonText: 'Sim, excluir',
    });

    if (isConfirmed) {
        router.delete(route('pacientes.destroy', paciente.id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.split('T')[0].split('-');
    return `${day}/${month}/${year}`;
};
</script>

<template>
    <Head title="Pacientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pacientes</h2>
                    <p class="text-sm text-gray-500">Gerencie seus pacientes e códigos de acesso ao aplicativo.</p>
                </div>

                <Link
                    :href="route('pacientes.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Novo Paciente
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <div class="relative max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por nome, e-mail ou código..."
                    class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
            </div>

            <!-- Tabela -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div v-if="filteredPacientes.length === 0" class="py-12 text-center">
                    <UserIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum paciente encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece cadastrando um novo paciente no sistema.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Paciente</th>
                                <th class="px-6 py-4">Anexo / Exame</th>
                                <th class="px-6 py-4">Nascimento</th>
                                <th class="px-6 py-4">Contato</th>
                                <th class="px-6 py-4">Código de Acesso</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="paciente in filteredPacientes" :key="paciente.id" class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ paciente.nome }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ paciente.diagnostico || 'Sem diagnóstico informado' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <button
                                        v-if="paciente.anexo_url"
                                        type="button"
                                        @click="activeModalImage = paciente.anexo_url"
                                        class="group relative block h-10 w-10 overflow-hidden rounded-lg border border-gray-200 bg-gray-100 shadow-sm"
                                        title="Clique para ampliar o laudo"
                                    >
                                        <img
                                            :src="paciente.anexo_url"
                                            alt="Laudo"
                                            class="h-full w-full object-cover transition group-hover:scale-110"
                                        />
                                    </button>
                                    <span v-else class="text-xs text-gray-400 italic">Sem anexo</span>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ formatDate(paciente.data_nascimento) }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-900">{{ maskPhone(paciente.telefone) || '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ paciente.email || '-' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <button
                                        @click="copyCode(paciente.codigo_acesso, paciente.id)"
                                        type="button"
                                        title="Clique para copiar"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-mono font-bold text-blue-700 transition hover:bg-blue-100"
                                    >
                                        {{ paciente.codigo_acesso }}
                                        <ClipboardDocumentCheckIcon v-if="copiedId === paciente.id" class="h-4 w-4 text-green-600" />
                                        <ClipboardDocumentIcon v-else class="h-4 w-4 text-blue-500" />
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('pacientes.edit', paciente.id)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition"
                                            title="Editar"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button
                                            @click="deletePaciente(paciente)"
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
                        <DialogPanel class="relative mx-auto max-w-3xl overflow-hidden rounded-2xl bg-white p-4 shadow-2xl">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <h3 class="text-base font-semibold text-gray-900">Imagem do Diagnóstico / Exame</h3>
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
                                    alt="Laudo Ampliado"
                                    class="max-h-[70vh] w-auto object-contain rounded-lg"
                                />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>