<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { confirmAction } from '@/Utils/alert';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    TagIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    categorias: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const isModalOpen = ref(false);
const editingCategoria = ref(null);

const form = useForm({
    nome: '',
});

const filteredCategorias = computed(() => {
    if (!search.value) return props.categorias;
    const term = search.value.toLowerCase();
    return props.categorias.filter((c) => c.nome.toLowerCase().includes(term));
});

const openCreateModal = () => {
    editingCategoria.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (categoria) => {
    editingCategoria.value = categoria;
    form.nome = categoria.nome;
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
    editingCategoria.value = null;
};

const submit = () => {
    if (editingCategoria.value) {
        form.patch(route('categorias.update', editingCategoria.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('categorias.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCategoria = async (categoria) => {
    const { isConfirmed } = await confirmAction({
        title: `Remover "${categoria.nome}"?`,
        text: categoria.exercicios_count > 0
            ? `Esta categoria possui ${categoria.exercicios_count} exercício(s) vinculado(s). Eles ficarão sem categoria definida.`
            : 'Esta ação não poderá ser desfeita.',
        confirmButtonText: 'Sim, excluir',
    });

    if (isConfirmed) {
        router.delete(route('categorias.destroy', categoria.id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.split('T')[0].split('-');
    return `${day}/${month}/${year}`;
};
</script>

<template>
    <Head title="Categorias de Exercícios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Categorias de Exercícios</h2>
                    <p class="text-sm text-gray-500">Organize seus exercícios por áreas e objetivos clínicos.</p>
                </div>

                <button
                    type="button"
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-5 w-5" />
                    Nova Categoria
                </button>
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
                    placeholder="Buscar categoria..."
                    class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div v-if="filteredCategorias.length === 0" class="py-12 text-center">
                    <TagIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhuma categoria encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Cadastre uma nova categoria para organizar seus exercícios.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Nome da Categoria</th>
                                <th class="px-6 py-4">Exercícios Vinculados</th>
                                <th class="px-6 py-4">Data de Criação</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="cat in filteredCategorias" :key="cat.id" class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5 font-medium text-gray-900">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                            <TagIcon class="h-4 w-4" />
                                        </div>
                                        {{ cat.nome }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ cat.exercicios_count }} {{ cat.exercicios_count === 1 ? 'exercício' : 'exercícios' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ formatDate(cat.created_at) }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="openEditModal(cat)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition"
                                            title="Editar Categoria"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteCategoria(cat)"
                                            class="rounded-lg p-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 transition"
                                            title="Excluir Categoria"
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

        <TransitionRoot as="template" :show="isModalOpen">
            <Dialog as="div" class="relative z-50" @close="closeModal">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" />
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
                        <DialogPanel class="relative mx-auto max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <DialogTitle class="text-lg font-bold text-gray-900">
                                    {{ editingCategoria ? 'Editar Categoria' : 'Nova Categoria' }}
                                </DialogTitle>
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                                >
                                    <XMarkIcon class="h-5 w-5" />
                                </button>
                            </div>

                            <form @submit.prevent="submit" class="mt-5 space-y-5">
                                <div>
                                    <InputLabel for="nome" value="Nome da Categoria *" />
                                    <TextInput
                                        id="nome"
                                        type="text"
                                        class="mt-1 block w-full rounded-xl"
                                        v-model="form.nome"
                                        required
                                        autofocus
                                        placeholder="Ex: Motricidade Orofacial, Fala, Voz..."
                                    />
                                    <InputError class="mt-2" :message="form.errors.nome" />
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="rounded-xl px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition"
                                    >
                                        Cancelar
                                    </button>
                                    <PrimaryButton
                                        class="!rounded-xl !bg-blue-600 !px-5 !py-2 !font-semibold hover:!bg-blue-700"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        {{ editingCategoria ? 'Salvar Alterações' : 'Cadastrar Categoria' }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>