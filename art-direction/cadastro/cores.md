# Cores

Valores pintados nos frames lidos. A cor em vigor no produto é a superfície escura com primário dourado em `oklch`, em `frontend/src/assets/main.css`.

| Papel nesta secção | Token | Valor | Onde |
|---|---|---|---|
| Canvas | `neutral/ultra-dark` | `#09090B` | fundo do ecrã e texto do botão primário |
| Superfície | `neutral/dark` | `#27272A` | folha, botão de voltar, barra `Navigation` |
| Campo | `neutral/base` | `#52525B` | fundo do input e do botão Google |
| Fio da folha | `neutral/base` | `#52525B` | traço superior de 1px |
| Fio do campo | `neutral/soft` | `#A1A1AA` | traço de 1px no repouso |
| Texto de título | — | `#FFFFFF` | `Crie uma conta` |
| Texto principal | `neutral/ultra-light` | `#FAFAFA` | rótulos, valor preenchido, `Já tem uma conta?`, botão Google |
| Texto de apoio | `neutral/light` | `#E4E4E7` | descrição e regras no vazio |
| Texto atenuado | `neutral/soft` | `#A1A1AA` | placeholder e `Criar com` |
| Acento | `primary/base` | `#3CE3F6` | botão `Criar conta` |
| Link | `primary/dark` | `#0BDCF4` | `Entrar` |
| A carregar | `primary/ultra-dark` | `#09B0C3` | botão do frame `criar-conta-carregando-dados` |
| Perigo | `danger/base` | `#E11D48` | traço de 2px do campo em erro |
| Mensagem | `danger/soft` | `#FB7185` | `Campo obrigatório`, `Preencha com um email válido`, `Preencha com um celular válido`, e as regras nesse estado |

## Estados

- Repouso do campo: fundo `#52525B`, traço 1px `#A1A1AA`, placeholder `#A1A1AA`.
- Valor escrito, no erro de dados: texto `#FAFAFA` dentro do campo.
- Erro: traço 2px `#E11D48`, ícone de exclamação 24px, mensagem `#FB7185`. No erro de campo obrigatório as quatro regras também passam a `#FB7185`. No erro de dados, as quatro regras ficam `#FB7185` mesmo com Senha e Confirmar senha ainda no traço de repouso.
- Primário: fundo `#3CE3F6`, texto `#09090B`.
- A carregar: fundo `#09B0C3`, sem rótulo, ícone de 48px.
- Secundário: fundo `#52525B`, texto `#FAFAFA`.
- Hover e foco: não há camada com esses estados nos frames lidos.
