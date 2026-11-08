# Roteiro de teste caixa-preta — Sprint 03

## Objetivo

Validar proteção CSRF, segurança das mutações e alteração da própria senha.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Migrations executadas com `make migrate`.
- Usuário ativo criado e capaz de acessar `/Home`.
- Navegador com cookies habilitados.

## Dados necessários

- Login e senha atuais de um usuário ativo.
- Uma nova senha com pelo menos 8 caracteres.

## Preparação do ambiente

1. Abra uma janela anônima do navegador.
2. Acesse `/login` e autentique-se.

## Casos de teste

### 1. Token CSRF ausente ou inválido

1. Com a sessão autenticada, envie uma mutação sem o token CSRF ou altere o valor do campo oculto `_csrf` usando as ferramentas do navegador.

Resultado esperado: a requisição é bloqueada e nenhuma alteração é realizada.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Formulário de alteração de senha

1. Acesse `/minha-senha`.
2. Informe a senha atual, uma nova senha válida e a confirmação igual.
3. Envie o formulário.

Resultado esperado: a sessão é encerrada e o navegador é redirecionado para `/login`.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Senha atual incorreta

1. Faça login novamente com a senha antiga.
2. Em `/minha-senha`, informe senha atual incorreta e uma nova senha válida.

Resultado esperado: a alteração é recusada com mensagem de erro, sem encerrar a sessão.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Tamanho mínimo e confirmação

1. Teste uma nova senha com menos de 8 caracteres.
2. Teste duas senhas novas diferentes.

Resultado esperado: ambos os casos são recusados e a sessão permanece ativa.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Nova autenticação

1. Após uma troca bem-sucedida, tente entrar com a senha antiga.
2. Tente entrar com a nova senha.

Resultado esperado: a senha antiga falha e a nova senha autentica com sucesso.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Sessão expirada em requisição HTMX

1. Com uma sessão autenticada, simule ou aguarde a expiração da sessão.
2. Execute uma requisição HTMX para uma rota protegida.

Resultado esperado: a resposta orienta o navegador a redirecionar para `/login` usando `HX-Redirect`.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
