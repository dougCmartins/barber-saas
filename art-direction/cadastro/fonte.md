# Fonte

Secção observada em 2026-10-12.

- Ficheiro: [Barber app (Community) (Copy)](https://www.figma.com/design/hSoYi7zoSMQMkWGhP3nToJ/Barber-app--Community---Copy-?node-id=114-409)
- `fileKey`: `hSoYi7zoSMQMkWGhP3nToJ`
- `nodeId`: `114:409`
- Nome da camada: `Cadastro de conta`
- Tipo: secção, 5301×4401
- Leitura: `get_metadata` da secção e `get_design_context` de `criar-conta-vazio` (`267:647`), `criar-conta-erro-campo-obrigatório` (`301:796`), `criar-conta-erro-dados` (`301:996`) e do botão de `criar-conta-carregando-dados` (`279:1194`)

O frame `criar-conta-preenchido` (`279:768`) não foi lido por limite do Figma. A geometria dele, no metadata, coincide com a do vazio.

## Stack do produto

O frontend em `frontend/` é Vue 3.5, Tailwind CSS 4.3, vue-router 5, Pinia 4 e vue-i18n 11.

A marca já carregada em `frontend/src/assets/main.css` usa Manrope no texto, Barlow Condensed no display, raio base `0.375rem` e uma superfície escura com primário dourado em `oklch`. Essa marca não vem desta secção.

O registo do produto em `/registo` é [`frontend/src/Domain/Orchestrator/Onboarding.vue`](../../frontend/src/Domain/Orchestrator/Onboarding.vue): cria a barbearia. Esta secção é a conta do cliente.
