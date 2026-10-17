# Tipografia

Estilos de texto expandidos na secção. A face em vigor no produto é outra: Manrope para texto e Barlow Condensed para display, em `frontend/src/assets/main.css`.

| Estilo | Família | Peso | Tamanho | Linha | Tracking | Alinhamento | Onde aparece expandido |
|---|---|---|---|---|---|---|---|
| `title/md` | Anton | 400 | 28px | 32px | `0.01em` | esquerda | `Desmarcar serviço` |
| `body/lg` | Work Sans | 400 | 20px | 24px | não definido | esquerda | `Tem certeza que deseja cancelar esse agendamento?` |
| `label/lg` | Work Sans | 600 | 20px | 24px | não definido | esquerda | `Outro período`, `Data inicial`, `Data final` |
| `label/lg (62:292)` | Work Sans | 600 | 20px | 24px | não definido | centro | `Desmarcar`, `Avaliar`, `Não, obrigado` |
| `body/md` | Work Sans | 400 | 16px | 20px | não definido | esquerda | placeholders `01/02/2025` e `01/10/2025` |
| `label/md` | Work Sans | 600 | 16px | 20px | não definido | esquerda | definido no pacote; nenhum texto expandido desta secção o usa |

Há uma variante `title/md (2:362)` igual à primeira, com alinhamento à direita. Nenhum texto expandido desta secção a usa.

Papéis observados:

- Display: Anton só no título do diálogo de cancelar.
- Corpo longo: Work Sans 20/24 no parágrafo desse diálogo.
- Rótulo de controlo: Work Sans 20/24 semibold, à esquerda nos campos e centrado nos botões.
- Meta de campo: Work Sans 16/20 no placeholder.

O título do snackbar `Serviço desmarcado.` está em `body/md` (16/20, peso 400), cor `#FB7185`.
