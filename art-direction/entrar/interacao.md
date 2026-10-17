# Interação

A plataforma desenhada é um telemóvel de 440px. `status-bar` (52px) e `Navigation` (24px) são chrome do aparelho no ficheiro. Foco e hover não estão no metadata.

## Caminho

`login ou cadastro` oferece duas acções do mesmo tamanho, 392×58, com 24px entre elas. Uma leva ao formulário. A outra não tem rótulo lido.

No formulário há dois campos, um checkbox de 143×46, um link de 167×44 na mesma linha, um botão de 58px, um segundo botão depois do divisor `entrar-com`, e um link de 40px no fundo da folha. Um botão de 48×48 fica no topo, à esquerda.

## Estados pela geometria

- Vazio (`114:554`): campos a 92px e 96px.
- Preenchido (`279:378`): a mesma medida do vazio. O valor escrito não foi lido.
- A carregar (`279:933`): a mesma medida do vazio. Não há, no metadata, uma mudança de altura do botão.
- Campo obrigatório (`279:576`): os dois campos passam a 124px e o ecrã a 998px.
- Dados incorrectos (`279:494`): a mesma altura do erro de campo obrigatório. A diferença de mensagem não foi lida.
- Sem form no viewport (`161:687`): a folha começa em y 760 e o conteúdo visível acaba antes.

Os textos de erro, do checkbox e dos botões ficam por ler.
