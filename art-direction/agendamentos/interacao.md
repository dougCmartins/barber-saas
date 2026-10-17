# Interação

A plataforma desenhada é um telemóvel de 440px de largura. A navegação inferior e a barra de estado existem como camadas (`bottom-nav`, `status-bar`, `top-bar`) nos frames de ecrã. Não há anel de foco nem estado hover desenhado nesta secção.

## Filtros

Na lista, `Barbeiro`, `Período` e `Situação` são dropdowns com chevron. Os frames `Agenda-filtro-barbeiro`, `Agenda-filtro-periodo` e `Agenda-filtro-status` são a mesma agenda com um filtro aberto.

As folhas inferiores fecham com `XOutline`. O chip expandido `Outro período` está preenchido a `#3CE3F6` com texto `#09090B`, raio 16px. A folha `filter` (`915:6722`) mostra esse chip mais dois campos:

- `Data inicial`, placeholder `01/02/2025`
- `Data final`, placeholder `01/10/2025`

Os outros chips das folhas estão em templates; o payload não expandiu os rótulos.

## Detalhe

Duas folhas de altura 956px:

- `1072:8220` termina com o botão `Desmarcar`
- `1083:8252` termina com o botão `Avaliar`

O botão de marca mede altura 58px, raio 12px, fundo `#3CE3F6`, texto `#09090B`, sombra `shadow/brand`. No detalhe, o rótulo `Desmarcar` e `Avaliar` usa a cor `#3CE3F6` sobre o botão secundário (componente `213:438`).

## Cancelar

O diálogo `1168:9299` pergunta `Tem certeza que deseja cancelar esse agendamento?`.

- `Não, obrigado`: largura 188px, altura 58px, padding `0px 20px`, fundo `#52525B`, texto `#FAFAFA`, raio 12px, `shadow/neutral`
- `Desmarcar`: altura 58px, padding medido `148px 20px`, fundo `#3CE3F6`, texto `#09090B`, raio 12px, `shadow/brand`

O snackbar em `1168:9350` (x 25, y 756) tem padding `8px 16px`, gap 12px, fundo `#27272A`, traço `#52525B` de 1px, raio 16px e o título `Serviço desmarcado.` em `#FB7185`. O botão de fechar está visível (`closed-button: true`) e o botão de acção está oculto (`has-button: false`).

## Estados de carga

`Agenda-skeleton` (`868:6179`) e `Agenda-skeleton-02` (`868:6464`) substituem cartões por barras de 376×56, raio 16px. Uma instância usa fundo `#27272A`; outra, `#52525B`.
