<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Partidas TFT
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Visualize e analise partidas recentes do Teamfight Tactics
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Buscar Partidas
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Região
            </label>
            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="americas">Américas</option>
              <option value="europe">Europa</option>
              <option value="asia">Ásia</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              PUUID
            </label>
            <input
              type="text"
              placeholder="Digite o PUUID do jogador"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Quantidade
            </label>
            <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="10">10 partidas</option>
              <option value="20">20 partidas</option>
              <option value="50">50 partidas</option>
              <option value="100">100 partidas</option>
            </select>
          </div>
          
          <div class="flex items-end">
            <button
              type="button"
              class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Sincronizar
            </button>
          </div>
        </div>
      </div>

      <!-- Matches List -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
          <li v-for="match in matches" :key="match.id">
            <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div 
                    :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center text-white font-bold',
                      match.winner_placement === 1 ? 'bg-yellow-500' : 
                      match.winner_placement <= 4 ? 'bg-green-500' : 'bg-gray-500'
                    ]"
                  >
                    {{ match.winner_placement }}
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    Partida {{ match.match_id }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ formatDate(match.game_datetime) }} - {{ formatDuration(match.game_length) }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                  {{ match.participants_count }} jogadores
                </div>
                <div class="text-sm text-gray-500">
                  Set {{ match.tft_set_number }}
                </div>
                <button
                  type="button"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Ver Detalhes
                </button>
              </div>
            </div>
          </li>
        </ul>
        
        <!-- Empty State -->
        <div v-if="matches.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma partida encontrada</h3>
          <p class="mt-1 text-sm text-gray-500">
            Comece sincronizando algumas partidas para ver os dados aqui.
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
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const matches = ref([
  // Dados de exemplo - serão substituídos por dados reais da API
  {
    id: 1,
    match_id: 'BR1_1234567890',
    game_datetime: new Date().toISOString(),
    game_length: 1800,
    winner_placement: 1,
    participants_count: 8,
    tft_set_number: 12,
  },
  {
    id: 2,
    match_id: 'BR1_1234567891',
    game_datetime: new Date(Date.now() - 3600000).toISOString(),
    game_length: 2100,
    winner_placement: 3,
    participants_count: 8,
    tft_set_number: 12,
  },
])

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('pt-BR')
}

const formatDuration = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>
