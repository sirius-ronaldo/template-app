# Sprint 01 — Instalação e primeiro ADMIN

## Objetivo

Validar a instalação da estrutura de segurança e a criação controlada do primeiro administrador.

## Pré-requisitos

- Docker e a rede `development` disponíveis.
- Banco PostgreSQL acessível pelo serviço `app`.
- Código do template atualizado.

## Dados necessários

- Login, nome, e-mail e senha temporária para o primeiro ADMIN.
- A senha deve ser tratada como segredo e removida do `.env` após o uso.

## Preparação do ambiente

1. Copie `.env.example` para `.env` e configure o banco.
2. Suba a aplicação com `make up`.
3. Execute `make migrate`.
4. Confirme que a migration terminou sem erro.

## Casos de teste

### 1. Migration em banco vazio

Passos:

1. Execute `make migrate`.
2. Consulte a saída do comando.

Resultado esperado: a migration cria a estrutura `seg_*`, o grupo `ADMIN` e termina com sucesso.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 2. Configuração obrigatória ausente

Passos:

1. Remova uma das variáveis `INITIAL_ADMIN_*` do ambiente.
2. Execute `docker compose exec app php bin/console security:create-initial-admin`.

Resultado esperado: o comando é recusado e identifica a configuração ausente, sem criar usuário.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 3. Criação do primeiro ADMIN

Passos:

1. Configure as quatro variáveis `INITIAL_ADMIN_*` com dados de teste.
2. Execute `docker compose exec app php bin/console security:create-initial-admin`.
3. Remova `INITIAL_ADMIN_PASSWORD` do `.env`.

Resultado esperado: o comando informa sucesso, não exibe senha ou hash e recomenda remover a senha inicial.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 4. Reexecução com ADMIN existente

Passos:

1. Execute novamente o comando com os mesmos dados.

Resultado esperado: a operação é recusada porque já existe um usuário ADMIN; nenhum usuário é sobrescrito.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

### 5. Login já existente sem ADMIN

Passos:

1. Em uma base sem associação ADMIN, cadastre previamente um usuário com o login escolhido.
2. Execute o comando de criação do primeiro ADMIN com esse login.

Resultado esperado: a operação é recusada por login existente e o usuário não é promovido.

Resultado obtido: ______________________________________________

Status: APROVADO / REPROVADO / BLOQUEADO

## Evidências / observações

__________________________________________________________________

## Critério de aprovação

Todos os casos aplicáveis devem estar APROVADOS; qualquer falha de segurança, sobrescrita ou exposição de senha reprova o roteiro.
