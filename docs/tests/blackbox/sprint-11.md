# Roteiro caixa-preta — Sprint 11

## Objetivo

Validar o fluxo Validar → Prévia → Aplicar do manifesto YAML de segurança.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Usuário ADMIN ativo.
- Acesso controlado ao arquivo `config/security/manifest.yaml`.
- Catálogo de segurança disponível para comparação.

## Dados necessários

Um manifesto válido contendo, por exemplo, a permissão `READ`, o tipo `WEB` com `READ` e a aplicação `PORTAL` do tipo `WEB`.

## Preparação do ambiente

1. Faça uma cópia de segurança do manifesto atual.
2. Altere somente os dados de teste no arquivo YAML.
3. Acesse a aplicação como ADMIN.
4. Abra `Manifesto` no menu administrativo.

## Casos de teste

### 1. Validar manifesto válido

1. Salve um YAML com `version: 1` e as três seções obrigatórias.
2. Clique em `Validar`.

Resultado esperado: o manifesto é aceito e nenhum diagnóstico de erro é exibido.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 2. Prévia sem escrita

1. Altere o nome de uma permissão ou aplicação no YAML.
2. Clique em `Prévia`.
3. Confirme os registros e relações indicados no diff.
4. Consulte o catálogo administrativo antes de aplicar.

Resultado esperado: a prévia mostra as contagens e não altera o banco.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 3. Aplicar manifesto

1. Com a prévia válida, clique em `Aplicar`.
2. Consulte permissões, tipos, aplicações e relações.

Resultado esperado: registros presentes são inseridos ou atualizados e as relações Tipo × Permissão seguem exatamente a lista declarada.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 4. Ausências sem ação destrutiva

1. Mantenha um registro existente no banco e remova-o do YAML.
2. Execute `Prévia` e depois `Aplicar`.

Resultado esperado: a tela diagnostica o registro ausente e ele não é excluído nem inativado automaticamente.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 5. Manifesto inválido e rollback

1. Altere a versão para `2`, remova uma seção obrigatória ou informe uma referência inexistente.
2. Clique em `Validar`.
3. Restaure o manifesto válido e introduza uma falha controlada em uma referência.
4. Clique em `Aplicar` e consulte o catálogo.

Resultado esperado: os diagnósticos são apresentados; o manifesto inválido não escreve e uma falha de aplicação não deixa alterações parciais.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

## Evidências / observações

____________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
