# Roteiro de teste caixa-preta — Sprint 02

## Objetivo

Validar login, proteção da página `/Home`, logout e expiração da sessão.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Migrations executadas com `make migrate`.
- Um usuário ativo criado pelo comando de inicialização da Sprint 01.
- Navegador com cookies habilitados.

## Dados necessários

- Login e senha de um usuário ativo.
- Login e senha incorretos para o caso de falha.

## Preparação do ambiente

1. Abra uma janela anônima do navegador.
2. Acesse a aplicação pela URL configurada no ambiente.

## Casos de teste

### 1. Acesso sem autenticação

1. Acesse `/Home` diretamente.

Resultado esperado: redirecionamento para `/login`.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Login inválido

1. Informe um login ou senha incorretos.
2. Envie o formulário.

Resultado esperado: operação recusada com mensagem genérica, sem informar se o login existe.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Login válido e página protegida

1. Informe as credenciais válidas.
2. Envie o formulário.

Resultado esperado: redirecionamento para `/Home` e exibição da página autenticada.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Usuário autenticado acessando o login

1. Com a sessão ativa, acesse `/login`.

Resultado esperado: redirecionamento para `/Home`.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Logout

1. Com a sessão ativa, envie o formulário “Sair”.
2. Tente acessar `/Home` novamente.

Resultado esperado: redirecionamento para `/login` após o logout.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Expiração por inatividade e duração máxima

1. Faça login e mantenha a sessão sem atividade por mais de 30 minutos.
2. Repita o teste mantendo a sessão por mais de 8 horas, mesmo realizando acessos.

Resultado esperado: a sessão expira e `/Home` redireciona para `/login` em ambos os casos.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento deste roteiro pelos stakeholders.
