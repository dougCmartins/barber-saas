# Composição

Sete ecrãs de 440px. `status-bar` mede 52px. `Navigation` mede 24px e fica no fundo (y 932 nos frames de 956px, y 974 nos de 998px). São chrome do telemóvel no ficheiro.

## Entrada

`login ou cadastro` (`114:512`) tem 956px. A `top-bar` começa em y 108 e mede 440×236. O logo está centrado: caixa 152×104, dentro dela um quadro 104×104, e o rectângulo do logo a 88×88 com margem 8px. O bloco de texto começa em y 144, 440×92. O título mede 392×32. A descrição começa em y 44 e mede 392×48, portanto há 12px entre os dois.

Os dois botões ficam num bloco de 440×140, a x 24, cada um 392×58. O segundo começa em y 82, portanto o intervalo entre eles é 24px.

## Formulário

`login-vazio` (`114:554`) tem 956px. A `top-bar` começa em y 68, mede 440×164, e inclui um botão de 48×48 em x 24. O texto começa em y 72 (24px abaixo do botão), 392×92, com título 392×32 e descrição a y 44, 392×48.

O conteúdo começa em y 232. A folha começa 32px abaixo, 440×668. O form tem margem 24px, 392×364. Dois campos: 392×92 e, em y 116, 392×96. O intervalo é 24px.

A linha de acção mede 392×128. O checkbox mede 143×46. O link ao lado mede 167×44 e começa em x 225. O botão começa em y 70 e mede 392×58.

O bloco `entrar-com` começa em y 436, 392×102. Entre o fim do form (y 388) e esse bloco há 48px. O divisor mede 392×20: dois traços de 144px e um rótulo de 88×20 em x 152. O segundo botão começa em y 44 e mede 392×58.

O link do fundo começa em y 586, 392×40. O texto interno mede 360×24, com margem x 16 e y 8.

`login-preenchido` e `login-carregando-dados` repetem estas medidas.

## Erros

`login-erro-campo-obrigatório` e `login-erro-dados-incorretos` medem 998px. Cada campo passa a 392×124. O bloco de campos mede 392×272. O form mede 392×424. A folha mede 440×710.

## Form fora do viewport

`login-sem-form` (`161:687`) mede 956px. O topo é o mesmo do vazio. A folha começa em y 760 dentro de um conteúdo que só tem 700px de altura, por isso o form fica abaixo do ecrã. Dentro dessa folha há só dois campos (392×92 e 392×96) e um botão 392×58.
