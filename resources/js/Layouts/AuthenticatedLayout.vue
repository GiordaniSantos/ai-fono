<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Toast } from '@/Utils/alert';
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {
    Bars3Icon,
    XMarkIcon,
    HomeIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
    ChevronDownIcon,
    UserGroupIcon,
    SparklesIcon,
    TagIcon,
} from '@heroicons/vue/24/outline';

const page = usePage();

const triggerFlashMessage = () => {
    const flash = page.props.flash;
    if (!flash) return;

    if (flash.success) {
        Toast.fire({
            icon: 'success',
            title: flash.success,
        });
    }

    if (flash.error) {
        Toast.fire({
            icon: 'error',
            title: flash.error,
        });
    }
};

onMounted(() => {
    triggerFlashMessage();
});

watch(
    () => page.props.flash,
    () => {
        triggerFlashMessage();
    },
    { deep: true }
);

const sidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    { name: 'Pacientes', href: route('pacientes.index'), icon: UserGroupIcon, current: route().current('pacientes.*') },
    { name: 'Exercícios', href: route('exercicios.index'), icon: SparklesIcon, current: route().current('exercicios.*') },
    { name: 'Categorias', href: route('categorias.index'), icon: TagIcon, current: route().current('categorias.*') },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild
                        as="template"
                        enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full"
                        enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leave-from="translate-x-0"
                        leave-to="-translate-x-full"
                    >
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                <button type="button" class="-m-2.5 p-2.5 text-white/70 hover:text-white" @click="sidebarOpen = false">
                                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                </button>
                            </div>

                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-[#1a1a1a] px-6 pb-4 border-r border-white/10">
                                <div class="flex h-20 shrink-0 items-center border-b border-white/10 pb-2">
                                    <Link :href="route('dashboard')" class="flex items-center">
                                        <img src="/images/logo.png" alt="AIFono" class="h-10 w-auto object-contain" />
                                    </Link>
                                </div>
                                <nav class="flex flex-1 flex-col">
                                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <ul role="list" class="-mx-2 space-y-1.5">
                                                <li v-for="item in navigation" :key="item.name">
                                                    <Link
                                                        :href="item.href"
                                                        :class="[
                                                            item.current
                                                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                                                            'group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold leading-6 transition duration-150',
                                                        ]"
                                                    >
                                                        <component :is="item.icon" class="h-6 w-6 shrink-0" aria-hidden="true" />
                                                        {{ item.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-white/10 bg-[#1a1a1a] px-6 pb-4">
                <div class="flex h-20 shrink-0 items-center border-b border-white/10">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <img src="/images/logo.png" alt="AIFono" class="h-10 w-auto object-contain" />
                    </Link>
                </div>

                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1.5">
                                <li v-for="item in navigation" :key="item.name">
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            item.current
                                                ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/30'
                                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                                            'group flex gap-x-3 rounded-xl p-2.5 text-sm font-semibold leading-6 transition duration-150',
                                        ]"
                                    >
                                        <component :is="item.icon" class="h-6 w-6 shrink-0" aria-hidden="true" />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="lg:pl-64 flex flex-col min-h-screen">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button
                    type="button"
                    class="-m-2.5 p-2.5 text-gray-600 hover:text-gray-900 lg:hidden"
                    @click="sidebarOpen = true"
                >
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <div class="flex flex-1 justify-end gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <Menu as="div" class="relative">
                            <MenuButton class="-m-1.5 flex items-center gap-3 rounded-full p-1.5 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <img
                                    v-if="$page.props.auth.user.foto_url"
                                    :src="$page.props.auth.user.foto_url"
                                    alt="Avatar"
                                    class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200"
                                />
                                <div
                                    v-else
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 font-bold text-xs text-blue-600 ring-1 ring-blue-200"
                                >
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>

                                <span class="text-sm font-medium leading-6">{{ $page.props.auth.user.name }}</span>
                                <ChevronDownIcon class="h-4 w-4 text-gray-400" aria-hidden="true" />
                            </MenuButton>

                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <MenuItems class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-xl border border-gray-200 bg-white py-2 shadow-lg focus:outline-none">
                                    <MenuItem v-slot="{ active }">
                                        <Link
                                            :href="route('profile.edit')"
                                            :class="[active ? 'bg-gray-50 text-gray-900' : 'text-gray-700', 'flex items-center px-4 py-2 text-sm leading-6 transition']"
                                        >
                                            <UserIcon class="mr-3 h-4 w-4 text-gray-400" />
                                            Perfil
                                        </Link>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            :class="[active ? 'bg-gray-50 text-red-600' : 'text-gray-700', 'flex w-full items-center px-4 py-2 text-sm leading-6 transition']"
                                        >
                                            <ArrowRightOnRectangleIcon class="mr-3 h-4 w-4 text-gray-400" />
                                            Sair
                                        </Link>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>

            <header class="border-b border-gray-200 bg-white" v-if="$slots.header">
                <div class="px-4 py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="py-6 flex-1 bg-gray-50">
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>