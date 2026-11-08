# Roteiro caixa-preta — Sprint 10

## Objetivo

Validar a configuração de grants pela matriz Grupo × Aplicação × Permissão e seu efeito imediato na autorização.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Usuário ADMIN ativo.
- Usuário de teste ativo, sem grupo ADMIN.
- Tipo e aplicação ativos.
- Pelo menos duas permissões ativas e válidas para o tipo da aplicação.

## Dados necessários

- Grupo `OPERADOR`.
- Aplicação `PORTAL`.
- Permissões `READ` e `WRITE`.
- Uma funcionalidade protegida por `PORTAL` + `READ`.

## Preparação do ambiente

1. Crie ou selecione o usuário de teste.
2. Crie ou selecione o grupo `OPERADOR`.
3. Associe o usuário de teste ao grupo.
4. Acesse a aplicação como ADMIN.
5. Abra `Configurações > Grupos > Permissões` para o grupo `OPERADOR`.

## Casos de teste

### 1. Matriz e filtro

1. Confirme a hierarquia por tipo e aplicação.
2. Recolha e expanda os blocos de tipo e aplicação.
3. Busque parte do código ou nome da aplicação.

Resultado esperado: somente tipos e aplicações efetivamente ativos aparecem; o filtro retorna a aplicação correspondente.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 2. Concessão explícita

1. Marque `READ` para `PORTAL`.
2. Não marque `WRITE`.
3. Clique em `Salvar permissões`.
4. Faça login com o usuário de teste.
5. Acesse a funcionalidade protegida por `PORTAL` + `READ`.

Resultado esperado: o acesso é permitido somente após o salvamento explícito.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 3. Remoção e efeito imediato

1. Volte à matriz como ADMIN.
2. Desmarque `READ` e salve.
3. Repita o acesso com o usuário de teste.

Resultado esperado: o usuário recebe acesso negado após a remoção do grant.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 4. Grants cumulativos

1. Associe o mesmo usuário a um segundo grupo ativo.
2. Conceda `READ` ao primeiro grupo e `WRITE` ao segundo.
3. Teste os dois acessos com o mesmo usuário.
4. Remova `READ` somente do primeiro grupo.

Resultado esperado: os grants dos grupos são cumulativos; remover um grant não remove o grant concedido pelo outro grupo.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 5. Grupo ADMIN e validações

1. Abra a tela de permissões do grupo ADMIN.
2. Tente enviar uma relação inválida ou uma permissão inativa, se possível pela ferramenta de teste.

Resultado esperado: ADMIN é apresentado como acesso irrestrito e não possui matriz editável; relações inválidas são rejeitadas sem alterar os grants anteriores.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

## Evidências / observações

____________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
