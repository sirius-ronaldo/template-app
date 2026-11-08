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

## 2026-11-08 — Sprint 05 — Infraestrutura comum da UI administrativa

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- `ViewRenderer` em PHP puro com layout opcional.
- `FlashBag` reutilizável para mensagens de sessão.
- Componente de paginação com padrão 25 e limite máximo 100.
- Componentes de flash, erro de campo, confirmação e paginação.
- Layout administrativo em `resources/views/layouts/app.php`.
- Partial HTMX para tabela, filtro, preservação de query string e `hx-push-url`.
- Tela administrativa piloto `Configurações > Segurança` protegida por ADMIN.
- Menu com `Minha senha` para autenticados e `Segurança` somente para ADMIN.
- Compatibilidade da infraestrutura com respostas full e HTMX.
- Testes automatizados de renderer, flash, paginação e visibilidade do menu.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-05.md`.

## 2026-11-08 — Sprint 06 — Usuários

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- DTO `UserInput` e `UserValidator` específicos para usuários.
- `UserRepository` com listagem filtrada, paginação, CRUD, status e senha.
- `UserManagementService` com validações, Argon2id e regras de proteção do ADMIN.
- CRUD administrativo de usuários protegido por ADMIN.
- Login case-sensitive e único, e-mail obrigatório e validado.
- Edição cadastral separada da alteração de senha.
- Reset administrativo de senha em tela e ação próprias.
- Ativação, inativação e exclusão respeitando integridade referencial.
- Bloqueio de auto-inativação e autoexclusão do ADMIN autenticado.
- Registro de mensagens operacionais sem dados sensíveis.
- Views administrativas com filtros, paginação e respostas full/HTMX.
- Testes unitários e de integração PostgreSQL para CRUD, senha, duplicidade e integridade.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-06.md`.

## 2026-11-08 — Sprint 07 — Grupos e Usuários × Grupos

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- DTO `GroupInput` e `GroupValidator` com normalização e regex de código.
- `GroupRepository` com CRUD, listagem, associação e sincronização transacional.
- `GroupManagementService` com proteção estrutural do grupo `ADMIN`.
- CRUD administrativo de grupos protegido por ADMIN.
- Tela orientada pelo usuário para associação múltipla usuário–grupo.
- Atualização em lote por diff, remoção de associações desmarcadas e rejeição de grupos inativos.
- Views administrativas com filtro, paginação e respostas full/HTMX.
- Testes unitários e PostgreSQL reais para CRUD, regex, ADMIN, associação, remoção e rollback.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-07.md`.

## 2026-11-08 — Sprint 08 — Catálogo de permissões, tipos e compatibilidade

Status: concluída tecnicamente; homologação caixa-preta pendente.

Implementações:

- CRUD administrativo de permissões com código, nome, descrição e status ativo.
- CRUD administrativo de tipos de aplicação com código, nome, descrição e status ativo.
- Normalização de códigos com trim e uppercase, além de validação do padrão `^[A-Z][A-Z0-9_]*$`.
- Bloqueio de códigos duplicados e tratamento de ativação, inativação e exclusão.
- Tela administrativa para seleção explícita das relações tipo de aplicação × permissão.
- Atualização em lote das relações em transação, com rollback quando houver permissão inativa ou inexistente.
- Permissões inativas não podem ser incluídas em novas relações.
- Remoção de relação não exclui grants de grupos; grants incompatíveis deixam de autorizar imediatamente.
- Rotas e navegação administrativa para os novos catálogos.
- Testes unitários e de integração PostgreSQL para CRUD, duplicidade, status, relações, rollback e impacto na autorização.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-08.md`.

## 2026-11-08 — Sprint 09 — Aplicações

Status: em implementação técnica; homologação caixa-preta pendente.

Implementações:

- CRUD administrativo de aplicações com código, nome, descrição, tipo e status ativo.
- Validação de código obrigatório, normalização e unicidade.
- Seleção de tipo obrigatória, oferecendo somente tipos ativos para novas aplicações.
- Alteração de tipo com preservação de aplicações vinculadas a tipos inativos.
- Exibição do estado cadastral e do estado efetivo, calculado por aplicação ativa e tipo ativo.
- Exclusão condicionada à integridade referencial, com orientação para inativação quando houver grants.
- Impacto imediato da inativação, alteração de tipo e inativação do tipo no `Authorizer`.
- Rotas, navegação administrativa, views full/HTMX e mensagens operacionais.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-09.md`.

## 2026-11-08 — Sprint 10 — Grupos × Aplicações × Permissões

Status: em implementação técnica; homologação caixa-preta pendente.

Implementações:

- Matriz administrativa hierárquica de tipo, aplicação e permissões válidas por grupo.
- Busca por código ou nome da aplicação e expansão/recolhimento por tipo e aplicação.
- Seleção explícita de permissões por aplicação, com salvamento manual.
- Validação backend de grupo, estado efetivo da aplicação e compatibilidade da permissão.
- Sincronização incremental de grants em transação, com rollback em relações inválidas.
- Tratamento especial do grupo ADMIN com acesso irrestrito e matriz não editável.
- Preservação dos grants cumulativos entre grupos.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-10.md`.

## 2026-11-08 — Sprint 11 — Manifesto YAML e Atualizar Aplicações

Status: em implementação técnica; homologação caixa-preta pendente.

Implementações:

- Manifesto estrutural em `config/security/manifest.yaml` usando `symfony/yaml`.
- Parser, DTOs e validações de versão, seções, campos, booleanos, códigos duplicados e referências.
- Prévia com diff de permissões, tipos, aplicações e relações tipo × permissão.
- Diagnóstico de registros existentes ausentes do manifesto sem exclusão ou inativação automática.
- Fluxo administrativo Validar, Prévia e Aplicar.
- Sincronização em uma única transação, na ordem permissões, tipos, aplicações e relações.
- Registro técnico da sincronização com administrador, versão, contagens e resultado, sem segredos.
- Roteiro caixa-preta em `docs/tests/blackbox/sprint-11.md`.

## 2026-11-08 — Sprint 12 — Consolidação, regressão e instalação limpa

Status: em implementação técnica; homologação caixa-preta pendente.

Implementações:

- PHPUnit configurado para forçar `APP_ENV=testing`.
- Testes de integração protegidos contra uso acidental do banco principal.
- Banco e schema de teste separados, com migrations Phinx executadas automaticamente na preparação.
- Limpeza isolada do schema de teste entre casos, sem intervenção SQL manual do desenvolvedor.
- Revisão da documentação de instalação e configuração das credenciais de teste.
- Roteiro caixa-preta fim a fim em `docs/tests/blackbox/sprint-12.md`.
