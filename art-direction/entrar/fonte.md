# Fonte

Secção observada em 2026-10-12.

- Ficheiro: [Barber app (Community) (Copy)](https://www.figma.com/design/hSoYi7zoSMQMkWGhP3nToJ/Barber-app--Community---Copy-?node-id=114-290)
- `fileKey`: `hSoYi7zoSMQMkWGhP3nToJ`
- `nodeId`: `114:290`
- Nome da camada: `Entrar na conta`
- Tipo: secção, 5430×4401
- Leitura: `get_metadata` da secção

`get_design_context` e `get_figma_data` devolveram limite de plano (429). Cor, família tipográfica, sombra e o texto das camadas não vieram nessa leitura. Não foram copiados de Agendamentos, Agendar nem Cadastro.

## Stack do produto

O frontend em `frontend/` é Vue 3.5, Tailwind CSS 4.3, vue-router 5, Pinia 4 e vue-i18n 11.

A marca já carregada em `frontend/src/assets/main.css` usa Manrope no texto, Barlow Condensed no display, raio base `0.375rem` e uma superfície escura com primário dourado em `oklch`. Essa marca não vem desta secção.

O login do produto está em [`frontend/src/Domain/Identity/Login.vue`](../../frontend/src/Domain/Identity/Login.vue), rota `/login`. Pede email e senha, com cópia em `frontend/src/i18n/pt-BR.ts`. Esta secção desenha mais peças do que esse ecrã: dois botões na entrada, checkbox, um segundo botão depois de um divisor e um link no fundo da folha.
