# Escopo

A secção `Entrar na conta` (`114:290`) tem sete frames directos, todos com largura 440px. O login do produto é a rota `/login`.

| Peça | Nó | Medida | O que está medido |
|---|---|---|---|
| Entrada | `114:512` | 956px | `login ou cadastro`. Logo 88px, título 32px, dois botões 392×58 |
| Form abaixo do ecrã | `161:687` | 956px | `login-sem-form`. Folha em y 760 |
| Vazio | `114:554` | 956px | dois campos, checkbox, divisor `entrar-com`, dois botões, link |
| Preenchido | `279:378` | 956px | a mesma geometria do vazio. Valores não lidos |
| A carregar | `279:933` | 956px | a mesma geometria do vazio. Sem mudança de altura do botão no metadata |
| Campo obrigatório | `279:576` | 998px | campos a 124px |
| Dados incorrectos | `279:494` | 998px | campos a 124px. A mensagem não foi lida |

Texto, cor, raio e sombra destes frames não foram lidos. Hover e foco não estão no metadata.
