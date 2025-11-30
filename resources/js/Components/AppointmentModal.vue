<template>
    <TransitionRoot as="template" :show="isOpen">
        <Dialog as="div" class="relative z-50" @close="closeModal">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            <div>
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full" :class="iconBgClass">
                                    <component :is="iconComponent" class="h-6 w-6" :class="iconClass" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                        {{ title }}
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ message }}
                                        </p>
                                    </div>

                                    <!-- Appointment Form for booking -->
                                    <div v-if="type === 'book'" class="mt-6 text-left">
                                        <form @submit.prevent="confirmAction" class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Nombre completo
                                                </label>
                                                <input
                                                    v-model.trim="form.patient_name"
                                                    type="text"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                />
                                                <p v-if="formErrors.patient_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                    {{ formErrors.patient_name }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Cédula / Documento
                                                </label>
                                                <input
                                                    v-model.trim="form.patient_document"
                                                    type="text"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                />
                                                <p v-if="formErrors.patient_document" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                    {{ formErrors.patient_document }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Correo electrónico
                                                </label>
                                                <input
                                                    v-model.trim="form.patient_email"
                                                    type="email"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                />
                                                <p v-if="formErrors.patient_email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                    {{ formErrors.patient_email }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Razón de la consulta
                                                </label>
                                                <textarea
                                                    v-model.trim="form.appointment_reason"
                                                    rows="3"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="Describe brevemente tu motivo de consulta"
                                                ></textarea>
                                                <p v-if="formErrors.appointment_reason" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                    {{ formErrors.appointment_reason }}
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button
                                    type="button"
                                    class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm sm:col-start-2 transition-colors duration-200"
                                    :class="confirmButtonClass"
                                    :disabled="isLoading"
                                    @click="confirmAction"
                                >
                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ isLoading ? 'Procesando...' : confirmButtonText }}
                                </button>
                                <button
                                    type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:col-start-1 sm:mt-0 transition-colors duration-200"
                                    :disabled="isLoading"
                                    @click="closeModal"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
//cambio prueba
import { ref, computed, watch } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { 
    CalendarDaysIcon, 
    ExclamationTriangleIcon, 
    CheckCircleIcon,
    XCircleIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    isOpen: Boolean,
    type: {
        type: String,
        default: 'book' // book, cancel, info
    },
    title: String,
    message: String,
    // CORRECCIÓN: Permitir String o Date para evitar el warning de Vue
    appointmentDate: [String, Date],
    appointmentId: [Number, String],
    isLoading: Boolean
});

const emit = defineEmits(['close', 'confirm']);

const formattedDate = computed(() => {
    if (!props.appointmentDate) return '';
    const date = typeof props.appointmentDate === 'string'
        ? new Date(props.appointmentDate)
        : props.appointmentDate;

    if (!date || isNaN(date.getTime())) return '';

    return date.toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'America/Bogota'
    });
});

const iconComponent = computed(() => {
    switch (props.type) {
        case 'book':
            return CalendarDaysIcon;
        case 'cancel':
            return ExclamationTriangleIcon;
        case 'success':
            return CheckCircleIcon;
        case 'error':
            return XCircleIcon;
        default:
            return CalendarDaysIcon;
    }
});

const iconBgClass = computed(() => {
    switch (props.type) {
        case 'book':
            return 'bg-blue-100 dark:bg-blue-900';
        case 'cancel':
            return 'bg-red-100 dark:bg-red-900';
        case 'success':
            return 'bg-green-100 dark:bg-green-900';
        case 'error':
            return 'bg-red-100 dark:bg-red-900';
        default:
            return 'bg-blue-100 dark:bg-blue-900';
    }
});

const iconClass = computed(() => {
    switch (props.type) {
        case 'book':
            return 'text-blue-600 dark:text-blue-400';
        case 'cancel':
            return 'text-red-600 dark:text-red-400';
        case 'success':
            return 'text-green-600 dark:text-green-400';
        case 'error':
            return 'text-red-600 dark:text-red-400';
        default:
            return 'text-blue-600 dark:text-blue-400';
    }
});

const confirmButtonClass = computed(() => {
    const baseClasses = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
    
    switch (props.type) {
        case 'book':
            return `bg-blue-600 text-white hover:bg-blue-500 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed ${baseClasses}`;
        case 'cancel':
            return `bg-red-600 text-white hover:bg-red-500 focus-visible:outline-red-600 disabled:opacity-50 disabled:cursor-not-allowed ${baseClasses}`;
        case 'success':
        case 'error':
            return `bg-gray-600 text-white hover:bg-gray-500 focus-visible:outline-gray-600 disabled:opacity-50 disabled:cursor-not-allowed ${baseClasses}`;
        default:
            return `bg-blue-600 text-white hover:bg-blue-500 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed ${baseClasses}`;
    }
});

const confirmButtonText = computed(() => {
    switch (props.type) {
        case 'book':
            return 'Confirmar Cita';
        case 'cancel':
            return 'Sí, Cancelar';
        case 'success':
        case 'error':
            return 'Entendido';
        default:
            return 'Confirmar';
    }
});

const initialFormState = () => ({
    patient_name: '',
    patient_document: '',
    patient_email: '',
    appointment_reason: '',
});

const form = ref(initialFormState());
const formErrors = ref({});

watch(
    () => props.isOpen && props.type === 'book',
    (shouldReset) => {
        if (shouldReset) {
            form.value = initialFormState();
            formErrors.value = {};
        }
    }
);

function validateForm() {
    const errors = {};

    if (!form.value.patient_name) {
        errors.patient_name = 'Ingresa tu nombre completo.';
    }

    if (!form.value.patient_document) {
        errors.patient_document = 'Ingresa tu cédula o documento.';
    }

    if (!form.value.patient_email) {
        errors.patient_email = 'Ingresa tu correo electrónico.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.patient_email)) {
        errors.patient_email = 'El correo electrónico no es válido.';
    }

    if (!form.value.appointment_reason) {
        errors.appointment_reason = 'Describe brevemente la razón de tu consulta.';
    }

    formErrors.value = errors;

    return Object.keys(errors).length === 0;
}

function closeModal() {
    if (props.isLoading) return;
    emit('close');
}

function confirmAction() {
    if (props.isLoading) return;

    if (props.type === 'book' && !validateForm()) {
        return;
    }

    emit('confirm', {
        type: props.type,
        date: props.appointmentDate,
        form: { ...form.value },
    });
}
</script>