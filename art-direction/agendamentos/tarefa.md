# Tarefa

O trabalho desta secção é uma agenda de agendamentos no telemóvel: ver a lista, filtrar, abrir o detalhe, desmarcar ou avaliar.

O primeiro olhar, no frame `Agenda-carregado` (`789:5541`), é a lista carregada. A largura do ecrã é 440px. No topo da lista há três dropdowns. Abaixo, secções com cartões. Não há cartaz de marca neste viewport.

Conteúdo que a lista carrega, pela estrutura do cartão `card-06`:

1. Prova visual: rectângulo de imagem, 96px de largura, raio 16px.
2. Título do cartão.
3. Dia, com ícone de calendário.
4. Hora, com ícone de relógio, e um badge de estado.
5. Num cartão, uma estrela com nota.

Filtros no ecrã: `Barbeiro`, `Período`, `Situação`.

Acções medidas no detalhe e no diálogo:

- `Desmarcar` no detalhe `1072:8220`
- `Avaliar` no detalhe `1083:8252`
- Diálogo `1168:9299`: título `Desmarcar serviço`, corpo `Tem certeza que deseja cancelar esse agendamento?`, botões `Não, obrigado` e `Desmarcar`
- Snackbar no segundo `Agenda-carregado` (`1168:9350`): `Serviço desmarcado.`

A cópia de produto já em `frontend/src/i18n/pt-BR.ts` (loja pública, login, marcação) é outra superfície. Estes rótulos são os da secção Figma.
