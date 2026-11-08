# Roteiro de teste caixa-preta — Sprint 06

## Objetivo

Validar o CRUD administrativo de usuários, reset de senha, status, integridade e proteções do ADMIN.

## Pré-requisitos

- Aplicação iniciada com `make up`.
- Migrations executadas com `make migrate`.
- Usuário ADMIN ativo.
- Navegador com cookies habilitados.

## Dados necessários

- Credenciais do ADMIN.
- Um usuário de teste que possa ser criado, editado, inativado e excluído.
- Uma senha inicial e uma nova senha para reset.

## Preparação do ambiente

1. Autentique-se como ADMIN.
2. Acesse `Configurações > Segurança > Administrar usuários`.

## Casos de teste

### 1. Listagem, filtro e paginação

1. Consulte a lista de usuários.
2. Use o filtro por login, nome ou e-mail.
3. Informe tamanhos de página 25 e acima de 100.

Resultado esperado: a lista filtra corretamente, o padrão é 25 e o limite máximo é 100.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Criação e validações

1. Crie um usuário com login, nome, e-mail, status ativo e senha válida.
2. Tente criar outro usuário com o mesmo login.
3. Tente criar login com diferença apenas de maiúsculas/minúsculas.
4. Tente usar e-mail inválido, senha curta e confirmação diferente.

Resultado esperado: dados válidos são aceitos; login idêntico é recusado; login com caixa diferente é permitido; dados inválidos retornam erro 422.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Edição cadastral

1. Edite nome, e-mail e status do usuário.
2. Verifique que a senha não é alterada pela tela de edição.

Resultado esperado: dados cadastrais são atualizados sem exibir ou misturar hash/senha.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Ativação e inativação

1. Inative o usuário de teste.
2. Ative-o novamente.

Resultado esperado: o status muda e o usuário inativo não consegue autenticar.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Reset administrativo de senha

1. Acesse a ação “Resetar senha”.
2. Informe nova senha válida e confirmação.
3. Teste a senha antiga e a nova senha no login.

Resultado esperado: a senha antiga falha e a nova senha autentica; o hash nunca é exibido.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 6. Proteções do ADMIN e integridade

1. Tente inativar o próprio ADMIN.
2. Tente excluir o próprio ADMIN.
3. Tente excluir um usuário associado a grupo.

Resultado esperado: auto-inativação e autoexclusão são recusadas; exclusão com dependência é recusada sem remover os dados relacionados.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
