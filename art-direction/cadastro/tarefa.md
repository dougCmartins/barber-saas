# Tarefa

O trabalho desta secção é criar a conta do cliente para poder agendar.

O primeiro olhar, em `criar-conta-vazio` (`267:647`), é o título e o formulário. Não há cartaz.

Conteúdo que o ecrã carrega:

1. Voltar, botão circular de 48px com chevron de 24px.
2. Título `Crie uma conta`.
3. Descrição `Informe seus dados e comece a agendar seus serviços com facilidade!`
4. Campos obrigatórios, marcados com `*`: `Email`, `Celular`, `Senha`, `Confirmar senha`.
5. Quatro regras da senha, cada uma com um ícone de 24px.
6. Acção `Criar conta`.
7. Divisor `Criar com` e `Cadastrar com o Google`.
8. `Já tem uma conta?` e o link `Entrar`.

Placeholders no vazio: `ex: cliente@gmail.com`, `(21)90000-0000`, `******` nas duas senhas.

No erro de dados, os valores lidos são `joaogmail.com`, `(21)95466-00` e `****` nas senhas. As mensagens são `Preencha com um email válido` e `Preencha com um celular válido`.

No erro de campo obrigatório, os quatro campos mostram `Campo obrigatório`.

A cópia de produto em `frontend/src/i18n/pt-BR.ts` (criar a barbearia) é outra superfície. Estes rótulos são os da secção Figma.
