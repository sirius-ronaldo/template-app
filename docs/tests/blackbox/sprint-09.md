# Roteiro caixa-preta — Sprint 09

## Objetivo

Validar o CRUD de aplicações, seus tipos, estados cadastral e efetivo e o impacto na autorização.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Usuário ADMIN ativo.
- Tipos de aplicação WEB ativo e LEGACY inativo.
- Permissão compatível e grupo com grant para a aplicação.

## Dados necessários

- Código `PORTAL`, nome `Portal` e tipo WEB.
- Um usuário não ADMIN associado ao grupo que possui o grant.

## Preparação do ambiente

1. Acesse a aplicação como ADMIN.
2. Entre em `Configurações > Aplicações`.

## Casos de teste

### 1. Criar aplicação

1. Clique em `Nova aplicação`.
2. Informe código, nome, descrição e tipo WEB ativo.
3. Salve.

Resultado esperado: a aplicação aparece com `Ativo` e estado efetivo `Ativo`.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 2. Validações e tipos disponíveis

1. Tente salvar código vazio, código com formato inválido e código já usado.
2. Abra novamente o formulário de nova aplicação.
3. Verifique os tipos disponíveis.

Resultado esperado: os erros são apresentados; somente tipos ativos são oferecidos para uma nova aplicação.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 3. Estado efetivo

1. Inative a aplicação.
2. Confirme a listagem.
3. Reative a aplicação e inative seu tipo.

Resultado esperado: o estado cadastral acompanha a aplicação, enquanto o estado efetivo fica inativo quando a aplicação ou o tipo está inativo.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 4. Alteração de tipo e autorização

1. Reative o tipo WEB e edite a aplicação para usar outro tipo ativo.
2. Com o usuário de teste, acesse a funcionalidade protegida pelo grant antigo.
3. Inative a aplicação ou o tipo e repita o acesso.

Resultado esperado: o vínculo de tipo é alterado; grants antigos incompatíveis permanecem cadastrados, mas deixam de autorizar quando a aplicação ou tipo está inativo/incompatível.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 5. Integridade na exclusão

1. Tente excluir uma aplicação que possua grants.
2. Exclua uma aplicação sem dados relacionados.

Resultado esperado: a primeira operação é recusada com orientação para inativar; a segunda é concluída.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

## Evidências / observações

____________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
