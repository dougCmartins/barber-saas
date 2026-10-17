# Cores

Valores pintados em camadas expandidas da secção `691:3999`. A cor em vigor no produto é a superfície escura com primário dourado em `oklch`, em `frontend/src/assets/main.css`.

| Papel nesta secção | Valor | Onde |
|---|---|---|
| Canvas do ecrã | `#09090B` | `Agenda-carregado` |
| Superfície | `#27272A` | folhas, diálogo, snackbar, dropdown, uma barra de skeleton |
| Texto principal | `#FAFAFA` | corpo, rótulos, `Não, obrigado` |
| Texto de título | `#FFFFFF` | `Desmarcar serviço` |
| Texto atenuado | `#A1A1AA` | placeholders de data |
| Acento | `#3CE3F6` | traço dos dropdowns, chip `Outro período`, botão primário, rótulos `Desmarcar` e `Avaliar` no detalhe |
| Texto sobre acento | `#09090B` | chip seleccionado e botão primário |
| Aviso do snackbar | `#FB7185` | `Serviço desmarcado.` |
| Fio | `#52525B` | traço de 1px das folhas, snackbar e chip; fundo do botão `Não, obrigado`; segunda barra de skeleton |
| Barra inferior | `rgba(39, 39, 42, 0.9)` | `bottom-nav`, com `blur(6px)` |

O quadro da secção, fora dos ecrãs, usa `#52525B` com traço branco tracejado. Não é superfície de ecrã.

## Estados

- Seleccionado: chip `Outro período` em `#3CE3F6` / `#09090B`.
- Repouso do filtro: fundo `#27272A`, traço `#3CE3F6`.
- Primário: `#3CE3F6` / `#09090B`.
- Secundário: `#52525B` / `#FAFAFA`.
- Skeleton: barras `#27272A` e `#52525B`.
- Hover e foco: não há camada expandida com esses estados.

## No pacote de estilos, sem camada expandida

O mesmo payload traz estes valores, sem uma camada expandida desta secção a usá-los: `#E4E4E7`, `#6DEAF8`, o gradiente `linear-gradient(270deg, rgba(39, 39, 42, 1) 0%, rgba(82, 82, 91, 1) 50%, rgba(39, 39, 42, 1) 100%)` e `blur(4px)` sobre `rgba(9, 9, 11, 0.9)`.
