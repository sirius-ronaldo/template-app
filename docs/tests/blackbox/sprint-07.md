# Roteiro de teste caixa-preta — Sprint 07

## Objetivo

Validar o CRUD de grupos e a associação múltipla de usuários a grupos.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Migrations executadas com `make migrate`.
- Usuário ADMIN ativo.
- Navegador com cookies habilitados.

## Dados necessários

- Credenciais do ADMIN.
- Um usuário de teste.
- Grupos ativos, um grupo inativo e o grupo estrutural ADMIN.

## Preparação do ambiente

1. Autentique-se como ADMIN.
2. Acesse `Configurações > Segurança`.

## Casos de teste

### 1. CRUD e normalização de grupo

1. Crie um grupo usando código com espaços e letras minúsculas.
2. Edite nome e descrição.
3. Tente usar código iniciado por número ou com caracteres inválidos.

Resultado esperado: o código é salvo com trim e uppercase; dados inválidos são recusados.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Proteção do ADMIN

1. Tente inativar o grupo ADMIN.
2. Tente excluir o grupo ADMIN.
3. Edite nome e descrição do grupo ADMIN.

Resultado esperado: inativação e exclusão são recusadas; campos descritivos podem ser alterados.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Associação múltipla

1. Acesse a ação “Grupos” de um usuário.
2. Selecione dois ou mais grupos ativos.
3. Salve e consulte novamente.

Resultado esperado: todas as associações selecionadas permanecem vinculadas.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Remoção e grupo inativo

1. Desmarque um grupo anteriormente selecionado e salve.
2. Tente selecionar um grupo inativo.

Resultado esperado: a associação desmarcada é removida; grupo inativo não pode receber nova associação.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Associação ao ADMIN

1. Associe o usuário ao grupo ADMIN.
2. Remova a associação ao ADMIN mantendo outros grupos.

Resultado esperado: o ADMIN autenticado consegue administrar essa associação, e o diff preserva apenas os grupos selecionados.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Falha transacional

1. Envie uma seleção contendo um grupo válido e um grupo inativo ou inexistente.
2. Consulte novamente as associações do usuário.

Resultado esperado: a operação inteira é recusada e nenhuma associação parcial é gravada.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
