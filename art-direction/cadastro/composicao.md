# Composição

Os ecrãs medem 440px de largura.

## Topo

`main` tem `padding-top: 68px` e `padding-bottom: 24px`. A `top-bar` é uma coluna com gap 24px e padding horizontal 24px. O botão de voltar mede 48×48, padding 12px, ícone 24px. O bloco de texto tem gap 12px: o título ocupa 32px de altura e a descrição 48px, ambos a 392px de largura.

## Folha

Em `criar-conta-vazio`, `criar-conta-preenchido` e `criar-conta-carregando-dados` o frame tem 1264px de altura. O conteúdo começa com `padding-top: 32px`. A folha tem fundo `#27272A`, traço só no topo de 1px `#52525B`, raio `24px 24px 0 0`, padding 24px e gap 48px entre o form, o bloco `Criar com` e o link.

O form é uma coluna com gap 24px. Os campos também têm gap 24px. Cada campo é label e caixa com gap 8px. A caixa tem padding `20px 16px` e gap 8px até o ícone. A lista de regras tem gap 8px; cada item é ícone 24px mais texto, gap 8px. A última regra ocupa 40px de altura; as outras, 24px.

O botão `Criar conta` mede 392×58. O bloco `Criar com` tem gap 24px: uma linha com o rótulo ao centro (gap 8px) e o botão Google, também 392×58. O link do fundo tem padding `8px 16px` e 40px de altura.

## Sem formulário visível

`criar-conta-sem-form` (`267:677`) mede 956px. A folha está em y 760 dentro de um conteúdo que começa em y 232 e tem 700px, por isso o form fica abaixo do viewport. Nesse frame a folha ainda contém dois campos e um botão, não os quatro.

## Erros

`criar-conta-erro-campo-obrigatório` mede 1384px. Os quatro campos ganham a linha `Campo obrigatório` e passam a 124px no metadata.

`criar-conta-erro-dados` mede 1328px. Só Email e Celular ganham mensagem e ficam a 124px. Senha e Confirmar senha ficam a 96px, como no vazio.
