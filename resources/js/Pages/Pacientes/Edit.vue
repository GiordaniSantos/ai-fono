<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    KeyIcon,
    PhotoIcon,
    ArrowTopRightOnSquareIcon,
    TrashIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';
import { maskPhone } from '@/Utils/mask';

const props = defineProps({
    paciente: {
        type: Object,
        required: true,
    },
});

const fileInput = ref(null);
const previewUrl = ref(props.paciente.anexo_url || null);

const form = useForm({
    _method: 'PATCH',
    nome: props.paciente.nome,
    data_nascimento: props.paciente.data_nascimento ? props.paciente.data_nascimento.substring(0, 10) : '',
    email: props.paciente.email ?? '',
    telefone: maskPhone(props.paciente.telefone ?? ''),
    diagnostico: props.paciente.diagnostico ?? '',
    anexo: null,
    remover_anexo: false,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.anexo = file;
        form.remover_anexo = false;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const removeFile = () => {
    form.anexo = null;
    form.remover_anexo = true;
    previewUrl.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submit = () => {
    form.post(route('pacientes.update', props.paciente.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar - ${paciente.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('pacientes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Editar Paciente</h2>
            </div>
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-blue-600 p-2 text-white">
                        <KeyIcon class="h-5 w-5" />
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-blue-900">Código de Acesso do Paciente</div>
                        <div class="text-xs text-blue-700">Utilizado para o paciente se autenticar no aplicativo.</div>
                    </div>
                </div>
                <span class="rounded-xl border border-blue-200 bg-white px-3.5 py-1.5 font-mono text-base font-bold text-blue-700 shadow-sm">
                    {{ paciente.codigo_acesso }}
                </span>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="nome" value="Nome Completo *" />
                        <TextInput
                            id="nome"
                            type="text"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.nome"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.nome" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="data_nascimento" value="Data de Nascimento *" />
                            <TextInput
                                id="data_nascimento"
                                type="date"
                                class="mt-1 block w-full rounded-xl"
                                v-model="form.data_nascimento"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.data_nascimento" />
                        </div>

                        <div>
                            <InputLabel for="telefone" value="Telefone / WhatsApp" />
                            <TextInput
                                id="telefone"
                                type="text"
                                class="mt-1 block w-full rounded-xl"
                                v-model="form.telefone"
                                @input="form.telefone = maskPhone($event.target.value)"
                                maxlength="15"
                            />
                            <InputError class="mt-2" :message="form.errors.telefone" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="email" value="E-mail" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="diagnostico" value="Diagnóstico / Queixa Principal" />
                        <textarea
                            id="diagnostico"
                            rows="4"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.diagnostico"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.diagnostico" />
                    </div>

                    <div class="space-y-3">
                        <InputLabel value="Anexo / Imagem do Diagnóstico (Exame, Audiometria, Laudo)" />

                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <div
                            v-if="!previewUrl"
                            @click="fileInput.click()"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 p-6 transition hover:border-blue-500 hover:bg-blue-50/30"
                        >
                            <PhotoIcon class="h-10 w-10 text-gray-400" />
                            <span class="mt-2 text-sm font-semibold text-gray-700">Clique para selecionar uma imagem</span>
                            <span class="text-xs text-gray-500">PNG, JPG ou WEBP até 4MB</span>
                        </div>

                        <div
                            v-else
                            class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 p-4"
                        >
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <img
                                        :src="previewUrl"
                                        alt="Imagem do diagnóstico"
                                        class="h-28 w-28 rounded-xl border border-gray-300 bg-white object-cover shadow-sm"
                                    />
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">Imagem do Diagnóstico</h4>
                                        <p class="text-xs text-gray-500">Arquivo anexado ao prontuário do paciente</p>

                                        <div class="mt-3 flex items-center gap-3">
                                            <a
                                                :href="previewUrl"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline"
                                            >
                                                <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                                                Abrir em tamanho real
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 border-t border-gray-200 pt-3 sm:border-0 sm:pt-0">
                                    <button
                                        type="button"
                                        @click="fileInput.click()"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-100 transition"
                                    >
                                        <ArrowPathIcon class="h-4 w-4 text-gray-500" />
                                        Substituir
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeFile"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm hover:bg-red-50 transition"
                                    >
                                        <TrashIcon class="h-4 w-4 text-red-500" />
                                        Remover
                                    </button>
                                </div>
                            </div>
                        </div>

                        <InputError class="mt-2" :message="form.errors.anexo" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <Link
                            :href="route('pacientes.index')"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition"
                        >
                            Cancelar
                        </Link>
                        <PrimaryButton
                            class="!rounded-xl !bg-blue-600 !px-6 !py-2.5 !font-semibold hover:!bg-blue-700"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Salvar Alterações
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>