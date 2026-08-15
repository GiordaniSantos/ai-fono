<script setup>
const logoUrl = '/images/logo.png'; 
</script>

<template>
    <div class="min-h-screen bg-black flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <div class="w-full max-w-lg min-h-[90vh] bg-[#1a1a1a] rounded-t-3xl sm:rounded-3xl shadow-2xl p-8 flex flex-col items-center">
 
            <div class="w-full flex flex-col items-center mt-12 mb-10 space-y-10">
                <img :src="logoUrl" alt="AIFono Logo" class="h-24 w-auto object-contain" />

                <h1 class="text-4xl font-bold text-white text-center leading-tight max-w-sm">
                    Cuidado contínuo, dentro e fora do consultório
                </h1>
            </div>

            <div class="w-full flex flex-col items-center">
                <slot />
            </div>
        </div>
    </div>
</template>