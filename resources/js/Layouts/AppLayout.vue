<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gradient-to-br from-green-50 to-teal-50 dark:from-gray-900 dark:to-gray-800">
            <nav class="bg-white dark:bg-gray-800 border-b border-green-200 dark:border-gray-700 shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo Nutritive Agent -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="hidden md:block">
                                            <span class="text-xl font-bold text-green-700 dark:text-green-400">Nutritive Agent</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')" 
                                    class="text-green-700 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Panel principal
                                </NavLink>
                                <NavLink :href="route('appointments.index')"
                                    :active="route().current('appointments.index')"
                                    class="text-green-700 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Panel administrativo
                                </NavLink>
                                <NavLink :href="route('appointments.calendar')"
                                    :active="route().current('appointments.calendar')"
                                    class="text-green-700 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                    Calendario
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            v-if="$page.props.jetstream && $page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-green-300 transition hover:scale-105 duration-200">
                                            <img class="size-8 rounded-full object-cover"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 dark:text-green-400 bg-white dark:bg-gray-800 hover:text-green-900 dark:hover:text-green-300 focus:outline-none focus:bg-green-50 dark:focus:bg-gray-700 active:bg-green-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Gestionar Cuenta
                                        </div>

                                        <DropdownLink :href="route('profile.show')" class="text-green-700 hover:bg-green-50">
                                            Perfil
                                        </DropdownLink>

                                        <DropdownLink
                                            v-if="$page.props.jetstream && $page.props.jetstream.hasApiFeatures"
                                            :href="route('api-tokens.index')" class="text-green-700 hover:bg-green-50">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200 dark:border-gray-600" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button" class="text-red-600 hover:bg-red-50">
                                                Cerrar Sesión
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-gray-700 focus:outline-none focus:bg-green-50 dark:focus:bg-gray-700 focus:text-green-900 dark:focus:text-green-300 transition duration-150 ease-in-out"
                                @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1 bg-white dark:bg-gray-800">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                            class="text-green-700 hover:text-green-900 dark:text-green-400">
                            Panel Principal
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('appointments.index')"
                            :active="route().current('appointments.index')"
                            class="text-green-700 hover:text-green-900 dark:text-green-400">
                            Panel administrativo
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('appointments.calendar')"
                            :active="route().current('appointments.calendar')"
                            class="text-green-700 hover:text-green-900 dark:text-green-400">
                            Calendario
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-green-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                        <template v-if="$page.props.auth && $page.props.auth.user">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream && $page.props.jetstream.managesProfilePhotos"
                                    class="shrink-0 me-3">
                                    <img class="size-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-green-800 dark:text-green-400">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="font-medium text-sm text-green-600 dark:text-green-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')"
                                    :active="route().current('profile.show')"
                                    class="text-green-700 hover:text-green-900 dark:text-green-400">
                                    Perfil
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream && $page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')" :active="route().current('api-tokens.index')"
                                    class="text-green-700 hover:text-green-900 dark:text-green-400">
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button" class="text-red-600 hover:text-red-800">
                                        Cerrar Sesión
                                    </ResponsiveNavLink>
                                </form>

                                <!-- Team Management -->
                                <template v-if="$page.props.jetstream && $page.props.jetstream.hasTeamFeatures">
                                    <div class="border-t border-green-200 dark:border-gray-600" />

                                    <div class="block px-4 py-2 text-xs text-green-600 dark:text-green-400">
                                        Gestionar Equipo
                                    </div>

                                    <!-- Team Settings -->
                                    <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                        :active="route().current('teams.show')"
                                        class="text-green-700 hover:text-green-900 dark:text-green-400">
                                        Configuración del Equipo
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink
                                        v-if="$page.props.jetstream && $page.props.jetstream.canCreateTeams"
                                        :href="route('teams.create')" :active="route().current('teams.create')"
                                        class="text-green-700 hover:text-green-900 dark:text-green-400">
                                        Crear Nuevo Equipo
                                    </ResponsiveNavLink>

                                    <!-- Team Switcher -->
                                    <template
                                        v-if="$page.props.auth.user.all_teams && $page.props.auth.user.all_teams.length > 1">
                                        <div class="border-t border-green-200 dark:border-gray-600" />

                                        <div class="block px-4 py-2 text-xs text-green-600 dark:text-green-400">
                                            Cambiar Equipo
                                        </div>

                                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <ResponsiveNavLink as="button"
                                                    class="text-green-700 hover:text-green-900 dark:text-green-400">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                            class="me-2 size-5 text-green-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </ResponsiveNavLink>
                                            </form>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow-sm border-b border-green-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>