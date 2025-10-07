# TFT Analytics - Configuração e Instalação

## Visão Geral

Esta aplicação Laravel fornece uma API completa para análise de partidas do Teamfight Tactics (TFT), incluindo:

- Consulta de partidas mundiais
- Análise de replays
- Estatísticas de melhores builds
- Análise de composições populares
- Estatísticas de augments e itens
- Interface web moderna com Vue.js e Inertia.js

## Tecnologias Utilizadas

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Database**: SQLite (configurável)
- **API Integration**: Guzzle HTTP Client
- **Real-time**: Livewire (para componentes interativos)

## Configuração do Ambiente

### 1. Configuração do .env

Copie o arquivo `.env.example` para `.env` e configure as seguintes variáveis:

```env
APP_NAME="TFT Analytics"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Riot Games API Configuration
RIOT_API_KEY=your_riot_api_key_here
RIOT_BASE_URL=https://americas.api.riotgames.com
```

### 2. Obter API Key da Riot Games

1. Acesse [Riot Developer Portal](https://developer.riotgames.com/)
2. Faça login com sua conta Riot
3. Crie um novo projeto
4. Copie a API Key e adicione ao arquivo `.env`

### 3. Instalação das Dependências

```bash
# Instalar dependências PHP
composer install

# Instalar dependências JavaScript
npm install

# Gerar chave da aplicação
php artisan key:generate

# Executar migrations
php artisan migrate

# Compilar assets
npm run build
```

### 4. Executar a Aplicação

```bash
# Desenvolvimento
php artisan serve
npm run dev

# Ou usar o comando integrado
composer run dev
```

## Estrutura da API

### Endpoints Principais

#### Global Stats
- `GET /api/tft/stats` - Estatísticas globais
- `GET /api/tft/status` - Status da API da Riot

#### Sincronização de Dados
- `POST /api/tft/sync-matches` - Sincronizar partidas de um jogador
- `GET /api/tft/player` - Informações do jogador

#### Analytics
- `GET /api/tft/analytics/dashboard` - Dados do dashboard
- `GET /api/tft/analytics/best-builds` - Melhores builds
- `GET /api/tft/analytics/popular-compositions` - Composições populares
- `GET /api/tft/analytics/best-augments` - Melhores augments
- `GET /api/tft/analytics/best-items` - Melhores itens

#### Partidas
- `GET /api/tft/matches` - Listar partidas
- `GET /api/tft/matches/{id}` - Detalhes da partida
- `GET /api/tft/matches/{id}/replay` - Replay da partida

#### Builds
- `GET /api/tft/builds` - Listar builds
- `GET /api/tft/builds/{id}` - Detalhes do build
- `POST /api/tft/builds` - Criar build
- `PUT /api/tft/builds/{id}` - Atualizar build
- `DELETE /api/tft/builds/{id}` - Deletar build

#### Jogadores
- `GET /api/tft/players` - Listar jogadores
- `GET /api/tft/players/{id}` - Detalhes do jogador
- `GET /api/tft/players/{id}/matches` - Partidas do jogador
- `GET /api/tft/players/{id}/builds` - Builds do jogador
- `GET /api/tft/players/{id}/stats` - Estatísticas do jogador

## Estrutura do Banco de Dados

### Tabelas Principais

- **tft_matches** - Partidas do TFT
- **players** - Jogadores/Summoners
- **builds** - Análises de builds
- **champions** - Campeões do TFT
- **augments** - Augments disponíveis
- **items** - Itens do TFT
- **match_participants** - Participantes das partidas

### Tabelas de Relacionamento

- **build_champions** - Relacionamento builds-champions
- **build_augments** - Relacionamento builds-augments
- **build_items** - Relacionamento builds-items

## Funcionalidades

### 1. Sincronização de Partidas
- Busca partidas por PUUID do jogador
- Processa dados da API da Riot Games
- Armazena informações detalhadas das partidas
- Analisa composições e builds automaticamente

### 2. Análise de Builds
- Identifica composições vencedoras
- Calcula taxas de vitória e popularidade
- Analisa augments e itens mais eficazes
- Fornece estatísticas detalhadas

### 3. Interface Web
- Dashboard com estatísticas globais
- Visualização de partidas recentes
- Análise de builds populares
- Filtros avançados para pesquisa

### 4. API RESTful
- Endpoints completos para integração
- Documentação automática
- Rate limiting e cache
- Respostas padronizadas

## Uso da API

### Exemplo: Sincronizar Partidas

```bash
curl -X POST http://localhost:8000/api/tft/sync-matches \
  -H "Content-Type: application/json" \
  -d '{
    "puuid": "player_puuid_here",
    "region": "americas",
    "count": 20,
    "start": 0
  }'
```

### Exemplo: Buscar Melhores Builds

```bash
curl "http://localhost:8000/api/tft/analytics/best-builds?limit=10&min_placement=4"
```

## Desenvolvimento

### Estrutura de Arquivos

```
app/
├── Http/Controllers/
│   ├── Api/          # Controllers da API
│   └── Web/          # Controllers da interface web
├── Models/           # Models do Eloquent
├── Services/         # Serviços de integração
└── Providers/        # Service Providers

resources/js/
├── Pages/            # Páginas Vue.js
├── Components/       # Componentes Vue.js
├── Layouts/          # Layouts da aplicação
└── css/              # Estilos CSS

routes/
├── api.php           # Rotas da API
└── web.php           # Rotas web
```

### Comandos Úteis

```bash
# Executar migrations
php artisan migrate

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Compilar assets para produção
npm run build

# Executar testes
php artisan test
```

## Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
