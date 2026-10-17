# Tarefa

O trabalho desta secção é o cliente marcar um horário: escolher barbeiro, serviço e dia, confirmar, e ver o resultado.

O primeiro olhar, nos frames `Agendar` de 956px, é o cartão do barbeiro, não um cartaz. O cartão `card-04` traz foto, nome e nota.

Nomes expandidos, todos com estrela `5.0`: Jardel, Caio, Iago e Léo.

Camadas presentes no ecrã, com o texto interno por expandir: `card-service`, chips, calendário, rádio, `like-button` e swiper.

Botões com rótulo lido:

- `Confirmar` no frame `1050:12306`, fundo `#3CE3F6`
- `Agendar` no frame `1050:12968`, fundo `#09B0C3`
- `Confirmar` na folha `1050:14681`, fundo `#09B0C3`

Confirmações:

- Template do sucesso: título `Corte agendado com sucesso!` e corpo `Você já garantiu seu horário!` mais `Confira todos os detalhes em Meus agendamentos. Se precisar, é só remarcar ou desmarcar por lá mesmo.` As expressões `Você já garantiu seu horário!` e `Meus agendamentos` estão a peso 700. O botão do template é `Entrar`.
- Frame `1050:13299`: botão `Veja seus agendamentos`.
- Frame `1050:13312`: título `Conta validada com sucesso!` e corpo `Agora é só fazer o login e agendar aquele corte com seu barbeiro de confiança!`

A cópia de marcação já em `frontend/src/i18n/pt-BR.ts` é outra superfície.
