<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';
import AppointmentModal from '@/Components/AppointmentModal.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    daysAvailable: Array,
    appointmentDuration: Number,
});

const isLoading = ref(false);
const calendarApi = ref(null);
const calendarRef = ref(null);

function normalizeDate(dateStr) {
    const d = new Date(dateStr);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:00`;
}

// Modal state
const modalState = ref({
    isOpen: false,
    type: 'book',
    title: '',
    message: '',
    appointmentDate: null,
    appointmentId: null,
    isLoading: false
});

const toast = ref(null);

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    timeZone: 'America/Bogota',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    slotMinTime: '08:00:00',
    slotMaxTime: '18:00:00',
    slotDuration: `00:${props.appointmentDuration}:00`,
    allDaySlot: false,
    selectable: true,
    selectMirror: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    editable: false,
    eventStartEditable: false,
    eventDurationEditable: false,
    events: fetchEvents,
    hiddenDays: getHiddenDays(),
    businessHours: {
        daysOfWeek: getDaysOfWeek(),
        startTime: '08:00',
        endTime: '18:00',
    },
    height: 'auto',
    nowIndicator: true,
    eventDisplay: 'block',
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false
    },
    dayHeaderFormat: { weekday: 'short', month: 'numeric', day: 'numeric' },
    slotLabelFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false
    },
    eventClassNames: function(arg) {
        const type = arg.event.extendedProps.type;
        const status = arg.event.extendedProps.status;
        return [`event-${type}`, status ? `event-${status}` : ''];
    },
    eventContent: function(arg) {
        const type = arg.event.extendedProps.type;
        const status = arg.event.extendedProps.status;

        let icon = '';
        let bgColor = '';
        let textColor = '';
        let title = arg.event.title;

        if (type === 'available') {
            icon = '🟢';
            bgColor = 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800';
            textColor = 'text-emerald-800 dark:text-emerald-200';
            title = 'Disponible';
        } else if (type === 'booked') {
            if (status === 'scheduled') {
                icon = '🔴';
                bgColor = 'bg-red-100 dark:bg-red-900/30 border-red-200 dark:border-red-800';
                textColor = 'text-red-800 dark:text-red-200';
                title = 'Reservado';
            } else if (status === 'canceled') {
                icon = '⚪';
                bgColor = 'bg-gray-100 dark:bg-gray-900/30 border-gray-200 dark:border-gray-800';
                textColor = 'text-gray-600 dark:text-gray-400';
                title = 'Cancelado';
            } else if (status === 'completed') {
                icon = '✅';
                bgColor = 'bg-blue-100 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800';
                textColor = 'text-blue-800 dark:text-blue-200';
                title = 'Completado';
            }
        }

        return {
            html: `
                <div class="flex items-center gap-1 p-2 rounded-md border ${bgColor} ${textColor} text-xs font-medium shadow-sm">
                    <span class="text-sm">${icon}</span>
                    <span class="truncate font-semibold">${title}</span>
                </div>
            `
        };
    },
    loading: function(isLoading) {
        // Handle loading state if needed
    },
    eventDidMount: function(info) {
        // Add tooltip or additional styling
        if (info.event.extendedProps.type === 'available') {
            info.el.style.cursor = 'pointer';
        }
    },
});

// Convert day names to day numbers (0 = Sunday, 1 = Monday, etc.)
function getDaysOfWeek() {
    const dayMap = {
        'Sunday': 0,
        'Monday': 1,
        'Tuesday': 2,
        'Wednesday': 3,
        'Thursday': 4,
        'Friday': 5,
        'Saturday': 6,
    };
    return props.daysAvailable.map(day => dayMap[day]);
}

// Get days to hide (inverse of available days)
function getHiddenDays() {
    const allDays = [0, 1, 2, 3, 4, 5, 6];
    const availableDays = getDaysOfWeek();
    return allDays.filter(day => !availableDays.includes(day));
}

function onCalendarReady() {
    calendarApi.value = calendarRef.value?.getApi();
}

function formatForBackend(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

function handleModalConfirm(data) {
    modalState.value.isLoading = true;
    
    if (data.type === 'book') {
        const appointmentData = {
            // CAMBIADO: Usar directamente la fecha del modal, que ahora es una cadena de texto.
            appointment_date: data.date, 
        };
        
        axios.post('/appointments', appointmentData, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.data.success) {
                toast.value?.success('¡Cita Agendada!', response.data.message);
                calendarApi.value?.refetchEvents();
                closeModal();
            }
        })
        .catch(error => {
            let errorMessage = 'Error al crear la cita';
            
            if (error.response && error.response.data) {
                const errors = error.response.data.errors;
                if (errors && errors.appointment_date) {
                    errorMessage = errors.appointment_date[0];
                } else {
                    errorMessage = error.response.data.message || errorMessage;
                }
            }
            
            toast.value?.error('Error al Agendar', errorMessage);
            console.error('Error creating appointment:', error);
        })
        .finally(() => {
            modalState.value.isLoading = false;
        });
    } else if (data.type === 'cancel') {
        axios.patch(`/appointments/${modalState.value.appointmentId}/cancel`, {}, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.data.success) {
                toast.value?.success('Cita Cancelada', response.data.message);
                calendarApi.value?.refetchEvents();
                closeModal();
            }
        })
        .catch(error => {
            const errorMessage = error.response?.data?.message || 'Error al cancelar la cita';
            toast.value?.error('Error al Cancelar', errorMessage);
            console.error('Error canceling appointment:', error);
        })
        .finally(() => {
            modalState.value.isLoading = false;
        });
    }
}

function closeModal() {
    modalState.value = {
        isOpen: false,
        type: 'book',
        title: '',
        message: '',
        appointmentDate: null,
        appointmentId: null,
        isLoading: false
    };
}

async function fetchEvents(fetchInfo, successCallback, failureCallback) {
    isLoading.value = true;
    try {
        const response = await axios.get('/appointments/available-slots', {
            params: {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
            },
        });
        
        // CORRECCIÓN: El backend ahora devuelve directamente el array de eventos.
        // Antes devolvía un objeto { availableSlots: [], bookedAppointments: [] }, pero eso cambió.
        const events = response.data;
        
        successCallback(events);
    } catch (error) {
        console.error('Error fetching events:', error);
        failureCallback(error);
    } finally {
        isLoading.value = false;
    }
}

function handleDateSelect(selectInfo) {
    const calendarApi = selectInfo.view.calendar;
    // CAMBIADO: Usar la cadena de texto como la única fuente de verdad.
    const startDateString = selectInfo.startStr; 

    const normalized = normalizeDate(startDateString);

    // CAMBIADO: Crear un nuevo objeto Date a partir de la cadena para formatear la visualización.
    // Esto asegura que la hora de inicio es la correcta.
    const displayDate = new Date(startDateString);
    const formattedDate = displayDate.toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'America/Bogota',
    });
    
    modalState.value = {
        isOpen: true,
        type: 'book',
        title: 'Agendar Nueva Cita',
        message: `¿Deseas agendar una cita para el ${formattedDate}?`,
        // Se guarda la cadena de texto para enviar al backend.
        appointmentDate: normalized,
        appointmentId: null,
        isLoading: false
    };
    
    calendarApi.unselect();
}

function handleEventClick(clickInfo) {
    const eventType = clickInfo.event.extendedProps.type;
    
    if (eventType === 'available') {
        // CAMBIADO: Usar 'startStr' en lugar de 'start' para evitar errores de zona horaria.
        const startDateString = clickInfo.event.startStr;
        const displayDate = new Date(startDateString);

        const formattedDate = displayDate.toLocaleString('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'America/Bogota',
        });
        
        modalState.value = {
            isOpen: true,
            type: 'book',
            title: 'Agendar Nueva Cita',
            message: `¿Deseas agendar una cita para el ${formattedDate}?`,
            // CAMBIADO: Guardar la cadena de texto correcta.
            appointmentDate: startDateString,
            appointmentId: null,
            isLoading: false
        };

    } else if (eventType === 'booked') {
        const appointmentId = clickInfo.event.id;
        const status = clickInfo.event.extendedProps.status;
        // CAMBIADO: Usar 'startStr' para la visualización.
        const startDateString = clickInfo.event.startStr;
        const displayDate = new Date(startDateString);

        const formattedDate = displayDate.toLocaleString('es-CO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'America/Bogota',
        });
        
        if (status === 'scheduled') {
            modalState.value = {
                isOpen: true,
                type: 'cancel',
                title: 'Cancelar Cita',
                message: `¿Estás seguro de que deseas cancelar la cita del ${formattedDate}?`,
                // CAMBIADO: Guardar la cadena de texto.
                appointmentDate: startDateString,
                appointmentId: appointmentId,
                isLoading: false
            };
        } else {
            const statusText = status === 'canceled' ? 'cancelada' : 'completada';
            toast.value?.info(
                'Información de Cita',
                `Esta cita del ${formattedDate} ya está ${statusText}.`
            );
        }
    }
}
</script>

<template>
    <Head title="Calendario de Citas"/>

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Calendario de Citas
                </h2>
                <a href="/appointments" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Ver Lista
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Días Disponibles</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ daysAvailable.join(', ') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Duración por Cita</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ appointmentDuration }} minutos</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Horario de Atención</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">8:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Legend -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Leyenda</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-emerald-100 dark:bg-emerald-900/30 rounded flex items-center justify-center">
                                        <span class="text-xs">🟢</span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Disponible</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-red-100 dark:bg-red-900/30 rounded flex items-center justify-center">
                                        <span class="text-xs">🔴</span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Reservado</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-gray-100 dark:bg-gray-900/30 rounded flex items-center justify-center">
                                        <span class="text-xs">⚪</span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Cancelado</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/30 rounded flex items-center justify-center">
                                        <span class="text-xs">✅</span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Completado</span>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar -->
                        <div class="calendar-wrapper rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 relative">
                            <div v-if="isLoading" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-10">
                                <div class="flex items-center space-x-2">
                                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Cargando calendario...</span>
                                </div>
                            </div>
                            <FullCalendar ref="calendarRef" :options="calendarOptions" @ready="onCalendarReady" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Modal -->
        <AppointmentModal
            :is-open="modalState.isOpen"
            :type="modalState.type"
            :title="modalState.title"
            :message="modalState.message"
            :appointment-date="modalState.appointmentDate"
            :is-loading="modalState.isLoading"
            @close="closeModal"
            @confirm="handleModalConfirm"
        />
        <ToastNotification ref="toast" />
    </AppLayout>
</template>

<style>
/* FullCalendar custom styles */
.calendar-wrapper {
    --fc-border-color: #e5e7eb;
    --fc-button-bg-color: #3b82f6;
    --fc-button-border-color: #3b82f6;
    --fc-button-hover-bg-color: #2563eb;
    --fc-button-hover-border-color: #2563eb;
    --fc-button-active-bg-color: #1d4ed8;
    --fc-button-active-border-color: #1d4ed8;
    --fc-today-bg-color: #fef3c7;
    --fc-now-indicator-color: #ef4444;
    --fc-page-bg-color: #ffffff;
    --fc-neutral-bg-color: #f9fafb;
    --fc-list-event-hover-bg-color: #f3f4f6;
}

.dark .calendar-wrapper {
    --fc-border-color: #374151;
    --fc-page-bg-color: #1f2937;
    --fc-neutral-bg-color: #111827;
    --fc-list-event-hover-bg-color: #374151;
    --fc-today-bg-color: #374151;
}

.calendar-wrapper :deep(.fc) {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.calendar-wrapper :deep(.fc-theme-standard td),
.calendar-wrapper :deep(.fc-theme-standard th) {
    border-color: var(--fc-border-color);
}

.calendar-wrapper :deep(.fc-button) {
    background-color: var(--fc-button-bg-color) !important;
    border-color: var(--fc-button-border-color) !important;
    border-radius: 0.375rem !important;
    font-weight: 500 !important;
    text-transform: none !important;
    padding: 0.5rem 1rem !important;
    transition: all 0.2s ease-in-out !important;
}

.calendar-wrapper :deep(.fc-button:hover) {
    background-color: var(--fc-button-hover-bg-color) !important;
    border-color: var(--fc-button-hover-border-color) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
}

.calendar-wrapper :deep(.fc-button-active) {
    background-color: var(--fc-button-active-bg-color) !important;
    border-color: var(--fc-button-active-border-color) !important;
}

.calendar-wrapper :deep(.fc-button:disabled) {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
}

.calendar-wrapper :deep(.fc-header-toolbar) {
    margin-bottom: 1.5rem !important;
    flex-wrap: wrap !important;
    gap: 0.5rem !important;
}

.calendar-wrapper :deep(.fc-toolbar-title) {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #111827 !important;
}

.dark .calendar-wrapper :deep(.fc-toolbar-title) {
    color: #f9fafb !important;
}

.calendar-wrapper :deep(.fc-col-header-cell) {
    background-color: #f9fafb !important;
    border-bottom: 2px solid var(--fc-border-color) !important;
    font-weight: 600 !important;
    padding: 0.75rem !important;
}

.dark .calendar-wrapper :deep(.fc-col-header-cell) {
    background-color: #374151 !important;
}

.calendar-wrapper :deep(.fc-daygrid-day) {
    background-color: var(--fc-page-bg-color) !important;
    transition: background-color 0.2s ease-in-out !important;
}

.calendar-wrapper :deep(.fc-daygrid-day:hover) {
    background-color: var(--fc-neutral-bg-color) !important;
}

.calendar-wrapper :deep(.fc-timegrid-slot) {
    background-color: var(--fc-page-bg-color) !important;
    border-color: var(--fc-border-color) !important;
    height: 3rem !important;
}

.calendar-wrapper :deep(.fc-timegrid-slot:hover) {
    background-color: var(--fc-neutral-bg-color) !important;
}

.calendar-wrapper :deep(.fc-timegrid-axis) {
    border-color: var(--fc-border-color) !important;
}

.calendar-wrapper :deep(.fc-timegrid-divider) {
    border-color: var(--fc-border-color) !important;
}

.calendar-wrapper :deep(.fc-event) {
    border-radius: 0.375rem !important;
    border: none !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
    transition: all 0.2s ease-in-out !important;
}

.calendar-wrapper :deep(.fc-event:hover) {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
}

.calendar-wrapper :deep(.fc-event-main) {
    padding: 0 !important;
}

.calendar-wrapper :deep(.event-available) {
    cursor: pointer !important;
}

.calendar-wrapper :deep(.event-available:hover) {
    opacity: 0.9 !important;
}

.calendar-wrapper :deep(.fc-highlight) {
    background-color: rgba(59, 130, 246, 0.1) !important;
}

.calendar-wrapper :deep(.fc-day-today) {
    background-color: var(--fc-today-bg-color) !important;
}

.calendar-wrapper :deep(.fc-now-indicator-arrow) {
    border-left-color: var(--fc-now-indicator-color) !important;
    border-width: 8px 0 8px 8px !important;
}

.calendar-wrapper :deep(.fc-now-indicator-line) {
    border-color: var(--fc-now-indicator-color) !important;
}

/* Scrollbar styling */
.calendar-wrapper :deep(.fc-scroller) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.dark .calendar-wrapper :deep(.fc-scroller) {
    scrollbar-color: #4b5563 #1f2937;
}

.calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar) {
    width: 8px;
}

.calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-track) {
    background: #f1f5f9;
    border-radius: 4px;
}

.dark .calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-track) {
    background: #1f2937;
}

.calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-thumb) {
    background: #cbd5e1;
    border-radius: 4px;
}

.dark .calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-thumb) {
    background: #4b5563;
}

.calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-thumb:hover) {
    background: #94a3b8;
}

.dark .calendar-wrapper :deep(.fc-scroller::-webkit-scrollbar-thumb:hover) {
    background: #6b7280;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .calendar-wrapper :deep(.fc-header-toolbar) {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .calendar-wrapper :deep(.fc-toolbar-chunk) {
        display: flex !important;
        justify-content: center !important;
        margin-bottom: 0.5rem !important;
    }

    .calendar-wrapper :deep(.fc-button) {
        flex: 1 !important;
        margin: 0 0.125rem !important;
    }
}
</style>
