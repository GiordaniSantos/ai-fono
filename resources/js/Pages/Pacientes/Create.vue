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
    PhotoIcon,
    XMarkIcon,
    PlusIcon,
    SparklesIcon,
    CameraIcon,
    UserCircleIcon,
} from '@heroicons/vue/24/outline';
import { maskPhone } from '@/Utils/mask';

const fotoInput = ref(null);
const previewFotoUrl = ref(null);

const anexoInput = ref(null);
const previewAnexoUrl = ref(null);

const novoInteresse = ref('');

const sugestoes = [
    'Dinossauros',
    'Super-heróis',
    'Futebol',
    'Minecraft / Games',
    'Música / Canto',
    'Animais / Pets',
    'Carros / Veículos',
    'Princesas / Contos',
    'Espaço / Astronomia',
];

const form = useForm({
    foto: null,
    nome: '',
    data_nascimento: '',
    email: '',
    telefone: '',
    diagnostico: '',
    interesses: [],
    anexo: null,
});

const handleFotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.foto = file;
        previewFotoUrl.value = URL.createObjectURL(file);
    }
};

const removeFoto = () => {
    form.foto = null;
    previewFotoUrl.value = null;
    if (fotoInput.value) fotoInput.value.value = '';
};

const handleAnexoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.anexo = file;
        previewAnexoUrl.value = URL.createObjectURL(file);
    }
};

const removeAnexo = () => {
    form.anexo = null;
    previewAnexoUrl.value = null;
    if (anexoInput.value) anexoInput.value.value = '';
};

const addInteresse = () => {
    const valor = novoInteresse.value.trim();
    if (valor && !form.interesses.includes(valor)) {
        form.interesses.push(valor);
        novoInteresse.value = '';
    }
};

const toggleSugestao = (item) => {
    if (form.interesses.includes(item)) {
        removeInteresse(item);
    } else {
        form.interesses.push(item);
    }
};

const removeInteresse = (item) => {
    form.interesses = form.interesses.filter((i) => i !== item);
};

const submit = () => {
    form.post(route('pacientes.store'));
};
</script>

