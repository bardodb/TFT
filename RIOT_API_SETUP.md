# Configuração da API da Riot Games

Para usar a funcionalidade de sincronização de partidas do TFT, você precisa de uma chave da API da Riot Games.

## Como obter a chave da API

1. Acesse o [Portal de Desenvolvedores da Riot Games](https://developer.riotgames.com/)
2. Faça login com sua conta da Riot Games
3. Vá para a seção "API Keys"
4. Gere uma nova chave de desenvolvimento
5. Copie a chave gerada

## Configuração no projeto

1. Abra o arquivo `.env` na raiz do projeto
2. Encontre a linha `RIOT_API_KEY=your_riot_api_key_here`
3. Substitua `your_riot_api_key_here` pela sua chave real da API

Exemplo:
```
RIOT_API_KEY=RGAPI-12345678-1234-1234-1234-123456789012
```

## Limitações da chave de desenvolvimento

- **Rate Limit**: 100 requests a cada 2 minutos
- **Validade**: 24 horas (após isso você precisa gerar uma nova)
- **Escopo**: Apenas para desenvolvimento pessoal

## Para produção

Para usar em produção, você precisará:
1. Solicitar uma chave de produção no portal da Riot
2. Implementar rate limiting adequado
3. Configurar cache para reduzir chamadas à API

## Testando a configuração

Após configurar a chave, você pode testar acessando:
- A página de partidas em `/matches`
- Tentar sincronizar partidas de um jogador conhecido

## Exemplo de uso

1. Vá para a página de Partidas
2. Selecione "Buscar por Nome do Jogador"
3. Digite o nome do jogador (ex: "Symphonia")
4. Digite a tag (ex: "BR1")
5. Selecione a região
6. Clique em "Sincronizar"

O sistema irá:
1. Buscar o jogador pela API da Riot
2. Obter o PUUID do jogador
3. Buscar as partidas recentes
4. Processar e salvar os dados no banco
