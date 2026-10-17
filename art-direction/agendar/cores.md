# Cores

Valores pintados em camadas expandidas da secção `974:5660`. A cor em vigor no produto é a superfície escura com primário dourado em `oklch`, em `frontend/src/assets/main.css`.

| Papel nesta secção | Valor | Onde |
|---|---|---|
| Canvas | `#09090B` | frames `Agendar` e confirmações; texto dos botões `#3CE3F6` |
| Folha | `#27272A` | folhas de calendário e folha de acção do sucesso |
| Fio | `#52525B` | traço superior de 1px dessas folhas |
| Título | `#FFFFFF` | `Corte agendado com sucesso!` e `Conta validada com sucesso!` |
| Corpo | `#E4E4E7` | parágrafos do sucesso |
| Primário | `#3CE3F6` | `Confirmar` em `1050:12306`, `Veja seus agendamentos`, `Entrar` |
| Primário escuro | `#09B0C3` | `Agendar` e o `Confirmar` da folha `1050:14681` |
| Foto | imagem | fundo do `card-04` |

## Estados

- Botão claro: `#3CE3F6` / `#09090B`, com `shadow/brand`.
- Botão escuro: `#09B0C3`, sem sombra na camada.
- Hover e foco: não há camada expandida com esses estados.

## No pacote, sem camada expandida

`#FAFAFA`, `#A1A1AA`, `#6DEAF8`, `rgba(217, 217, 217, 0)` e o gradiente `linear-gradient(180deg, rgba(0, 0, 0, 0) 11%, rgba(0, 0, 0, 0.21) 30%, rgba(0, 0, 0, 1) 100%)`.

O quadro da secção, fora dos ecrãs, usa `#52525B` com traço branco tracejado. Não é superfície de ecrã.
