<script setup>
import {Head, router} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { ref } from 'vue';

const props = defineProps({
    appointments: Array,
});

// Modal state
const modalState = ref({
    isOpen: false,
    type: 'cancel', // 'cancel' o 'reject'
    title: '',
    message: '',
    appointmentId: null,
    appointmentDate: null,
    isLoading: false
});

const openCancelModal = (appointment) => {
    modalState.value = {
        isOpen: true,
        type: 'cancel',
        title: 'Cancelar Cita',
        message: `¿Estás seguro de que deseas cancelar y eliminar permanentemente la cita del ${formatDateTime(appointment.appointment_date)}? Esta acción no se puede deshacer.`,
        appointmentId: appointment.id,
        appointmentDate: appointment.appointment_date,
        isLoading: false
    };
};

const openRejectModal = (appointment) => {
    modalState.value = {
        isOpen: true,
        type: 'reject',
        title: 'Rechazar Cita',
        message: `¿Estás seguro de que deseas rechazar la cita del ${formatDateTime(appointment.appointment_date)}? El paciente será notificado.`,
        appointmentId: appointment.id,
        appointmentDate: appointment.appointment_date,
        isLoading: false
    };
};

const openConfirmModal = (appointment) => {
    modalState.value = {
        isOpen: true,
        type: 'confirm',
        title: 'Confirmar Cita',
        message: `¿Deseas confirmar la cita del ${formatDateTime(appointment.appointment_date)}? El paciente será notificado de la confirmación.`,
        appointmentId: appointment.id,
        appointmentDate: appointment.appointment_date,
        isLoading: false
    };
};

const handleModalConfirm = () => {
    modalState.value.isLoading = true;

    const endpoint = modalState.value.type === 'cancel'
        ? `/appointments/${modalState.value.appointmentId}/cancel`
        : modalState.value.type === 'confirm'
        ? `/appointments/${modalState.value.appointmentId}/confirm`
        : `/appointments/${modalState.value.appointmentId}/reject`;

    router.patch(endpoint, {}, {
        preserveScroll: true,
        onSuccess: () => {
            const successMessage = modalState.value.type === 'cancel'
                ? 'Cita cancelada y eliminada exitosamente'
                : modalState.value.type === 'confirm'
                ? 'Cita confirmada exitosamente'
                : 'Cita rechazada exitosamente';

            closeModal();
            // Mostrar notificación de éxito
            alert(successMessage);
        },
        onError: (errors) => {
            modalState.value.isLoading = false;
            const errorMessage = modalState.value.type === 'cancel'
                ? 'Error al cancelar la cita'
                : modalState.value.type === 'confirm'
                ? 'Error al confirmar la cita'
                : 'Error al rechazar la cita';
            alert(errorMessage);
        },
        onFinish: () => {
            modalState.value.isLoading = false;
        }
    });
};const closeModal = () => {
    modalState.value = {
        isOpen: false,
        type: 'cancel',
        title: '',
        message: '',
        appointmentId: null,
        appointmentDate: null,
        isLoading: false
    };
};

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
    <Head title="Appointments"/>

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Appointments
                </h2>
                <div class="flex gap-2">
                    <a href="/appointments/create" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        + Nueva Cita
                    </a>
                    <a href="/appointments/calendar" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Ver Calendario
                    </a>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensaje cuando no hay citas -->
                <div v-if="appointments.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-12">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay citas disponibles</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando una nueva cita.</p>
                    </div>
                </div>

                <!-- Grid de tarjetas de citas -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="appointment in appointments"
                        :key="appointment.id"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300"
                    >
                        <!-- Header de la tarjeta -->
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-gray-700 dark:to-gray-600 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Cita #{{ appointment.id }}
                                </span>
                                <span :class="getStatusColor(appointment.status)" class="px-2 py-1 inline-flex text-xs font-semibold rounded-full">
                                    {{ getStatusText(appointment.status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="px-4 py-5">
                            <!-- Información del paciente -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Paciente
                                </label>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ appointment.patient_name || 'N/A' }}
                                </p>
                            </div>

                            <!-- Documento -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Documento
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ appointment.patient_document || 'N/A' }}
                                </p>
                            </div>

                            <!-- Fecha y hora -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Fecha y Hora
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ formatDateTime(appointment.appointment_date) }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer con acciones -->
                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-wrap gap-2">
                                <a
                                    :href="`/appointments/${appointment.id}`"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Info
                                </a>
                                <button
                                    v-if="appointment.status === 'scheduled'"
                                    @click="openConfirmModal(appointment)"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                >
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirmar
                                </button>
                                <button
                                    v-if="appointment.status === 'scheduled' || appointment.status === 'confirmed'"
                                    @click="openCancelModal(appointment)"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                >
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar
                                </button>
                                <button
                                    v-if="appointment.status === 'scheduled'"
                                    @click="openRejectModal(appointment)"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200"
                                >
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :is-open="modalState.isOpen"
            :type="modalState.type"
            :title="modalState.title"
            :message="modalState.message"
            :is-loading="modalState.isLoading"
            @close="closeModal"
            @confirm="handleModalConfirm"
        />
    </AppLayout>
</template>
