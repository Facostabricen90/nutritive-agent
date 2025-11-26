<template>
    <div>
        <div
            aria-live="assertive"
            class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50"
        >
            <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
                <TransitionGroup
                    enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-for="toast in toasts"
                        :key="toast.id"
                        class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700"
                    >
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <component
                                        :is="getIcon(toast.type)"
                                        class="h-6 w-6"
                                        :class="getIconColor(toast.type)"
                                        aria-hidden="true"
                                    />
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ toast.title }}
                                    </p>
                                    <p v-if="toast.message" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ toast.message }}
                                    </p>
                                </div>
                                <div class="ml-4 flex flex-shrink-0">
                                    <button
                                        type="button"
                                        class="inline-flex rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                        @click="removeToast(toast.id)"
                                    >
                                        <span class="sr-only">Cerrar</span>
                                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { 
    CheckCircleIcon, 
    ExclamationTriangleIcon, 
    InformationCircleIcon,
    XCircleIcon,
    XMarkIcon 
} from '@heroicons/vue/24/outline';

const toasts = ref([]);
let toastCounter = 0;

function getIcon(type) {
    switch (type) {
        case 'success':
            return CheckCircleIcon;
        case 'error':
            return XCircleIcon;
        case 'warning':
            return ExclamationTriangleIcon;
        case 'info':
            return InformationCircleIcon;
        default:
            return InformationCircleIcon;
    }
}

function getIconColor(type) {
    switch (type) {
        case 'success':
            return 'text-green-400';
        case 'error':
            return 'text-red-400';
        case 'warning':
            return 'text-yellow-400';
        case 'info':
            return 'text-blue-400';
        default:
            return 'text-blue-400';
    }
}

function addToast(type, title, message = '', duration = 5000) {
    const id = ++toastCounter;
    const toast = {
        id,
        type,
        title,
        message
    };
    
    toasts.value.push(toast);
    if (duration > 0) {
        setTimeout(() => {
            removeToast(id);
        }, duration);
    }
    
    return id;
}

function removeToast(id) {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
        toasts.value.splice(index, 1);
    }
}

function clearAll() {
    toasts.value = [];
}

function success(title, message = '', duration = 5000) {
    return addToast('success', title, message, duration);
}

function error(title, message = '', duration = 7000) {
    return addToast('error', title, message, duration);
}

function warning(title, message = '', duration = 6000) {
    return addToast('warning', title, message, duration);
}

function info(title, message = '', duration = 5000) {
    return addToast('info', title, message, duration);
}

function handleToastEvent(event) {
    const { type, title, message, duration } = event.detail;
    addToast(type, title, message, duration);
}

onMounted(() => {
    window.addEventListener('show-toast', handleToastEvent);
});

onUnmounted(() => {
    window.removeEventListener('show-toast', handleToastEvent);
});

defineExpose({
    addToast,
    removeToast,
    clearAll,
    success,
    error,
    warning,
    info
});
</script>