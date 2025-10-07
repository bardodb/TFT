<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <div class="flex items-center">
            <Link
              :href="route('matches.index')"
              class="text-gray-400 hover:text-gray-500 mr-4"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Detalhes da Partida
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                Match ID: {{ match.match_id }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Match Info Card -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <dt class="text-sm font-medium text-gray-500">Data e Hora</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ formatDate(match.game_datetime) }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Duração</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ formatDuration(match.game_length) }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Set TFT</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              Set {{ match.tft_set_number }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Participantes</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ match.participants?.length || 8 }} jogadores
            </dd>
          </div>
        </div>
      </div>

      <!-- Participants Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Participantes
          </h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Colocação
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jogador
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Level
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Composição
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Augments
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="participant in sortedParticipants"
                :key="participant.id"
                :class="[
                  participant.placement === 1 ? 'bg-yellow-50' :
                  participant.placement <= 4 ? 'bg-green-50' : ''
                ]"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div
                    :class="[
                      'inline-flex items-center justify-center w-8 h-8 rounded-full text-white font-bold',
                      participant.placement === 1 ? 'bg-yellow-500' :
                      participant.placement <= 4 ? 'bg-green-500' : 'bg-gray-400'
                    ]"
                  >
                    {{ participant.placement }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ participant.player?.summoner_name || 'Desconhecido' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ participant.player?.puuid?.substring(0, 8) }}...
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ participant.level }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">
                    {{ participant.build?.composition_name || 'N/A' }}
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="augment in participant.augments?.slice(0, 3)"
                      :key="augment.id"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ augment.name?.substring(0, 15) }}...
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    v-if="participant.build"
                    @click="viewBuild(participant.build.id)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Ver Build
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  match: {
    type: Object,
    required: true
  }
})

const sortedParticipants = computed(() => {
  if (!props.match.participants) return []
  return [...props.match.participants].sort((a, b) => a.placement - b.placement)
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDuration = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const viewBuild = (buildId) => {
  router.visit(route('builds.show', buildId))
}
</script>

