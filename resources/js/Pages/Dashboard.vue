<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Panel Principal
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Bienvenido de vuelta a Nutritive Agent
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-lg border border-green-100 dark:border-gray-600">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Citas del Mes</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">12</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-lg border border-green-100 dark:border-gray-600">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Citas Completadas</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">8</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-lg border border-green-100 dark:border-gray-600">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Próxima Cita</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">Mañana, 10:00 AM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-8">
                        <div class="flex items-center mb-8">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Citas</h3>
                                <p class="text-gray-600 dark:text-gray-400">Administra tus consultas nutricionales</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Calendario de Citas -->
                            <a href="/appointments/calendar" class="group">
                                <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 border-2 border-green-200 dark:border-gray-600 rounded-2xl p-8 transition-all duration-300 hover:shadow-2xl hover:border-green-400 dark:hover:border-green-500 hover:-translate-y-2">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="text-green-500 group-hover:translate-x-2 transition-transform duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">📅 Calendario de Citas</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                        Visualiza y agenda nuevas consultas en nuestro calendario interactivo. Encuentra horarios disponibles y programa tu próxima sesión.
                                    </p>
                                    <div class="mt-4 flex items-center text-green-600 dark:text-green-400 font-medium">
                                        <span>Explorar calendario</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Lista de Citas -->
                            <a href="/appointments" class="group">
                                <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 border-2 border-green-200 dark:border-gray-600 rounded-2xl p-8 transition-all duration-300 hover:shadow-2xl hover:border-green-400 dark:hover:border-green-500 hover:-translate-y-2">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <div class="text-green-500 group-hover:translate-x-2 transition-transform duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">📋 Lista de Citas</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                        Revisa todas tus citas programadas, consulta detalles y realiza seguimiento a tu historial de consultas nutricionales.
                                    </p>
                                    <div class="mt-4 flex items-center text-green-600 dark:text-green-400 font-medium">
                                        <span>Ver todas las citas</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Acciones Rápidas</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <a href="/appointments/create" class="flex items-center p-4 bg-green-50 dark:bg-gray-700 rounded-xl hover:bg-green-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Nueva Cita</span>
                                </a>
                                <a href="/profile" class="flex items-center p-4 bg-green-50 dark:bg-gray-700 rounded-xl hover:bg-green-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Mi Perfil</span>
                                </a>
                                <a href="/appointments/calendar" class="flex items-center p-4 bg-green-50 dark:bg-gray-700 rounded-xl hover:bg-green-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Ver Calendario</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>