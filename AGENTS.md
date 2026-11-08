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

## Sprints, testes e homologacao
- Ao concluir uma sprint, finalize as implementacoes previstas, execute os testes automatizados aplicaveis e apresente um resumo das implementacoes e dos resultados tecnicos.
- Quando houver funcionalidades testaveis externamente, gere obrigatoriamente um roteiro de teste caixa-preta em Markdown, preferencialmente em `docs/tests/blackbox/sprint-XX.md`.
- A execucao dos roteiros caixa-preta pelos stakeholders pode ocorrer posteriormente, conforme a disponibilidade deles, e a ausencia desses resultados nao impede a conclusao tecnica da sprint nem o inicio da sprint seguinte.
- Os testes automatizados previstos para cada sprint continuam obrigatorios. Falhas tecnicas conhecidas devem ser registradas e tratadas; nao devem ser ignoradas nem consideradas resolvidas sem correcao ou justificativa tecnica explicita.
- A ordem explicita do responsavel pelo projeto para iniciar a proxima sprint autoriza o avanco do desenvolvimento. O Codex nao deve avancar automaticamente para a proxima sprint.
- Problemas identificados posteriormente na homologacao caixa-preta devem ser registrados e tratados, mesmo que o projeto ja esteja em uma sprint posterior.
- Homologacao funcional pendente nao deve ser apresentada como concluida ou aprovada.
- O arquivo `dev_changelog.MD` identifica a sprint corrente. Ao receber ordem explicita para iniciar a proxima sprint, acrescente `ok` a sprint anterior e registre a nova sprint sem `ok`.
- O marcador `ok` representa autorizacao para avancar no desenvolvimento, nao necessariamente aprovacao dos testes caixa-preta pelos stakeholders.

## Regras
- Use `declare(strict_types=1);`.
- Siga PSR-12 e PSR-4.
- Quando adicionar comentarios no codigo, escreva em PT-BR, de forma sucinta, clara e objetiva; evite verbosidade e comentarios redundantes.
- Use prepared statements; nao concatene entrada do usuario em SQL.
- Nao altere `.env` nem exponha credenciais.
- Nao remova volumes Docker sem autorizacao explicita.
- Apos alteracoes, execute `make validate`.
- Informe arquivos alterados, comandos executados e limitacoes.
