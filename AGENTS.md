# AGENTS.md

## Contexto
Template base para projetos PHP 8.2 puros, em monolito tradicional com camadas simples.
Persistencia PostgreSQL por PDO. Frontend HTMX e Tailwind CSS.

## Ambiente
- Use `docker compose` para PHP e Composer.
- Nao execute PHP ou Composer diretamente no host.
- O servico da aplicacao se chama `app`.
- PostgreSQL compartilhado: hostname `dev-postgres`.

## Comandos
- Subir: `make up`
- Shell: `make shell`
- Testes: `make test`
- Analise: `make analyse`
- Validacao completa: `make validate`
- CSS de producao: `make css-build`

## Arquitetura
Fluxo principal: Controller -> Service -> Repository -> PDO.
- Controllers recebem a requisicao e retornam respostas/views.
- Services concentram regras de negocio.
- Repositories concentram todo SQL.
- Views nao executam consultas e escapam saida HTML.

## Regras
- Use `declare(strict_types=1);`.
- Siga PSR-12 e PSR-4.
- Quando adicionar comentarios no codigo, escreva em PT-BR, de forma sucinta, clara e objetiva; evite verbosidade e comentarios redundantes.
- Use prepared statements; nao concatene entrada do usuario em SQL.
- Nao altere `.env` nem exponha credenciais.
- Nao remova volumes Docker sem autorizacao explicita.
- Apos alteracoes, execute `make validate`.
- Informe arquivos alterados, comandos executados e limitacoes.
