# Roteiro de teste caixa-preta — Sprint 05

## Objetivo

Validar o layout administrativo, menu por perfil, flash, filtro, paginação, respostas HTMX e erros de validação.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Usuário autenticado comum.
- Usuário ADMIN ativo.
- Navegador com cookies e JavaScript habilitados.

## Dados necessários

- Credenciais do usuário comum e do ADMIN.
- Uma rota ou formulário com resposta de validação 422.

## Preparação do ambiente

1. Abra uma janela anônima para cada perfil.
2. Acesse `/login` e autentique-se.

## Casos de teste

### 1. Menu do usuário comum

1. Acesse `/Home` com o usuário comum.

Resultado esperado: “Minha senha” aparece no menu; “Segurança” não aparece.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Menu ADMIN e tela piloto

1. Acesse `/Home` com o ADMIN.
2. Clique em “Segurança”.

Resultado esperado: o item aparece e a tela `Configurações > Segurança` é exibida.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Filtro com HTMX

1. Na tela piloto, preencha o filtro.
2. Envie o formulário.

Resultado esperado: somente o conteúdo da tabela é atualizado, a URL é preservada e a página não recarrega integralmente.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Paginação

1. Acesse a tela piloto com parâmetros de página e tamanho.
2. Teste tamanho superior a 100.

Resultado esperado: o tamanho padrão é 25, o limite máximo é 100 e filtros permanecem na URL.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Flash e validação 422

1. Execute uma operação que gere mensagem de sucesso ou erro.
2. Envie um formulário com dados inválidos.
3. Repita a operação usando HTMX.

Resultado esperado: a mensagem flash aparece uma vez; a resposta full e a resposta HTMX exibem o erro de validação sem expor detalhes internos.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
