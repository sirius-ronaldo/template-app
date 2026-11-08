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

## 2026-11-08 — Sprint 03 — CSRF, segurança web e alteração da própria senha

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- `CsrfTokenManager` com token por sessão, `random_bytes()` e validação com `hash_equals()`.
- `CsrfMiddleware` protegendo POST, PUT, PATCH e DELETE, com suporte a formulários tradicionais e HTMX.
- Headers de segurança `X-Content-Type-Options`, `Referrer-Policy` e `X-Frame-Options`.
- Helper `View::e()` com `htmlspecialchars()` usando `ENT_QUOTES | ENT_SUBSTITUTE`.
- Rotas autenticadas `GET /minha-senha` e `POST /minha-senha`.
- Validação de senha atual, mínimo de 8 caracteres e confirmação da nova senha.
- Invalidação da sessão após troca bem-sucedida e redirecionamento para `/login`.
- Resposta `HX-Redirect: /login` para sessão expirada em requisições HTMX.
- Testes automatizados de CSRF e troca de senha.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-03.md`.

## 2026-11-08 — Sprint 04 — Autorização e ADMIN

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- `AuthorizationRepository` com SQL centralizado para usuário, ADMIN e grants.
- `AdminChecker` e `Authorizer` com `can()` e `authorize()`.
- Tratamento especial para ADMIN ativo e grants cumulativos entre grupos ativos.
- Validação de usuário, grupo, aplicação, tipo, permissão e compatibilidade da permissão com o tipo.
- Metadata de rotas para `auth`, `admin`, `application` e `permission`.
- Rejeição de metadata inconsistente no registro de rotas.
- Inclusão automática de CSRF antes da autenticação e autorização em mutações.
- `AdminMiddleware` e `AuthorizationMiddleware`.
- Respostas 403, 404 e 405, incluindo header `Allow` quando aplicável.
- Testes automatizados unitários e de integração contra PostgreSQL real.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-04.md`.
