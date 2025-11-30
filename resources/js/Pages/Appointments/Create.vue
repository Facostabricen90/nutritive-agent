<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { CalendarIcon, ClockIcon, UserIcon, IdentificationIcon, EnvelopeIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    daysAvailable: Array,
    appointmentDuration: Number,
    userId: Number,
});

const toast = ref(null);
const isLoadingSlots = ref(false);
const availableSlots = ref([]);
const selectedDate = ref('');
const selectedTime = ref('');

const form = useForm({
    appointment_date: '',
    patient_name: '',
    patient_document: '',
    patient_email: '',
    appointment_reason: '',
    user_id: props.userId,
});

// Generate available dates (next 30 days, filtered by daysAvailable)
const availableDates = computed(() => {
    const dates = [];
    const today = new Date();
    const daysMap = {
        'Monday': 1, 'Tuesday': 2, 'Wednesday': 3, 'Thursday': 4, 'Friday': 5, 'Saturday': 6, 'Sunday': 0,
        'Lunes': 1, 'Martes': 2, 'Miércoles': 3, 'Jueves': 4, 'Viernes': 5, 'Sábado': 6, 'Domingo': 0
    };

    const allowedDays = props.daysAvailable.map(day => daysMap[day]).filter(d => d !== undefined);

    for (let i = 0; i < 30; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);

        if (allowedDays.includes(date.getDay())) {
            dates.push({
                value: date.toISOString().split('T')[0],
                label: date.toLocaleDateString('es-CO', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                })
            });
        }
    }

    return dates;
});

// Watch for date changes to load available slots
watch(selectedDate, async (date) => {
    selectedTime.value = '';
    availableSlots.value = [];

    if (!date) return;

    isLoadingSlots.value = true;

    try {
        const startDate = new Date(date);
        const endDate = new Date(date);
        endDate.setDate(endDate.getDate() + 1);

        const response = await axios.get('/appointments/available-slots', {
            params: {
                start: startDate.toISOString().split('T')[0],
                end: endDate.toISOString().split('T')[0],
                user_id: props.userId,
            },
        });

        // Filter only available slots for selected date
        availableSlots.value = response.data
            .filter(slot => {
                const slotDate = slot.start.split(' ')[0];
                return slotDate === date && (slot.type === 'available' || slot.extendedProps?.type === 'available');
            })
            .map(slot => ({
                value: slot.start,
                label: formatTime(slot.start)
            }))
            .sort((a, b) => a.value.localeCompare(b.value));

    } catch (error) {
        console.error('Error fetching slots:', error);
        toast.value?.error('Error', 'No se pudieron cargar los horarios disponibles.');
    } finally {
        isLoadingSlots.value = false;
    }
});

function formatTime(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });
}

const submit = async () => {
    if (!selectedTime.value) {
        toast.value?.error('Error', 'Por favor selecciona una fecha y hora.');
        return;
    }

    // Construct full datetime
    form.appointment_date = selectedTime.value;

    form.post('/appointments', {
        preserveScroll: true,
        onSuccess: () => {
            toast.value?.success('¡Éxito!', 'La cita se ha creado exitosamente.');

            // Redirect to appointments index
            setTimeout(() => {
                window.location.href = '/appointments';
            }, 1500);
        },
        onError: (errors) => {
            if (errors.appointment_date && (errors.appointment_date.includes('already been taken') || errors.appointment_date.includes('ya está reservado'))) {
                toast.value?.error('Cita No Disponible', 'Este horario ya fue reservado. Por favor selecciona otro horario.');
            } else {
                const errorMessage = Object.values(errors).flat()[0] || 'No se pudo crear la cita. Verifica los datos ingresados.';
                toast.value?.error('Error', errorMessage);
            }
        }
    });
};
</script>

