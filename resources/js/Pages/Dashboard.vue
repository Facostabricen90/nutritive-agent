<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Página Principal
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gestión de Citas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="/appointments/calendar" class="block p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition">
                                <h4 class="text-xl font-bold text-green-800 dark:text-green-400 mb-2">📅 Calendario de Citas</h4>
                                <p class="text-gray-600 dark:text-gray-400">Ver y agendar citas disponibles</p>
                            </a>
                            <a href="/appointments" class="block p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                <h4 class="text-xl font-bold text-blue-800 dark:text-blue-400 mb-2">📋 Lista de Citas</h4>
                                <p class="text-gray-600 dark:text-gray-400">Ver todas las citas registradas</p>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
