# Changelog do projeto

Registro cronológico das implementações realizadas em cada sprint.

## 2026-09-14 — Sprint 00 — Preparação e baseline técnico

Status: concluída e aceita.

Implementações:

- Configuração centralizada de ambiente, timezone e nível de log.
- Validação de `APP_TIMEZONE` e aplicação do timezone no bootstrap.
- Conexão PDO compartilhada com PostgreSQL e configurações seguras de execução.
- Logger técnico em `storage/logs/app.log`.
- Configuração do Phinx para migrations PostgreSQL.
- Scripts Composer para testes, análise estática, estilo e validação completa.
- Ajustes de autoload PSR-4 e bootstrap mínimo da aplicação.
- Testes unitários de configuração e logger.

## 2026-11-08 — Sprint 01 — Estrutura do banco e primeiro ADMIN

Status: concluída e aceita.

Implementações:

- Migration Phinx completa do módulo de segurança com tabelas `seg_*`.
- Chaves primárias, estrangeiras, restrições únicas, índices e triggers de `updated_at`.
- Criação do grupo estrutural `ADMIN` sem criação automática de usuário.
- Comando `security:create-initial-admin` no dispatcher `bin/console`.
- Leitura das configurações de criação inicial pelo ambiente.
- Validação de configuração obrigatória, duplicidade de login e ADMIN existente.
- Hash de senha com Argon2id e transação na criação do usuário.
- Testes unitários do serviço de criação do primeiro ADMIN.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-01.md`.

## 2026-11-08 — Sprint 02 — Núcleo HTTP, sessão e autenticação

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- Núcleo `Application`, pipeline de middleware e `ExceptionHandler`.
- `PasswordHasher` com Argon2id, verificação e rehash de senha.
- `SessionManager` com regeneração de sessão, timeout de inatividade de 30 minutos e duração absoluta de 8 horas.
- `AuthenticationService` e `UserCredentialRepository` para autenticação por login case-sensitive.
- Confirmação do usuário ativo durante requisições autenticadas.
- Controllers de login e logout.
- Rotas `GET /login`, `POST /login`, `POST /logout` e `GET /Home`.
- Proteção da página `/Home` por middleware de autenticação.
- View de login e ação de logout.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-02.md`.
- Validação automatizada com PHPCS, PHPStan e PHPUnit.
