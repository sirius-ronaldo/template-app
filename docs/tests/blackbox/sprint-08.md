# Roteiro de teste caixa-preta — Sprint 08

## Objetivo

Validar os CRUDs de permissões e tipos de aplicação e a compatibilidade tipo × permissão.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Estrutura atualizada com `make migrate`.
- Usuário ADMIN ativo.
- Navegador com cookies habilitados.

## Dados necessários

- Uma permissão ativa e uma permissão inativa.
- Um tipo de aplicação ativo.
- Uma aplicação vinculada ao tipo e um grupo com grant da permissão.

## Preparação do ambiente

1. Autentique-se como ADMIN.
2. Acesse `Configurações > Segurança`.

## Casos de teste

### 1. CRUD e normalização de permissão

1. Crie uma permissão com código contendo espaços e letras minúsculas.
2. Edite nome e descrição.
3. Tente usar código iniciado por número, com hífen ou duplicado.

Resultado esperado: o código é salvo com trim e uppercase; formatos inválidos e duplicidades são recusados.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. CRUD e status de tipo de aplicação

1. Crie um tipo com código válido.
2. Edite os dados e inative o tipo.
3. Reative o tipo e tente excluir um tipo usado por aplicação.

Resultado esperado: criação, edição e alternância de status funcionam; a integridade referencial impede exclusão indevida.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Associação tipo × permissão

1. Acesse a ação de permissões de um tipo.
2. Selecione duas permissões ativas e salve.
3. Consulte novamente a tela.

Resultado esperado: as relações selecionadas permanecem associadas e a permissão inativa não é oferecida para nova seleção.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Remoção de relação e grant existente

1. Mantenha um grant de grupo para uma permissão compatível com o tipo.
2. Remova a relação tipo × permissão.
3. Tente acessar a aplicação com o usuário do grupo.

Resultado esperado: o registro do grant permanece, mas a autorização efetiva é negada enquanto a compatibilidade não existir.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Inativação de permissão

1. Inative uma permissão que possua relação ou grant.
2. Tente criar uma nova relação com ela.
3. Tente autorizar o grant existente.

Resultado esperado: a permissão inativa não pode entrar em nova relação e não produz autorização efetiva.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Falha transacional

1. Envie uma seleção contendo uma permissão ativa e uma permissão inativa ou inexistente.
2. Consulte novamente as relações do tipo.

Resultado esperado: a operação inteira é recusada e nenhuma alteração parcial é gravada.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
