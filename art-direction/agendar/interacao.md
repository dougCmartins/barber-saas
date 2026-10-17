# Interação

A plataforma desenhada é um telemóvel de 440px. `status-bar` e `Navigation` aparecem nas confirmações. Não há anel de foco nem hover neste payload.

## Escolher e confirmar

O swiper troca o cartão do barbeiro. Os frames expostos fixam Jardel, Caio, Iago ou Léo, sempre com nota `5.0`. Há também um `like-button` e rádios em `card-service`; o estado seleccionado desses controlos não veio expandido.

Dois botões de 58px e raio 12px:

- `Confirmar` em `#3CE3F6`, texto `#09090B`, `shadow/brand`, padding medido `148px 20px`
- `Agendar`, e um segundo `Confirmar` na folha do calendário, em `#09B0C3`, padding `0px 20px`, sem sombra nessa classe

A folha do calendário abre por cima, com cantos superiores de 24px.

## Depois de marcar

O sucesso centra um ícone, um título Anton e um parágrafo. A folha de baixo leva o passo seguinte:

- template: `Entrar`
- frame `1050:13299`: `Veja seus agendamentos`
- frame `1050:13312`: o título passa a `Conta validada com sucesso!` e o corpo pede login para agendar; o botão continua o template `Entrar`

Chips e dias do calendário estão na estrutura. Os rótulos desses chips não foram expandidos.
