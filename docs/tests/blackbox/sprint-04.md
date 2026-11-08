# Roteiro de teste caixa-preta — Sprint 04

## Objetivo

Validar o bloqueio de rotas por autenticação, perfil ADMIN e combinação de aplicação com permissão.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Migrations executadas com `make migrate`.
- Usuários ativos de teste disponíveis.
- Aplicações, permissões, tipos e associações configurados conforme a matriz de segurança do ambiente.

## Dados necessários

- Um usuário sem grupo.
- Um usuário com uma permissão concedida.
- Um usuário com dois grupos, sendo a permissão concedida por apenas um deles.
- Um usuário ADMIN ativo.
- Cenários com usuário, grupo, aplicação, tipo e permissão inativos.

## Preparação do ambiente

1. Abra uma janela anônima do navegador para cada usuário.
2. Configure ou solicite ao responsável pelos dados de segurança os usuários e as associações necessárias.
3. Identifique uma rota protegida por aplicação e permissão e uma rota protegida por ADMIN.

## Casos de teste

### 1. Rota protegida sem autenticação

1. Acesse a rota protegida sem estar autenticado.

Resultado esperado: redirecionamento para `/login`.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Usuário sem grupo ou grant

1. Autentique-se com o usuário sem grupo.
2. Acesse a rota protegida por aplicação e permissão.

Resultado esperado: resposta 403 e acesso negado.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Grant correto e grants cumulativos

1. Autentique-se com o usuário que possui o grant correto.
2. Acesse a rota protegida.
3. Repita com o usuário que possui dois grupos, com o grant em apenas um deles.

Resultado esperado: ambos os acessos são permitidos quando todos os itens da matriz estão ativos.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. ADMIN ativo

1. Autentique-se com um usuário ADMIN ativo.
2. Acesse a rota protegida.

Resultado esperado: acesso permitido pelo tratamento especial de ADMIN.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Elementos inativos ou permissão incompatível

1. Teste, individualmente, usuário, grupo, aplicação, tipo ou permissão inativos.
2. Teste uma permissão que não seja válida para o tipo da aplicação.

Resultado esperado: todos os acessos são negados com 403.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Métodos não permitidos

1. Envie uma requisição usando método não registrado para uma rota existente.

Resultado esperado: resposta 405 com o header `Allow` informando os métodos aceitos.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
