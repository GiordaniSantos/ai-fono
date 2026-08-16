<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { CameraIcon, UserIcon } from '@heroicons/vue/24/outline';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const photoInput = ref(null);
const photoPreview = ref(user.foto_url || null);

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    email: user.email,
    crfa: user.crfa ?? '',
    especialidade: user.especialidade ?? '',
    foto: null,
});

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.foto = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.foto = null;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Informações do Perfil
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Atualize sua foto, dados cadastrais, registro profissional e endereço de e-mail.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel value="Foto de Perfil" />

                <input
                    type="file"
                    ref="photoInput"
                    class="hidden"
                    accept="image/*"
                    @change="handlePhotoChange"
                />

                <div class="mt-2 flex items-center gap-5">
                    <div class="relative group h-20 w-20 shrink-0">
                        <img
                            v-if="photoPreview"
                            :src="photoPreview"
                            alt="Foto de perfil"
                            class="h-20 w-20 rounded-full object-cover ring-2 ring-blue-500/20 shadow-sm"
                        />
                        <div
                            v-else
                            class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-gray-400 ring-1 ring-gray-200"
                        >
                            <UserIcon class="h-10 w-10" />
                        </div>
                    </div>

                    <div>
                        <button
                            type="button"
                            @click="photoInput.click()"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <CameraIcon class="h-4 w-4 text-gray-500" />
                            Alterar Foto
                        </button>
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG ou WEBP até 2MB.</p>
                    </div>
                </div>

                <InputError class="mt-2" :message="form.errors.foto" />
            </div>

            <div>
                <InputLabel for="name" value="Nome Completo" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="crfa" value="CRFa (Registro Profissional)" />
                <TextInput
                    id="crfa"
                    type="text"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.crfa"
                    placeholder="Ex: CRFa 7-12345"
                />
                <InputError class="mt-2" :message="form.errors.crfa" />
            </div>

            <div>
                <InputLabel for="especialidade" value="Especialidade" />
                <TextInput
                    id="especialidade"
                    type="text"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.especialidade"
                    placeholder="Ex: Linguagem, Voz, Motricidade Orofacial"
                />
                <InputError class="mt-2" :message="form.errors.especialidade" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Seu endereço de e-mail não foi verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Um novo link de verificação foi enviado para o seu endereço de e-mail.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Salvo com sucesso.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>