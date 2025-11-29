<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bienvenido a Nutritive Agent" />
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-teal-50 text-gray-800 dark:from-gray-900 dark:to-gray-800 dark:text-white/90">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md shadow-sm dark:bg-gray-900/80">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-green-700 dark:text-green-400">Nutritive Agent</span>
                    </div>

                    <!-- Navigation -->
                    <nav v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-lg text-green-700 hover:bg-green-50 transition-colors duration-200 dark:text-green-400 dark:hover:bg-gray-800"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 rounded-lg text-green-700 hover:bg-green-50 transition-colors duration-200 dark:text-green-400 dark:hover:bg-gray-800"
                            >
                                Iniciar Sesión
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-4 py-2 rounded-lg text-green-700 hover:bg-green-50 transition-colors duration-200 dark:text-green-400 dark:hover:bg-gray-800"
                            >
                                Registrarse
                            </Link>
                        </template>

                        <Link
                            :href="route('appointments.calendar')"
                            class="px-6 py-2 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5"
                        >
                            Agendar Cita
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="py-16 md:py-24">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                    <!-- Hero Content -->
                    <div class="md:w-1/2">
                        <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                            Tu salud empieza con una
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-teal-600">nutrición</span>
                            adecuada
                        </h1>
                        <p class="text-xl text-gray-600 mb-8 dark:text-gray-300">
                            Conectamos pacientes con especialistas en nutrición certificados para un acompañamiento personalizado y resultados sostenibles.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link
                                :href="route('appointments.calendar')"
                                class="px-8 py-4 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center font-semibold"
                            >
                                Agendar Cita Gratis
                            </Link>
                            <Link
                                :href="route('login')"
                                class="px-8 py-4 border-2 border-green-500 text-green-600 rounded-xl hover:bg-green-50 transition-colors duration-200 text-center font-semibold dark:text-green-400 dark:hover:bg-gray-800"
                            >
                                Acceder a Mi Cuenta
                            </Link>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="md:w-1/2 relative">
                        <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl">
                            <img
                                src="../../images/Nutricion-HomePage.png"
                                alt="Nutricionista profesional asesorando a paciente"
                                class="w-full h-auto object-cover"
                            />
                        </div>
                        <!-- Floating elements -->
                        <div class="absolute -top-4 -left-4 w-24 h-24 bg-green-200 rounded-full opacity-50 z-0"></div>
                        <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-teal-200 rounded-full opacity-50 z-0"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">¿Por qué elegir Nutritive Agent?</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto dark:text-gray-300">
                        Ofrecemos un enfoque integral para tu bienestar nutricional con especialistas certificados y planes personalizados.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl dark:from-gray-700 dark:to-gray-600">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Especialistas Certificados</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Todos nuestros nutricionistas están certificados y cuentan con amplia experiencia en diferentes áreas de la nutrición clínica y deportiva.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl dark:from-gray-700 dark:to-gray-600">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Atención Personalizada</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Desarrollamos planes nutricionales adaptados a tus necesidades, objetivos y condiciones de salud específicas.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl dark:from-gray-700 dark:to-gray-600">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Seguimiento Continuo</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Monitoreamos tu progreso y ajustamos tu plan según sea necesario para garantizar resultados óptimos y sostenibles.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-gradient-to-r from-green-600 to-teal-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">¿Listo para transformar tu salud?</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Agenda tu primera consulta gratuita con uno de nuestros especialistas en nutrición.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link
                        :href="route('appointments.calendar')"
                        class="px-8 py-4 bg-white text-green-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center font-semibold"
                    >
                        Agendar Mi Primera Cita
                    </Link>
                    <Link
                        :href="route('login')"
                        class="px-8 py-4 border-2 border-white text-white rounded-xl hover:bg-white/10 transition-colors duration-200 text-center font-semibold"
                    >
                        Iniciar Sesión
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-8 bg-gray-100 dark:bg-gray-900">
            <div class="container mx-auto px-4 text-center text-gray-600 dark:text-gray-400">
                <p>Nutritive Agent &copy; {{ new Date().getFullYear() }} - Tu salud en buenas manos</p>
            </div>
        </footer>
    </div>
</template>
