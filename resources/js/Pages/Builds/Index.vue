<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Melhores Builds TFT
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Descubra as composições mais eficazes e populares
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Filtrar Builds
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Colocação Máxima
            </label>
            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="1">1º lugar</option>
              <option value="2">Top 2</option>
              <option value="3">Top 3</option>
              <option value="4" selected>Top 4</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Taxa de Vitória Mínima
            </label>
            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="0">Qualquer</option>
              <option value="10">10%+</option>
              <option value="20">20%+</option>
              <option value="30">30%+</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Ordenar por
            </label>
            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="win_rate">Taxa de Vitória</option>
              <option value="placement">Colocação</option>
              <option value="pick_rate">Popularidade</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Builds Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          v-for="build in builds"
          :key="build.id"
          class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
        >
          <!-- Build Header -->
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                {{ build.composition_name || 'Composição Desconhecida' }}
              </h3>
              <div 
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  build.placement === 1 ? 'bg-yellow-100 text-yellow-800' :
                  build.placement <= 4 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ build.placement }}º lugar
              </div>
            </div>

            <!-- Build Stats -->
            <div class="grid grid-cols-3 gap-4 mb-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">
                  {{ build.win_rate?.toFixed(1) || '0' }}%
                </div>
                <div class="text-xs text-gray-500">Taxa de Vitória</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">
                  {{ build.pick_rate?.toFixed(1) || '0' }}%
                </div>
                <div class="text-xs text-gray-500">Popularidade</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">
                  {{ build.avg_placement?.toFixed(1) || '0' }}
                </div>
                <div class="text-xs text-gray-500">Colocação Média</div>
              </div>
            </div>

            <!-- Champions Preview -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Campeões</h4>
              <div class="flex flex-wrap gap-1">
                <div
                  v-for="champion in build.champions?.slice(0, 8)"
                  :key="champion.id"
                  class="w-8 h-8 bg-gray-200 rounded border-2 border-gray-300 flex items-center justify-center"
                  :title="champion.name"
                >
                  <span class="text-xs font-bold text-gray-600">
                    {{ champion.name?.charAt(0) || '?' }}
                  </span>
                </div>
                <div
                  v-if="build.champions?.length > 8"
                  class="w-8 h-8 bg-gray-100 rounded border-2 border-gray-300 flex items-center justify-center"
                >
                  <span class="text-xs font-bold text-gray-500">
                    +{{ build.champions.length - 8 }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Augments Preview -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Augments</h4>
              <div class="flex flex-wrap gap-1">
                <div
                  v-for="augment in build.augments?.slice(0, 3)"
                  :key="augment.id"
                  class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded"
                  :title="augment.name"
                >
                  {{ augment.name?.substring(0, 10) || 'Unknown' }}...
                </div>
              </div>
            </div>

            <!-- Build Description -->
            <div class="mb-4">
              <p class="text-sm text-gray-600">
                {{ build.description || 'Nenhuma descrição disponível para esta composição.' }}
              </p>
            </div>

            <!-- Build Actions -->
            <div class="flex space-x-2">
              <button
                type="button"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Ver Detalhes
              </button>
              <button
                type="button"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Copiar Build
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="builds.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum build encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">
          Comece sincronizando algumas partidas para ver os builds aqui.
        </p>
        <div class="mt-6">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Sincronizar Partidas
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const builds = ref([
  // Dados de exemplo - serão substituídos por dados reais da API
  {
    id: 1,
    composition_name: 'Kai\'Sa Carry',
    placement: 1,
    win_rate: 67.5,
    pick_rate: 15.2,
    avg_placement: 3.2,
    description: 'Composição focada em Kai\'Sa como carry principal com suporte de campeões de custo alto.',
    champions: [
      { id: 1, name: 'Kai\'Sa' },
      { id: 2, name: 'Aatrox' },
      { id: 3, name: 'Azir' },
      { id: 4, name: 'Bel\'Veth' },
      { id: 5, name: 'Kassadin' },
      { id: 6, name: 'Malzahar' },
      { id: 7, name: 'Rek\'Sai' },
      { id: 8, name: 'Kha\'Zix' },
    ],
    augments: [
      { id: 1, name: 'Voidborn' },
      { id: 2, name: 'Adaptive Evolution' },
      { id: 3, name: 'Void Resonance' },
    ],
  },
  {
    id: 2,
    composition_name: 'Demacia Carry',
    placement: 2,
    win_rate: 58.3,
    pick_rate: 22.1,
    avg_placement: 3.8,
    description: 'Composição Demacia com foco em campeões de custo alto e sinergias defensivas.',
    champions: [
      { id: 1, name: 'Lux' },
      { id: 2, name: 'Garen' },
      { id: 3, name: 'Jarvan IV' },
      { id: 4, name: 'Poppy' },
      { id: 5, name: 'Galio' },
      { id: 6, name: 'Sona' },
    ],
    augments: [
      { id: 1, name: 'Demacian Might' },
      { id: 2, name: 'Shield Wall' },
      { id: 3, name: 'Radiant Aegis' },
    ],
  },
])
</script>
