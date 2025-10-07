<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Dashboard TFT
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Análise de partidas, builds e estatísticas do Teamfight Tactics
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <Link
            :href="route('matches.index')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Sincronizar Partidas
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total de Partidas
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ globalStats.total_matches?.toLocaleString() || '0' }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total de Jogadores
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ globalStats.total_players?.toLocaleString() || '0' }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Taxa de Vitória Global
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ globalStats.global_win_rate?.toFixed(1) || '0' }}%
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Colocação Média
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ globalStats.avg_placement?.toFixed(1) || '0' }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Best Builds -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Melhores Builds
              </h3>
              <Link
                :href="route('builds.index')"
                class="text-sm text-blue-600 hover:text-blue-500 font-medium"
              >
                Ver todas →
              </Link>
            </div>
            <div class="space-y-3">
              <Link
                v-for="build in bestBuilds.slice(0, 5)"
                :key="build.id"
                :href="route('builds.index')"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ build.composition_name || 'Composição Desconhecida' }}
                  </p>
                  <p class="text-xs text-gray-500">
                    Colocação: {{ build.placement }}º
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">
                    {{ build.win_rate?.toFixed(1) || '0' }}%
                  </p>
                  <p class="text-xs text-gray-500">Taxa de Vitória</p>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Popular Compositions -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Composições Populares
              </h3>
              <Link
                :href="route('builds.index')"
                class="text-sm text-blue-600 hover:text-blue-500 font-medium"
              >
                Ver todas →
              </Link>
            </div>
            <div class="space-y-3">
              <Link
                v-for="composition in popularCompositions.slice(0, 5)"
                :key="composition.composition_name"
                :href="route('builds.index')"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ composition.composition_name }}
                  </p>
                  <p class="text-xs text-gray-500">
                    Colocação Média: {{ composition.avg_placement?.toFixed(1) }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">
                    {{ composition.usage_count }}
                  </p>
                  <p class="text-xs text-gray-500">Usos</p>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Best Augments -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Melhores Augments
            </h3>
            <div class="space-y-3">
              <div
                v-for="augment in bestAugments.slice(0, 5)"
                :key="augment.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ augment.name }}
                  </p>
                  <p class="text-xs text-gray-500">
                    Tier {{ augment.tier }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">
                    {{ augment.win_rate?.toFixed(1) || '0' }}%
                  </p>
                  <p class="text-xs text-gray-500">Taxa de Vitória</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Best Items -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Melhores Itens
            </h3>
            <div class="space-y-3">
              <div
                v-for="item in bestItems.slice(0, 5)"
                :key="item.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ item.name }}
                  </p>
                  <p class="text-xs text-gray-500">
                    Custo: {{ item.gold_cost }}g
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">
                    {{ item.win_rate?.toFixed(1) || '0' }}%
                  </p>
                  <p class="text-xs text-gray-500">Taxa de Vitória</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  globalStats: Object,
  bestBuilds: Array,
  popularCompositions: Array,
  bestAugments: Array,
  bestItems: Array,
})
</script>
