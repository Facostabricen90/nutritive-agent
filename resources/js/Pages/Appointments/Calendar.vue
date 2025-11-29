<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import AppointmentModal from '@/Components/AppointmentModal.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    daysAvailable: Array,
    appointmentDuration: Number,
});

const isLoading = ref(false);
const toast = ref(null);

// State for navigation
const currentMonth = ref(new Date());
const selectedDate = ref(new Date());
const events = ref([]);

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

// --- Date Helpers ---

const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const dayNames = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];

function getDaysInMonth(date) {
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
}

function getFirstDayOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
}

function normalizeDate(dateStr) {
    const d = new Date(dateStr);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:00`;
}

function isSameDay(d1, d2) {
    return d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate();
}

function addDays(date, days) {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}

function startOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Adjust when day is Sunday
    return new Date(d.setDate(diff));
}

// --- Computed Properties ---

const currentMonthName = computed(() => {
    return `${monthNames[currentMonth.value.getMonth()]} de ${currentMonth.value.getFullYear()}`;
});

const miniCalendarGrid = computed(() => {
    const days = [];
    const firstDay = getFirstDayOfMonth(currentMonth.value);
    const totalDays = getDaysInMonth(currentMonth.value);

    // Previous month padding
    const prevMonth = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 0);
    const prevMonthDays = prevMonth.getDate();
    for (let i = firstDay - 1; i >= 0; i--) {
        days.push({
            date: new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, prevMonthDays - i),
            isCurrentMonth: false
        });
    }

    // Current month days
    for (let i = 1; i <= totalDays; i++) {
        days.push({
            date: new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), i),
            isCurrentMonth: true
        });
    }

    // Next month padding
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
        days.push({
            date: new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, i),
            isCurrentMonth: false
        });
    }

    return days;
});

const weekDates = computed(() => {
    // Show 5 days (Mon-Fri) or 7 days depending on preference. 
    // Image shows Mon-Fri. Let's show Mon-Fri for now, or maybe dynamic based on daysAvailable?
    // The image shows "LUN 6" to "VIE 10".
    const start = startOfWeek(selectedDate.value);
    const days = [];
    // Assuming Mon-Fri (1-5)
    for (let i = 0; i < 5; i++) {
        days.push(addDays(start, i));
    }
    return days;
});

// --- Actions ---

function prevMonth() {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1);
}

function nextMonth() {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1);
}

function selectDate(date) {
    selectedDate.value = date;
    // Also update current month if selected date is in another month
    if (date.getMonth() !== currentMonth.value.getMonth()) {
        currentMonth.value = new Date(date.getFullYear(), date.getMonth(), 1);
    }
}

function prevWeek() {
    selectedDate.value = addDays(selectedDate.value, -7);
    if (selectedDate.value.getMonth() !== currentMonth.value.getMonth()) {
        currentMonth.value = new Date(selectedDate.value.getFullYear(), selectedDate.value.getMonth(), 1);
    }
}

function nextWeek() {
    selectedDate.value = addDays(selectedDate.value, 7);
    if (selectedDate.value.getMonth() !== currentMonth.value.getMonth()) {
        currentMonth.value = new Date(selectedDate.value.getFullYear(), selectedDate.value.getMonth(), 1);
    }
}

// --- Data Fetching ---

async function fetchEvents() {
    isLoading.value = true;
    const start = weekDates.value[0];
    const end = addDays(weekDates.value[weekDates.value.length - 1], 1); // End of the last day

    // Format for API: YYYY-MM-DD
    const startStr = start.toISOString().split('T')[0];
    const endStr = end.toISOString().split('T')[0];

    try {
        const response = await axios.get('/appointments/available-slots', {
            params: {
                start: startStr,
                end: endStr,
            },
        });
        events.value = response.data;
    } catch (error) {
        console.error('Error fetching events:', error);
        toast.value?.error('Error', 'No se pudieron cargar los horarios.');
    } finally {
        isLoading.value = false;
    }
}

// Watch for week changes to refetch
watch(weekDates, () => {
    fetchEvents();
}, { immediate: true });

function getSlotsForDay(date) {
    const dateStr = date.toISOString().split('T')[0];
    // Filter events that start on this day
    // Note: event.start might be full ISO string or YYYY-MM-DD HH:mm:ss
    return events.value.filter(event => {
        const eventStart = event.start.replace(' ', 'T'); // Handle SQL format if needed
        return eventStart.startsWith(dateStr);
    }).sort((a, b) => a.start.localeCompare(b.start));
}

function formatTime(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });
}

function getDayHeader(date) {
    const dayName = date.toLocaleDateString('es-CO', { weekday: 'short' }).toUpperCase().replace('.', '');
    const dayNum = date.getDate();
    return { name: dayName, num: dayNum };
}

// --- Event Handling ---

function handleSlotClick(event) {
    const type = event.extendedProps?.type || event.type; // Adapt to FullCalendar or raw structure
    const status = event.extendedProps?.status || event.status;
    const startDateString = event.start;
    const displayDate = new Date(startDateString);

    const formattedDate = displayDate.toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'America/Bogota',
    });

    if (type === 'available') {
        modalState.value = {
            isOpen: true,
            type: 'book',
            title: 'Agendar Nueva Cita',
            message: `¿Deseas agendar una cita para el ${formattedDate}?`,
            appointmentDate: startDateString,
            appointmentId: null,
            isLoading: false
        };
    } else if (type === 'booked') {
        if (status === 'scheduled') {
            modalState.value = {
                isOpen: true,
                type: 'cancel',
                title: 'Cancelar Cita',
                message: `¿Estás seguro de que deseas cancelar la cita del ${formattedDate}?`,
                appointmentDate: startDateString,
                appointmentId: event.id,
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

function handleModalConfirm(data) {
    modalState.value.isLoading = true;

    if (data.type === 'book') {
        const appointmentData = {
            appointment_date: data.date,
        };

        axios.post('/appointments', appointmentData)
            .then(response => {
                if (response.data.success) {
                    toast.value?.success('¡Cita Agendada!', response.data.message);
                    fetchEvents();
                    closeModal();
                }
            })
            .catch(error => {
                let errorMessage = 'Error al crear la cita';
                if (error.response?.data?.errors?.appointment_date) {
                    errorMessage = error.response.data.errors.appointment_date[0];
                } else {
                    errorMessage = error.response?.data?.message || errorMessage;
                }
                toast.value?.error('Error al Agendar', errorMessage);
            })
            .finally(() => {
                modalState.value.isLoading = false;
            });
    } else if (data.type === 'cancel') {
        axios.patch(`/appointments/${modalState.value.appointmentId}/cancel`, {})
            .then(response => {
                if (response.data.success) {
                    toast.value?.success('Cita Cancelada', response.data.message);
                    fetchEvents();
                    closeModal();
                }
            })
            .catch(error => {
                const errorMessage = error.response?.data?.message || 'Error al cancelar la cita';
                toast.value?.error('Error al Cancelar', errorMessage);
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

// Helper to get slot classes
function getSlotClasses(event) {
    const type = event.extendedProps?.type || event.type;
    const status = event.extendedProps?.status || event.status;

    if (type === 'available') {
        return 'bg-white border-blue-200 text-blue-600 hover:border-blue-500 hover:ring-1 hover:ring-blue-500';
    } else if (type === 'booked') {
        if (status === 'scheduled') return 'bg-red-50 border-red-200 text-red-600';
        if (status === 'completed') return 'bg-green-50 border-green-200 text-green-600';
        if (status === 'canceled') return 'bg-gray-50 border-gray-200 text-gray-400 line-through';
    }
    return 'bg-gray-50 border-gray-200 text-gray-400';
}
</script>

<template>

    <Head title="Calendario de Citas" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Agendamiento
                </h2>
                <a href="/appointments"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Ver Lista
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <!-- Header Info -->
                    <div
                        class="flex flex-col md:flex-row gap-6 mb-8 border-b border-gray-200 dark:border-gray-700 pb-6">
                        <div class="flex-shrink-0">
                            <div
                                class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                E
                            </div>
                        </div>
                        <div>
                            <h3 class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide font-semibold">
                                Enrique
                                Alonso de Armas (DEMO)</h3>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Reunirse con Enrique
                                Alonso</h1>
                            <div class="flex items-center mt-2 text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Citas de {{ appointmentDuration }} min</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Left Column: Mini Calendar -->
                        <div class="w-full lg:w-1/3 xl:w-1/4">
                            <div class="bg-white dark:bg-gray-800 rounded-lg">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Selecciona una fecha
                                </h4>

                                <!-- Month Navigation -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-gray-700 dark:text-gray-300 font-semibold capitalize">{{
                                        currentMonthName
                                        }}</span>
                                    <div class="flex space-x-2">
                                        <button @click="prevMonth"
                                            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <button @click="nextMonth"
                                            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-1 text-center text-sm mb-2">
                                    <div v-for="day in dayNames" :key="day" class="text-gray-400 font-medium py-1">
                                        {{ day }}
                                    </div>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                    <button v-for="(day, index) in miniCalendarGrid" :key="index"
                                        @click="selectDate(day.date)" :class="[
                                            'w-8 h-8 rounded-full flex items-center justify-center mx-auto transition-colors',
                                            !day.isCurrentMonth ? 'text-gray-300 dark:text-gray-600' : 'text-gray-700 dark:text-gray-300',
                                            isSameDay(day.date, selectedDate) ? 'bg-blue-600 text-white font-bold hover:bg-blue-700' : 'hover:bg-blue-50 dark:hover:bg-gray-700'
                                        ]">
                                        {{ day.date.getDate() }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Time Slots -->
                        <div class="w-full lg:w-2/3 xl:w-3/4">
                            <div class="flex items-center justify-between mb-4 lg:hidden">
                                <button @click="prevWeek" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <span class="font-semibold">Semana del {{ weekDates[0].toLocaleDateString() }}</span>
                                <button @click="nextWeek" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>
                            </div>

                            <div class="relative min-h-[400px]">
                                <div v-if="isLoading"
                                    class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 flex items-center justify-center z-10">
                                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4">
                                    <div v-for="day in weekDates" :key="day.toISOString()" class="flex flex-col gap-3">
                                        <!-- Column Header -->
                                        <div class="text-center mb-2">
                                            <div
                                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                                {{ getDayHeader(day).name }}
                                            </div>
                                            <div class="text-xl font-bold text-gray-900 dark:text-white">
                                                {{ getDayHeader(day).num }}
                                            </div>
                                        </div>

                                        <!-- Slots -->
                                        <div class="space-y-3">
                                            <div v-if="getSlotsForDay(day).length === 0" class="text-center py-4">
                                                <span class="text-gray-300 dark:text-gray-600 text-2xl">-</span>
                                            </div>
                                            <button v-for="slot in getSlotsForDay(day)" :key="slot.start"
                                                @click="handleSlotClick(slot)" :class="[
                                                    'w-full py-3 px-2 rounded-md border text-sm font-semibold transition-all duration-200 shadow-sm',
                                                    getSlotClasses(slot)
                                                ]">
                                                {{ formatTime(slot.start) }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <AppointmentModal :is-open="modalState.isOpen" :type="modalState.type" :title="modalState.title"
            :message="modalState.message" :appointment-date="modalState.appointmentDate"
            :is-loading="modalState.isLoading" @close="closeModal" @confirm="handleModalConfirm" />
        <ToastNotification ref="toast" />
    </AppLayout>
</template>
