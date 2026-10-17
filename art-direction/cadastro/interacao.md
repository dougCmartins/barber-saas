# Interação

A plataforma desenhada é um telemóvel de 440px. `status-bar` (52px, hora `9:30`) e `Navigation` (24px, puxador 108×4) são chrome do aparelho no ficheiro. Não há anel de foco nem hover nos frames lidos.

## Caminho

Voltar é o botão circular com chevron. O form pede Email, Celular, Senha e Confirmar senha, todos com `*`. Senha e Confirmar senha mostram o olho de 24px. As quatro regras ficam sempre por baixo dos campos.

`Criar conta` é o botão de 58px. `Cadastrar com o Google` é o segundo botão, depois do rótulo `Criar com`. `Entrar` fecha o ecrã para quem já tem conta.

## Estados lidos

- Vazio (`267:647`): placeholders `ex: cliente@gmail.com`, `(21)90000-0000` e `******`. Regras em `#E4E4E7`.
- A carregar (`279:1195`): o botão passa a `#09B0C3`, perde o rótulo e mostra três pontos a 48px. O resto desse frame não foi relido.
- Campo obrigatório (`301:796`): os quatro campos ganham traço 2px `#E11D48`, ícone de exclamação e a linha `Campo obrigatório` em `#FB7185`. As quatro regras passam à mesma cor.
- Dados inválidos (`301:996`): Email mostra `joaogmail.com` e `Preencha com um email válido`. Celular mostra `(21)95466-00` e `Preencha com um celular válido`. As senhas ficam `****`, com traço de repouso e o olho. As regras ficam `#FB7185`.
- Sem form no viewport (`267:677`): a folha começa em y 760, abaixo da área visível de 956px. O texto desse frame não foi lido.

O frame preenchido (`279:768`) tem a mesma medida do vazio no metadata. Os valores dos campos não foram lidos.