<template>
    <Head title="Nueva Cita"/>

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Agendar Nueva Cita
                </h2>
                <a href="/appointments" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-teal-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-green-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                    Ver Mis Citas
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Card -->
                <div class="mb-8 bg-gradient-to-r from-green-50 to-teal-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-lg p-6 border border-green-100 dark:border-gray-600">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center">
                                <CalendarIcon class="w-7 h-7 text-white" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                Sistema de Agendamiento Rápido
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold">Días:</span> {{ daysAvailable.join(', ') }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold">Horario:</span> 8:00 AM - 6:00 PM
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold">Duración:</span> {{ appointmentDuration }} min
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl">
                    <form @submit.prevent="submit" class="p-8 space-y-8">

                        <!-- Date and Time Selection -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <ClockIcon class="w-6 h-6 mr-3 text-green-500" />
                                    Fecha y Hora de la Cita
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Selecciona cuándo deseas realizar la consulta
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Date -->
                                <div class="space-y-2">
                                    <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        📅 Fecha de la Cita
                                    </label>
                                    <select
                                        id="date"
                                        v-model="selectedDate"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                        required
                                    >
                                        <option value="">Selecciona una fecha</option>
                                        <option v-for="date in availableDates" :key="date.value" :value="date.value">
                                            {{ date.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Time -->
                                <div class="space-y-2">
                                    <label for="time" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        ⏰ Hora de la Cita
                                    </label>
                                    <select
                                        id="time"
                                        v-model="selectedTime"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                        required
                                        :disabled="!selectedDate || isLoadingSlots"
                                    >
                                        <option value="">{{ isLoadingSlots ? 'Cargando horarios...' : 'Selecciona una hora' }}</option>
                                        <option v-for="slot in availableSlots" :key="slot.value" :value="slot.value">
                                            {{ slot.label }}
                                        </option>
                                    </select>
                                    <p v-if="selectedDate && !isLoadingSlots && availableSlots.length === 0" class="text-sm text-amber-600 dark:text-amber-400 flex items-center mt-2">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        No hay horarios disponibles para esta fecha
                                    </p>
                                </div>
                            </div>

                            <p v-if="form.errors.appointment_date" class="text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                {{ form.errors.appointment_date }}
                            </p>
                        </div>

                        <!-- Patient Information -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <UserIcon class="w-6 h-6 mr-3 text-teal-500" />
                                    Información del Paciente
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Datos personales del paciente que recibirá la consulta
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Patient Name -->
                                <div class="space-y-2">
                                    <label for="patient_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                        <UserIcon class="w-4 h-4 mr-1 text-gray-400" />
                                        Nombre Completo
                                    </label>
                                    <input
                                        id="patient_name"
                                        v-model="form.patient_name"
                                        type="text"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                        placeholder="Ej: Juan Pérez García"
                                        required
                                    />
                                    <p v-if="form.errors.patient_name" class="text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.patient_name }}
                                    </p>
                                </div>

                                <!-- Patient Document -->
                                <div class="space-y-2">
                                    <label for="patient_document" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                        <IdentificationIcon class="w-4 h-4 mr-1 text-gray-400" />
                                        Cédula / Documento
                                    </label>
                                    <input
                                        id="patient_document"
                                        v-model="form.patient_document"
                                        type="text"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                        placeholder="Ej: 1234567890"
                                        required
                                    />
                                    <p v-if="form.errors.patient_document" class="text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.patient_document }}
                                    </p>
                                </div>

                                <!-- Patient Email -->
                                <div class="md:col-span-2 space-y-2">
                                    <label for="patient_email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                        <EnvelopeIcon class="w-4 h-4 mr-1 text-gray-400" />
                                        Correo Electrónico
                                    </label>
                                    <input
                                        id="patient_email"
                                        v-model="form.patient_email"
                                        type="email"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                        placeholder="correo@ejemplo.com"
                                        required
                                    />
                                    <p v-if="form.errors.patient_email" class="text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.patient_email }}
                                    </p>
                                </div>
                            </div>

                            <!-- Appointment Reason -->
                            <div class="space-y-2">
                                <label for="appointment_reason" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                    <ChatBubbleLeftRightIcon class="w-4 h-4 mr-1 text-gray-400" />
                                    Motivo de la Consulta
                                </label>
                                <textarea
                                    id="appointment_reason"
                                    v-model="form.appointment_reason"
                                    rows="4"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 transition duration-150"
                                    placeholder="Describe brevemente el motivo de la consulta..."
                                    required
                                ></textarea>
                                <p v-if="form.errors.appointment_reason" class="text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.appointment_reason }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a
                                href="/appointments"
                                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing || isLoadingSlots"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-teal-500 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-green-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Creando Cita...' : 'Agendar Cita' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <ToastNotification ref="toast" />
    </AppLayout>
</template>