<template>
    <Head title="Novo Paciente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('pacientes.index')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Novo Paciente</h2>
            </div>
        </template>

        <div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="flex flex-col sm:flex-row items-center gap-5 border-b border-gray-100 pb-6">
                        <input
                            ref="fotoInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFotoChange"
                        />

                        <div class="relative group">
                            <div
                                v-if="previewFotoUrl"
                                class="h-24 w-24 rounded-full overflow-hidden border-2 border-blue-500 shadow-md"
                            >
                                <img
                                    :src="previewFotoUrl"
                                    alt="Foto do Paciente"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 border-2 border-dashed border-slate-300 text-slate-400"
                            >
                                <UserCircleIcon class="h-16 w-16 stroke-1" />
                            </div>

                            <button
                                v-if="previewFotoUrl"
                                type="button"
                                @click="removeFoto"
                                class="absolute -top-1 -right-1 rounded-full bg-red-600 p-1 text-white shadow-md hover:bg-red-700 transition"
                                title="Remover foto"
                            >
                                <XMarkIcon class="h-3.5 w-3.5" />
                            </button>
                        </div>

                        <div class="text-center sm:text-left space-y-1">
                            <h3 class="text-sm font-bold text-gray-900">Foto do Paciente</h3>
                            <p class="text-xs text-gray-500">
                                Esta foto será exibida no perfil e na identificação do aplicativo. (PNG, JPG até 4MB)
                            </p>
                            <div class="pt-1">
                                <button
                                    type="button"
                                    @click="fotoInput.click()"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                                >
                                    <CameraIcon class="h-4 w-4 text-gray-500" />
                                    {{ previewFotoUrl ? 'Alterar Foto' : 'Selecionar Foto' }}
                                </button>
                            </div>
                            <InputError class="mt-1" :message="form.errors.foto" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="nome" value="Nome Completo *" />
                        <TextInput
                            id="nome"
                            type="text"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.nome"
                            required
                            autofocus
                            placeholder="Ex: Arthur Gabriel Silva"
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
                                placeholder="(51) 98421-9721"
                            />
                            <InputError class="mt-2" :message="form.errors.telefone" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="email" value="E-mail (opcional)" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.email"
                            placeholder="paciente@exemplo.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="diagnostico" value="Diagnóstico / Queixa Principal" />
                        <textarea
                            id="diagnostico"
                            rows="3"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            v-model="form.diagnostico"
                            placeholder="Ex: Dificuldade no fonema /r/ vibrante, ceceio anterior, hipotonia labial..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.diagnostico" />
                    </div>

                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5 space-y-3">
                        <div class="flex items-center gap-2">
                            <SparklesIcon class="h-5 w-5 text-indigo-600" />
                            <div>
                                <h3 class="text-sm font-bold text-indigo-950">Interesses & Preferências do Paciente</h3>
                                <p class="text-xs text-indigo-700">
                                    Essas palavras-chave serão usadas pela <strong>Inteligência Artificial</strong> para gerar histórias e exercícios personalizados.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <TextInput
                                type="text"
                                class="block w-full rounded-xl bg-white border-indigo-200 text-sm placeholder:text-gray-400"
                                v-model="novoInteresse"
                                @keydown.enter.prevent="addInteresse"
                                placeholder="Digite um tema (ex: Pokémon, Basquete, Robótica) e tecle Enter..."
                            />
                            <button
                                type="button"
                                @click="addInteresse"
                                class="inline-flex items-center gap-1 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition"
                            >
                                <PlusIcon class="h-4 w-4" />
                                Inserir
                            </button>
                        </div>

                        <div v-if="form.interesses.length > 0" class="flex flex-wrap gap-2 pt-2">
                            <span
                                v-for="interesse in form.interesses"
                                :key="interesse"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-300 bg-white px-3 py-1 text-xs font-bold text-indigo-900 shadow-sm"
                            >
                                {{ interesse }}
                                <button
                                    type="button"
                                    @click="removeInteresse(interesse)"
                                    class="text-indigo-400 hover:text-red-600 transition"
                                >
                                    <XMarkIcon class="h-3.5 w-3.5" />
                                </button>
                            </span>
                        </div>
                        <div v-else class="text-xs text-gray-500 italic">
                            Nenhum interesse selecionado ainda. Adicione acima ou selecione as sugestões abaixo:
                        </div>

                        <div class="pt-2 border-t border-indigo-100/80">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-800/70 block mb-1.5">
                                Sugestões Rápidas:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="sugestao in sugestoes"
                                    :key="sugestao"
                                    type="button"
                                    @click="toggleSugestao(sugestao)"
                                    :class="[
                                        form.interesses.includes(sugestao)
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50',
                                        'rounded-lg border px-2.5 py-1 text-xs font-medium transition'
                                    ]"
                                >
                                    + {{ sugestao }}
                                </button>
                            </div>
                        </div>

                        <InputError class="mt-2" :message="form.errors.interesses" />
                    </div>

                    <div>
                        <InputLabel value="Anexo / Imagem do Diagnóstico (Exame, Laudo, Audiometria)" />
                        <input
                            ref="anexoInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleAnexoChange"
                        />

                        <div class="mt-2">
                            <div v-if="previewAnexoUrl" class="relative inline-block">
                                <img
                                    :src="previewAnexoUrl"
                                    alt="Pré-visualização do laudo"
                                    class="h-44 w-auto rounded-xl border border-gray-200 object-cover shadow-sm"
                                />
                                <button
                                    type="button"
                                    @click="removeAnexo"
                                    class="absolute -right-2 -top-2 rounded-full bg-red-600 p-1 text-white shadow-md hover:bg-red-700 transition"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                            </div>

                            <div
                                v-else
                                @click="anexoInput.click()"
                                class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 p-6 transition hover:border-blue-500 hover:bg-blue-50/30"
                            >
                                <PhotoIcon class="h-10 w-10 text-gray-400" />
                                <span class="mt-2 text-sm font-semibold text-gray-700">Clique para selecionar imagem do laudo ou exame</span>
                                <span class="text-xs text-gray-500">PNG, JPG ou WEBP até 4MB</span>
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
                            Cadastrar Paciente
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>