<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    appointment: Object,
});

const formatDateTime = (dateTime) => {
    const date = new Date(dateTime);
    return date.toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'America/Bogota'
    });
};

const getStatusColor = (status) => {
    const colors = {
        scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        confirmed: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        canceled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        rejected: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        scheduled: 'Programada',
        confirmed: 'Confirmada',
        completed: 'Completada',
        canceled: 'Cancelada',
        rejected: 'Rechazada',
    };
    return texts[status] || status;
};
</script>

<template>
    <Head title="Información de la Cita"/>

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Información de la Cita #{{ appointment.id }}
                </h2>
                <a href="/appointments" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Volver al panel
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Header con Estado -->
                    <div class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Estado de la Cita
                            </h3>
                            <span :class="getStatusColor(appointment.status)" class="px-3 py-1 inline-flex text-sm font-semibold rounded-full">
                                {{ getStatusText(appointment.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Información del Doctor -->
                    <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Información del Doctor
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Nombre del Doctor
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ appointment.user?.name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Correo Electrónico
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ appointment.user?.email || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Cita -->
                    <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Detalles de la Cita
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Fecha y Hora
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ formatDateTime(appointment.appointment_date) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Paciente -->
                    <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Información del Paciente
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Nombre Completo
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ appointment.patient_name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Documento de Identidad
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ appointment.patient_document || 'N/A' }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Correo Electrónico
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ appointment.patient_email || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Razón de la Consulta -->
                    <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Razón de la Consulta
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-base text-gray-900 dark:text-white whitespace-pre-wrap">
                                {{ appointment.appointment_reason || 'No se especificó una razón para la consulta.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Información de Registro -->
                    <div class="px-6 py-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Información de Registro
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Fecha de Creación
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ formatDateTime(appointment.created_at) }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Última Actualización
                                </label>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ formatDateTime(appointment.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con Botón -->
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4">
                        <div class="flex justify-end">
                            <a href="/appointments" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Volver al Panel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
