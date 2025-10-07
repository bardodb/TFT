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
        <!-- Sync Messages -->
        <div v-if="syncMessage" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
          <p class="text-sm text-green-800">{{ syncMessage }}</p>
        </div>
        <div v-if="syncError" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
          <p class="text-sm text-red-800">{{ syncError }}</p>
        </div>

        <!-- Search Type Toggle -->
        <div class="mb-4">
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input
                type="radio"
                v-model="searchType"
                value="riotId"
                class="mr-2"
              />
              <span class="text-sm font-medium text-gray-700">Buscar por Nome do Jogador</span>
            </label>
            <label class="flex items-center">
              <input
                type="radio"
                v-model="searchType"
                value="puuid"
                class="mr-2"
              />
              <span class="text-sm font-medium text-gray-700">Buscar por PUUID</span>
            </label>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Região
            </label>
            <select 
              v-model="syncForm.region"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="americas">Américas</option>
              <option value="europe">Europa</option>
              <option value="asia">Ásia</option>
            </select>
          </div>
          
          <!-- Riot ID Search -->
          <div v-if="searchType === 'riotId'" class="md:col-span-2">
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Nome do Jogador
                </label>
                <input
                  v-model="syncForm.gameName"
                  type="text"
                  placeholder="Ex: Symphonia"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tag
                </label>
                <input
                  v-model="syncForm.tagLine"
                  type="text"
                  placeholder="Ex: BR1"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>
          
          <!-- PUUID Search -->
          <div v-else>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              PUUID
            </label>
            <input
              v-model="syncForm.puuid"
              type="text"
              placeholder="Digite o PUUID do jogador"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Quantidade
            </label>
            <select 
              v-model.number="syncForm.count"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option :value="10">10 partidas</option>
              <option :value="20">20 partidas</option>
              <option :value="50">50 partidas</option>
              <option :value="100">100 partidas</option>
            </select>
          </div>
          
          <div class="flex items-end">
            <button
              type="button"
              @click="syncMatches"
              :disabled="syncing || !isFormValid"
              :class="[
                'w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                (syncing || !isFormValid)
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500'
              ]"
            >
              <svg v-if="syncing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ syncing ? 'Sincronizando...' : 'Sincronizar' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Matches List -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul v-if="matches.data && matches.data.length > 0" class="divide-y divide-gray-200">
          <li v-for="match in matches.data" :key="match.id">
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
                    {{ match.winner_placement || '?' }}
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
                  {{ match.participants_count || 8 }} jogadores
                </div>
                <div class="text-sm text-gray-500">
                  Set {{ match.tft_set_number }}
                </div>
                <Link
                  :href="route('matches.show', match.match_id)"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Ver Detalhes
                </Link>
              </div>
            </div>
          </li>
        </ul>
        
        <!-- Empty State -->
        <div v-else class="text-center py-12">
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
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({
  matches: {
    type: Object,
    default: () => ({ data: [] })
  }
})

const searchType = ref('riotId')

const syncForm = ref({
  region: 'americas',
  puuid: '',
  gameName: '',
  tagLine: '',
  count: 20,
})

const syncing = ref(false)
const syncMessage = ref('')
const syncError = ref('')

const isFormValid = computed(() => {
  if (searchType.value === 'riotId') {
    return syncForm.value.gameName.trim() && syncForm.value.tagLine.trim()
  } else {
    return syncForm.value.puuid.trim()
  }
})

const syncMatches = async () => {
  if (!isFormValid.value) {
    syncError.value = searchType.value === 'riotId' 
      ? 'Por favor, insira o nome do jogador e a tag' 
      : 'Por favor, insira um PUUID válido'
    return
  }

  syncing.value = true
  syncMessage.value = ''
  syncError.value = ''

  try {
    let puuid = syncForm.value.puuid

    // Se estiver buscando por Riot ID, primeiro obter o PUUID
    if (searchType.value === 'riotId') {
      console.log('Buscando jogador por Riot ID:', {
        gameName: syncForm.value.gameName,
        tagLine: syncForm.value.tagLine,
        region: syncForm.value.region
      })

      const playerResponse = await axios.get('/api/tft/player-by-riot-id', {
        params: {
          gameName: syncForm.value.gameName,
          tagLine: syncForm.value.tagLine,
          region: syncForm.value.region,
        }
      })

      console.log('Resposta da busca do jogador:', playerResponse.data)

      if (playerResponse.data.success) {
        puuid = playerResponse.data.data.account.puuid
        console.log('PUUID obtido:', puuid)
        syncMessage.value = `Jogador encontrado: ${syncForm.value.gameName}#${syncForm.value.tagLine}`
      } else {
        throw new Error(playerResponse.data.message || 'Jogador não encontrado')
      }
    } else {
      // Validar formato do PUUID se estiver usando busca por PUUID
      if (syncForm.value.puuid.includes('#')) {
        throw new Error('O campo PUUID não deve conter o símbolo #. Use a busca por nome do jogador se você tem o nome e tag.')
      }
    }

    console.log('Sincronizando partidas com PUUID:', puuid)

    // Sincronizar partidas usando o PUUID
    const response = await axios.post('/api/tft/sync-matches', {
      puuid: puuid,
      region: syncForm.value.region,
      count: syncForm.value.count,
    })

    console.log('Resposta da sincronização:', response.data)

    if (response.data.success) {
      syncMessage.value = response.data.message
      // Recarregar a página para mostrar os dados atualizados
      setTimeout(() => {
        router.reload({ only: ['matches'] })
      }, 1500)
    }
  } catch (error) {
    console.error('Erro na sincronização:', error)
    syncError.value = error.response?.data?.message || error.message || 'Erro ao sincronizar partidas'
  } finally {
    syncing.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('pt-BR')
}

const formatDuration = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>
