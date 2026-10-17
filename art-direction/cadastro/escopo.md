# Escopo

A secção `Cadastro de conta` (`114:409`) tem seis frames directos, todos com largura 440px. Nenhum é a rota `/registo` do produto, que cria a barbearia.

| Peça | Nó | Medida | O que está lido |
|---|---|---|---|
| Form fora do viewport | `267:677` | 956px | `criar-conta-sem-form`. Folha em y 760. Texto não lido |
| Conta vazia | `267:647` | 1264px | `criar-conta-vazio`. Quatro campos, regras, `Criar conta`, Google, `Entrar` |
| Conta preenchida | `279:768` | 1264px | `criar-conta-preenchido`. Geometria igual à do vazio no metadata. Valores não lidos |
| A carregar | `279:1162` | 1264px | `criar-conta-carregando-dados`. Botão `279:1195` em `#09B0C3` com três pontos de 48px |
| Campo obrigatório | `301:796` | 1384px | Quatro vezes `Campo obrigatório`. Regras em `#FB7185` |
| Dados inválidos | `301:996` | 1328px | `Preencha com um email válido` e `Preencha com um celular válido`. Senhas no traço de repouso |

Estados presentes: vazio, preenchido (geometria), a carregar, campo obrigatório, dados inválidos, form abaixo do viewport. Hover e foco não estão desenhados.
