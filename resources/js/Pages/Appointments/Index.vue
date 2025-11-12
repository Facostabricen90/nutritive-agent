<script setup>
import {Head, router} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    appointments: Array,
});

const cancelAppointment = (appointment) => {
    if (confirm(`¿Estás seguro de que deseas cancelar la cita del ${formatDateTime(appointment.appointment_date)}?`)) {
        router.patch(`/appointments/${appointment.id}/cancel`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                alert('Cita cancelada exitosamente');
            },
            onError: () => {
                alert('Error al cancelar la cita');
            }
        });
    }
};

const deleteAppointment = (appointment) => {
    if (confirm(`¿Estás seguro de que deseas eliminar permanentemente la cita del ${formatDateTime(appointment.appointment_date)}?`)) {
        router.delete(`/appointments/${appointment.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                alert('Cita eliminada exitosamente');
            },
            onError: () => {
                alert('Error al eliminar la cita');
            }
        });
    }
};

const formatDateTime = (dateTime) => {
    const date = new Date(dateTime);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusColor = (status) => {
    const colors = {
        scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        canceled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        scheduled: 'Programada',
        completed: 'Completada',
        canceled: 'Cancelada',
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
    <div v-if="appointments.length === 0" class="text-center py-4 text-gray-600 dark:text-gray-400">
        No hay citas disponibles.
    </div>
    <div v-else class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                ID
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Usuario
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Fecha y Hora
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Estado
            </th>
            <th scope="col"
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Acciones
            </th>
        </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="appointment in appointments" :key="appointment.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ appointment.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ appointment.user?.name || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                {{ formatDateTime(appointment.appointment_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusColor(appointment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ getStatusText(appointment.status) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                    <a :href="`/appointments/${appointment.id}/edit`" 
                       v-if="appointment.status === 'scheduled'"
                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Editar
                    </a>
                    <button @click="cancelAppointment(appointment)"
                            v-if="appointment.status === 'scheduled'"
                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                        Cancelar
                    </button>
                    <button @click="deleteAppointment(appointment)"
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                        Eliminar
                    </button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>