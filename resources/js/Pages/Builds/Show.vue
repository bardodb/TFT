<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <div class="flex items-center">
            <Link
              :href="route('builds.index')"
              class="text-gray-400 hover:text-gray-500 mr-4"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ build.composition_name || 'Composição Desconhecida' }}
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                Detalhes completos da composição
              </p>
            </div>
          </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            @click="copyBuild"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Copiar Build
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div
                  :class="[
                    'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl',
                    build.placement === 1 ? 'bg-yellow-500' :
                    build.placement <= 4 ? 'bg-green-500' : 'bg-gray-500'
                  ]"
                >
                  {{ build.placement }}
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Colocação Final
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ build.placement }}º lugar
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
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total de Campeões
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ build.champions?.length || 0 }} campeões
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
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Augments
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ build.augments?.length || 0 }} augments
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Champions Section -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Campeões da Composição
          </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div v-if="build.champions && build.champions.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div
              v-for="champion in build.champions"
              :key="champion.id"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mb-2">
                <span class="text-2xl font-bold text-gray-600">
                  {{ champion.name?.charAt(0) || '?' }}
                </span>
              </div>
              <p class="text-sm font-medium text-gray-900 text-center">
                {{ champion.name }}
              </p>
              <p class="text-xs text-gray-500">
                Custo: {{ champion.cost || 'N/A' }}
              </p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Nenhum campeão registrado para esta composição
          </div>
        </div>
      </div>

      <!-- Augments Section -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Augments Utilizados
          </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div v-if="build.augments && build.augments.length > 0" class="space-y-4">
            <div
              v-for="augment in build.augments"
              :key="augment.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ augment.name }}
                </p>
                <p class="text-xs text-gray-500">
                  Tier {{ augment.tier }}
                </p>
              </div>
              <div
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  augment.tier === 3 ? 'bg-purple-100 text-purple-800' :
                  augment.tier === 2 ? 'bg-blue-100 text-blue-800' :
                  'bg-gray-100 text-gray-800'
                ]"
              >
                Tier {{ augment.tier }}
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Nenhum augment registrado para esta composição
          </div>
        </div>
      </div>

      <!-- Items Section -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Itens da Composição
          </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div v-if="build.items && build.items.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div
              v-for="item in build.items"
              :key="item.id"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-orange-300 rounded-lg flex items-center justify-center mb-2">
                <span class="text-xl font-bold text-orange-800">
                  {{ item.name?.charAt(0) || '?' }}
                </span>
              </div>
              <p class="text-xs font-medium text-gray-900 text-center">
                {{ item.name }}
              </p>
              <p class="text-xs text-gray-500">
                {{ item.gold_cost }}g
              </p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Nenhum item registrado para esta composição
          </div>
        </div>
      </div>

      <!-- Player Info (if available) -->
      <div v-if="build.participant?.player" class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Informações do Jogador
          </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Nome de Invocador</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ build.participant.player.summoner_name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">PUUID</dt>
              <dd class="mt-1 text-sm text-gray-900 font-mono">{{ build.participant.player.puuid }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Região</dt>
              <dd class="mt-1 text-sm text-gray-900 uppercase">{{ build.participant.player.region }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Level no Jogo</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ build.participant.level }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  build: {
    type: Object,
    required: true
  }
})

const copyBuild = () => {
  const buildText = `
Build: ${props.build.composition_name || 'Composição Desconhecida'}
Colocação: ${props.build.placement}º

Campeões: ${props.build.champions?.map(c => c.name).join(', ') || 'N/A'}
Augments: ${props.build.augments?.map(a => a.name).join(', ') || 'N/A'}
Itens: ${props.build.items?.map(i => i.name).join(', ') || 'N/A'}
  `.trim()

  navigator.clipboard.writeText(buildText).then(() => {
    alert('Build copiada para a área de transferência!')
  }).catch(() => {
    alert('Erro ao copiar build')
  })
}
</script>

