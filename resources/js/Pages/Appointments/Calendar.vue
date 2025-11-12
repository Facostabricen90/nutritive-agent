<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

const props = defineProps({
    daysAvailable: Array,
    appointmentDuration: Number,
});

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
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

// Fetch events from the server
async function fetchEvents(fetchInfo, successCallback, failureCallback) {
    try {
        const response = await axios.get('/appointments/available-slots', {
            params: {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
            },
        });
        
        const events = [
            ...response.data.availableSlots,
            ...response.data.bookedAppointments,
        ];
        
        successCallback(events);
    } catch (error) {
        console.error('Error fetching events:', error);
        failureCallback(error);
    }
}

// Handle date selection
function handleDateSelect(selectInfo) {
    const calendarApi = selectInfo.view.calendar;
    const startDate = selectInfo.start;
    
    // Formatear fecha para mostrar
    const formattedDate = startDate.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    if (confirm(`¿Deseas agendar una cita para ${formattedDate}?`)) {
        // Crear appointment
        axios.post('/appointments', {
            appointment_date: startDate.toISOString().slice(0, 19).replace('T', ' '),
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.data.success) {
                alert(response.data.message);
                calendarApi.refetchEvents();
            }
        })
        .catch(error => {
            if (error.response && error.response.data) {
                const errors = error.response.data.errors;
                if (errors && errors.appointment_date) {
                    alert(errors.appointment_date[0]);
                } else {
                    alert(error.response.data.message || 'Error al crear la cita');
                }
            } else {
                alert('Error al crear la cita');
            }
            console.error('Error creating appointment:', error);
        });
    }
    
    calendarApi.unselect();
}

// Handle event click
function handleEventClick(clickInfo) {
    const eventType = clickInfo.event.extendedProps.type;
    
    if (eventType === 'available') {
        const startDate = clickInfo.event.start;
        const formattedDate = startDate.toLocaleString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        if (confirm(`¿Deseas agendar esta cita para ${formattedDate}?`)) {
            axios.post('/appointments', {
                appointment_date: startDate.toISOString().slice(0, 19).replace('T', ' '),
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (response.data.success) {
                    alert(response.data.message);
                    clickInfo.view.calendar.refetchEvents();
                }
            })
            .catch(error => {
                if (error.response && error.response.data) {
                    const errors = error.response.data.errors;
                    if (errors && errors.appointment_date) {
                        alert(errors.appointment_date[0]);
                    } else {
                        alert(error.response.data.message || 'Error al crear la cita');
                    }
                } else {
                    alert('Error al crear la cita');
                }
                console.error('Error creating appointment:', error);
            });
        }
    } else if (eventType === 'booked') {
        const appointmentId = clickInfo.event.id;
        const status = clickInfo.event.extendedProps.status;
        
        if (status === 'scheduled') {
            if (confirm('¿Deseas cancelar esta cita?')) {
                axios.patch(`/appointments/${appointmentId}/cancel`, {}, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        alert(response.data.message);
                        clickInfo.view.calendar.refetchEvents();
                    }
                })
                .catch(error => {
                    alert('Error al cancelar la cita');
                    console.error('Error canceling appointment:', error);
                });
            }
        } else {
            alert(`Esta cita ya está ${status === 'canceled' ? 'cancelada' : 'completada'}.`);
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            <p class="mb-2">
                                <strong>Días disponibles:</strong> {{ daysAvailable.join(', ') }}
                            </p>
                            <p class="mb-2">
                                <strong>Duración por cita:</strong> {{ appointmentDuration }} minutos
                            </p>
                            <p class="mb-4">
                                <strong>Horario:</strong> 8:00 AM - 6:00 PM
                            </p>
                            <div class="flex gap-4">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                    <span>Disponible</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                    <span>Reservado</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="calendar-wrapper">
                            <FullCalendar :options="calendarOptions" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* FullCalendar custom styles */
.calendar-wrapper {
    --fc-border-color: #374151;
    --fc-button-bg-color: #4b5563;
    --fc-button-border-color: #4b5563;
    --fc-button-hover-bg-color: #6b7280;
    --fc-button-hover-border-color: #6b7280;
    --fc-button-active-bg-color: #374151;
    --fc-button-active-border-color: #374151;
}

.dark .calendar-wrapper {
    --fc-border-color: #4b5563;
}

.calendar-wrapper :deep(.fc) {
    font-family: inherit;
}

.calendar-wrapper :deep(.fc-theme-standard td),
.calendar-wrapper :deep(.fc-theme-standard th) {
    border-color: var(--fc-border-color);
}

.calendar-wrapper :deep(.fc-button) {
    background-color: var(--fc-button-bg-color) !important;
    border-color: var(--fc-button-border-color) !important;
}

.calendar-wrapper :deep(.fc-button:hover) {
    background-color: var(--fc-button-hover-bg-color) !important;
}

.calendar-wrapper :deep(.fc-button-active) {
    background-color: var(--fc-button-active-bg-color) !important;
}

.calendar-wrapper :deep(.available-slot) {
    cursor: pointer;
}

.calendar-wrapper :deep(.available-slot:hover) {
    opacity: 0.8;
}

.dark .calendar-wrapper :deep(.fc) {
    color: #e5e7eb;
}

.dark .calendar-wrapper :deep(.fc-col-header-cell) {
    background-color: #374151;
}

.dark .calendar-wrapper :deep(.fc-daygrid-day) {
    background-color: #1f2937;
}

.dark .calendar-wrapper :deep(.fc-timegrid-slot) {
    background-color: #1f2937;
}
</style>
