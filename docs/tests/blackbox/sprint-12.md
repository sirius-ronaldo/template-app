# Roteiro caixa-preta — Sprint 12

## Objetivo

Validar a instalação limpa e os principais fluxos do módulo de segurança consolidado.

## Pré-requisitos

- PostgreSQL e a rede Docker compartilhada disponíveis.
- Arquivo `.env` configurado com banco de desenvolvimento e banco de teste separados.
- Nenhuma intervenção SQL manual realizada.

## Dados necessários

- Credenciais temporárias para criação do primeiro ADMIN.
- Um manifesto YAML válido para teste.
- Um usuário comum, grupo, aplicação e permissão para validar autorização.

## Preparação do ambiente

1. Em uma base de desenvolvimento limpa, configure `.env` a partir de `.env.example`.
2. Execute `make up` e `make install`.
3. Execute `make migrate`.
4. Execute `docker compose exec app php bin/console security:create-initial-admin`.
5. Remova a senha inicial do `.env` após a criação do ADMIN.

## Casos de teste

### 1. Instalação e login

1. Acesse `/login`.
2. Informe as credenciais do ADMIN criado.
3. Acesse `/Home`.

Resultado esperado: o login funciona, a página inicial é exibida e o menu administrativo está disponível.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 2. Segurança de sessão e CSRF

1. Encerre a sessão e tente acessar uma rota autenticada.
2. Envie um formulário mutável sem token CSRF.
3. Altere a própria senha com dados válidos e inválidos.

Resultado esperado: sessão encerrada recebe redirecionamento/login, requisição sem CSRF é recusada e a troca de senha aplica as validações previstas.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 3. Cadastros e associações

1. Crie usuário e grupo.
2. Associe o usuário ao grupo.
3. Crie permissão, tipo, aplicação e relação Tipo × Permissão.
4. Configure o grant na matriz do grupo.

Resultado esperado: os cadastros são persistidos e a matriz permite selecionar somente relações válidas e ativas.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 4. Autorização efetiva

1. Faça login com o usuário comum.
2. Acesse a funcionalidade protegida pelo grant.
3. Remova o grant como ADMIN e repita o acesso.
4. Inative aplicação, tipo ou permissão e repita o acesso.

Resultado esperado: o acesso permitido torna-se negado imediatamente quando o grant é removido ou algum elemento efetivo fica inativo.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 5. Manifesto YAML

1. Acesse `Manifesto` como ADMIN.
2. Altere controladamente `config/security/manifest.yaml`.
3. Execute `Validar`, `Prévia` e `Aplicar`.
4. Remova um registro do YAML e confirme o diagnóstico.

Resultado esperado: validação e prévia não escrevem; aplicação ocorre em transação; registros ausentes não são excluídos nem inativados.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

### 6. Regressão automatizada

1. Execute `make validate`.

Resultado esperado: PHPCS, PHPStan e PHPUnit concluem sem falhas, usando PostgreSQL real no banco de teste separado.

Resultado obtido: ____________________

Status: [ ] APROVADO [ ] REPROVADO [ ] BLOQUEADO

## Evidências / observações

____________________________________________________________

## Critério de aprovação

Todos os casos devem estar como APROVADO. A homologação funcional permanece pendente até o preenchimento pelos stakeholders.
