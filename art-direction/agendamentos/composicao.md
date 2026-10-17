# Composição

Todos os ecrãs directos medem 440px de largura. Dois frames têm altura fixa de 956px: o segundo `Agenda-carregado` (`1168:9350`) e os detalhes `1072:8220` e `1083:8252`. Os restantes abraçam o conteúdo.

## Lista carregada

`Agenda-carregado` (`789:5541`) é uma coluna. O frame `main` tem padding `216px 0px 160px`. Esse vazio superior e inferior deixa espaço para a barra de topo e para a navegação inferior, que aparecem no frame de 956px.

A fila de filtros (`chips-list-filter-page`) é uma linha com scroll horizontal, padding esquerdo 24px e gap 16px. Cada dropdown tem altura 52px, padding `0px 16px`, gap 16px, fundo `#27272A`, traço `#3CE3F6` de 1px e raio 16px. Os rótulos expandidos são `Barbeiro`, `Período` e `Situação`.

A lista agrupa cartões sob um cabeçalho de secção. O corpo do cartão é uma coluna com gap 12px. A imagem fica à esquerda, 96×fill, raio 16px. O cabeçalho do texto tem altura 40px; quando há estrela, o gap entre título e estrela é 36px. Dia e hora são linhas com ícone de 24px.

Há um segundo `Agenda-carregado` (`1168:9350`, 440×956) com a mesma lista e um snackbar absoluto em x 25, y 756.

## Densidade

A quantidade visível é a de uma agenda do dia: vários cartões por secção, não um único destaque. O frame de skeleton repete barras de 376×56 no lugar dos cartões.

## Folhas e diálogo

As folhas de filtro têm largura 440, padding `48px 24px 56px`, gap 24px, raio `24px 24px 0px 0px` e traço superior `#52525B` de 1px. A folha `filter` (`915:6722`) usa padding `48px 24px 72px` e acrescenta os campos `Data inicial` e `Data final`.

Os detalhes (`1072:8220`, `1083:8252`) usam padding `48px 0px 0px`, gap 16px e altura 956px.

O diálogo sem nome (`1168:9299`) é uma coluna com padding 24px, gap 48px, fundo `#27272A` e raio 16px. O ícone de exclamação mede 36×36.
