# Escopo

A secção `Agendamentos` (`691:3999`) tem 23 filhos directos, todos com largura 440px. Nenhum deles é uma rota em `frontend/src/router/index.ts`. As rotas do produto hoje são `/login`, `/registo`, `/dashboard`, `/` e `/:slug`.

| Peça | Nó | Medida | O que está expandido |
|---|---|---|---|
| Agenda em carga | `868:6179` | altura abraçada | `Agenda-skeleton`, barras 376×56 |
| Agenda em carga, variante | `868:6464` | altura abraçada | `Agenda-skeleton-02`, barra em `#52525B` |
| Lista | `789:5541` | altura abraçada | `Agenda-carregado`, dropdowns e cartões |
| Lista com aviso | `1168:9350` | 956px | `Agenda-carregado` e snackbar `Serviço desmarcado.` |
| Detalhe, desmarcar | `1072:8220` | 956px | folha com botão `Desmarcar` |
| Detalhe, avaliar | `1083:8252` | 956px | folha com botão `Avaliar` |
| Confirmar cancelamento | `1168:9299` | altura abraçada | diálogo sem nome de camada; `Desmarcar serviço` |
| Filtro de período na página | `942:7239` | altura abraçada | `Agenda-filtro-periodo` |
| Folha de chips | `915:6721` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `967:5411` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `967:5329` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `967:5371` | altura abraçada | rótulos dos chips no template |
| Período personalizado | `915:6722` | altura abraçada | `filter`: `Outro período`, `Data inicial`, `Data final` |
| Filtro de situação na página | `942:7968` | altura abraçada | `Agenda-filtro-status` |
| Folha de chips | `915:6730` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5453` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5487` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5522` | altura abraçada | rótulos dos chips no template |
| Filtro de barbeiro na página | `942:8167` | altura abraçada | `Agenda-filtro-barbeiro` |
| Folha de chips | `915:6731` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5556` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5590` | altura abraçada | rótulos dos chips no template |
| Folha de chips | `969:5626` | altura abraçada | rótulos dos chips no template |

Estados presentes na secção: lista carregada, skeleton em duas variantes, filtro aberto (barbeiro, período, situação), período com datas, detalhe para desmarcar, detalhe para avaliar, confirmação e snackbar. Hover e foco não estão desenhados.
