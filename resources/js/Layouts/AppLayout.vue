<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')" class="flex items-center">
                <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                  <span class="text-white font-bold text-lg">T</span>
                </div>
                <span class="ml-2 text-xl font-bold text-gray-900">TFT Analytics</span>
              </Link>
            </div>
            
            <!-- Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link
                :href="route('dashboard')"
                :class="[
                  $page.url === '/' || $page.url.startsWith('/dashboard') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                Dashboard
              </Link>
              
              <Link
                :href="route('matches.index')"
                :class="[
                  $page.url.startsWith('/matches')
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                Partidas
              </Link>
              
              <Link
                :href="route('builds.index')"
                :class="[
                  $page.url.startsWith('/builds')
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                Builds
              </Link>
              
              <Link
                :href="route('dashboard.analytics')"
                :class="[
                  $page.url.startsWith('/analytics')
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                Analytics
              </Link>
            </div>
          </div>
          
          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
          <Link
            :href="route('dashboard')"
            :class="[
              $page.url === '/' || $page.url.startsWith('/dashboard') 
                ? 'bg-blue-50 border-blue-500 text-blue-700' 
                : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
              'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
            ]"
            @click="mobileMenuOpen = false"
          >
            Dashboard
          </Link>
          
          <Link
            :href="route('matches.index')"
            :class="[
              $page.url.startsWith('/matches')
                ? 'bg-blue-50 border-blue-500 text-blue-700' 
                : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
              'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
            ]"
            @click="mobileMenuOpen = false"
          >
            Partidas
          </Link>
          
          <Link
            :href="route('builds.index')"
            :class="[
              $page.url.startsWith('/builds')
                ? 'bg-blue-50 border-blue-500 text-blue-700' 
                : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
              'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
            ]"
            @click="mobileMenuOpen = false"
          >
            Builds
          </Link>
          
          <Link
            :href="route('dashboard.analytics')"
            :class="[
              $page.url.startsWith('/analytics')
                ? 'bg-blue-50 border-blue-500 text-blue-700' 
                : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
              'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
            ]"
            @click="mobileMenuOpen = false"
          >
            Analytics
          </Link>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const mobileMenuOpen = ref(false)
</script>
