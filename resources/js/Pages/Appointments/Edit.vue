<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    appointment: Object,
    daysAvailable: Array,
    appointmentDuration: Number,
});

const form = useForm({
    appointment_date: props.appointment.appointment_date,
    status: props.appointment.status,
});

const formatDateForInput = (dateString) => {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const dateTimeValue = ref(formatDateForInput(props.appointment.appointment_date));

const submit = () => {
    form.appointment_date = dateTimeValue.value.replace('T', ' ') + ':00';
    form.put(`/appointments/${props.appointment.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Editar Cita"/>

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Cita #{{ appointment.id }}
                </h2>
                <a href="/appointments" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Volver a la Lista
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Información del Usuario -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Usuario
                                </label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ appointment.user?.name || 'N/A' }}
                                </p>
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="mb-6">
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Fecha y Hora de la Cita
                                </label>
                                <input
                                    id="appointment_date"
                                    v-model="dateTimeValue"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"
                                    required
                                />
                                <p v-if="form.errors.appointment_date" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.appointment_date }}
                                </p>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Días disponibles: {{ daysAvailable.join(', ') }} | Horario: 8:00 AM - 6:00 PM
                                </p>
                            </div>

                            <!-- Estado -->
                            <div class="mb-6">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estado
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"
                                >
                                    <option value="scheduled">Programada</option>
                                    <option value="completed">Completada</option>
                                    <option value="canceled">Cancelada</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end gap-4">
                                <a href="/appointments" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    Cancelar
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
