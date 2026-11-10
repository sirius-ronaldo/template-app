# Plano de aplicação do padrão de UI

## Objetivo

Aplicar ao Template App a linguagem visual documentada em
`/home/ronaldo/desenvolvimento/design_ui`, preservando PHP 8.2 puro, HTMX,
Tailwind CSS, autenticação, autorização, contratos de serviços e regras de
negócio existentes.

Este documento registra somente a avaliação e o plano de execução. Sua criação
não inicia uma nova sprint nem autoriza a implementação das alterações visuais.

## Referências analisadas

- `prompt-atualizacao-template.md`;
- `design-tokens.md`;
- `component-catalog.md`;
- `layout-specification.md`;
- `responsive-specification.md`;
- `accessibility-rules.md`;
- `migration-map.md`;
- `acceptance-criteria.md`;
- ativos de referência em `assets/`;
- capturas desktop e mobile nos temas claro e escuro;
- script Playwright `capture.mjs`;
- estrutura atual de views, layouts, CSS e testes do Template App.

`node_modules` foi considerado somente como dependência gerada, não como
conteúdo autoral da especificação.

## Avaliação do material

A aplicação do padrão é viável e está bem especificada. O pacote cobre tokens,
componentes, responsividade, acessibilidade, temas e critérios de aceite. As
capturas permitem compreender a hierarquia, a densidade e o comportamento
esperado de navegação, formulários, cards e tabelas.

### Pontos positivos

- Separação clara entre a linguagem visual e o domínio do SOL.
- Tokens semânticos adequados para temas claro e escuro.
- Layout desktop e mobile definido, incluindo breakpoint principal de 860 px.
- Requisitos explícitos para foco, teclado, semântica, escape HTML e redução de
  movimento.
- Restrições compatíveis com a stack atual do template.
- Critérios de aceite objetivos para CSS, validação, responsividade e ausência
  de acoplamento ao SOL.

### Pontos de atenção no pacote

- O `README.md` da referência informa que não há capturas geradas, embora elas
  estejam presentes.
- O `capture.mjs` usa rotas, labels, `data-sol-theme` e a chave `sol-theme`,
  específicos do SOL. Ele deve servir apenas como referência.
- Os PNGs contêm a identidade Sírius SOL e não podem se tornar dependências do
  template.
- O tema claro apresenta uma paleta resumida, sem atribuir explicitamente todos
  os tokens semânticos.
- Não há especificação formal de iconografia, matriz de contraste ou regressão
  visual automatizada.
- A fonte Manrope é preferencial, mas não está distribuída no pacote. A solução
  não deve depender obrigatoriamente de uma fonte remota.

## Diagnóstico do template

O template possui dois pipelines de renderização:

| Área | Renderer | Layout |
|---|---|---|
| Home, login, senha e erros | `App\Support\View` | `app/View/layouts/*.php` |
| Administração e fragmentos HTMX | `App\Support\ViewRenderer` | `resources/views/layouts/app.php` |

### Impactos da estrutura atual

- O shell autenticado está duplicado.
- O layout administrativo não recebe hoje o mesmo contexto de autenticação,
  estado ADMIN e navegação recebido pelo layout legado.
- Flashes e CSRF chegam aos layouts por mecanismos diferentes.
- Alterar somente um dos layouts produziria uma interface inconsistente.
- Uma substituição completa dos renderers aumentaria o escopo e o risco
  funcional de uma atualização que deve permanecer visual.

### Situação atual da interface

- `resources/css/input.css` contém apenas o import e as fontes do Tailwind.
- Não há JavaScript local para tema, drawer ou grupos expansíveis.
- Não há estrutura genérica de branding.
- Há muitas combinações de classes Tailwind repetidas nas views.
- O tratamento de overflow horizontal em tabelas não é uniforme.
- Foco, estados vazios, badges e estados de interação não estão padronizados.
- O logout POST existe no layout legado, mas não está disponível no layout
  administrativo.
- O repositório estava limpo no momento da avaliação.
- `docs/dev_changelog.MD` registra as Sprints 00 a 12 como `ok`. Uma futura
  sprint de UI poderá ser registrada como Sprint 13 somente após autorização
  explícita.

## Abordagem recomendada

Manter os dois renderers durante a atualização, compartilhando contexto visual,
fragmentos estruturais e classes CSS. Essa estratégia evita uma refatoração
funcional ampla e permite que todas as áreas recebam o mesmo padrão.

A implementação deve adotar:

- contexto visual compartilhado para branding, usuário e permissões de menu;
- parciais reutilizáveis para sidebar e topbar quando tecnicamente adequado;
- tokens e componentes concentrados no CSS;
- JavaScript pequeno e independente para tema e navegação mobile;
- `data-ui-theme="dark|light"` no elemento `html`;
- chave genérica, como `template-ui-theme`, no `localStorage`;
- branding genérico configurável, sem ativos ou nomes do SOL;
- menu baseado nas rotas reais do template.

## Plano de execução

### 1. Baseline e governança

1. Executar `make css-build` e `make validate` antes das alterações.
2. Confirmar o comportamento atual de Home, login, senha e telas
   administrativas.
3. Após autorização explícita para iniciar a próxima sprint, registrar a Sprint
   13 em `docs/dev_changelog.MD`, sem alterar o significado dos marcadores
   históricos.
4. Preservar o histórico de `changelog.MD`.

### 2. Infraestrutura visual

1. Criar configuração de UI e branding com valores genéricos.
2. Disponibilizar o mesmo contexto visual aos dois renderers.
3. Implementar tokens semânticos de cor, tipografia, espaçamento, raios,
   sombras, transições e estados.
4. Definir tema escuro como padrão e tema claro como variante.
5. Criar classes semânticas reutilizáveis para:
   - shell e conteúdo;
   - cards e métricas;
   - botões;
   - campos e filtros;
   - tabelas;
   - badges;
   - flashes;
   - paginação;
   - estados vazios e erros.
6. Preservar utilitários Tailwind quando não houver repetição relevante.

### 3. Shell autenticado

1. Implementar sidebar desktop de 300 px.
2. Implementar topbar sticky com tema, identidade, usuário e logout POST.
3. Destacar o item correspondente à rota atual.
4. Criar grupos expansíveis com `aria-expanded`.
5. Converter a sidebar em drawer no breakpoint mobile.
6. Adicionar backdrop e fechamento por botão, backdrop, tecla Escape e
   navegação.
7. Devolver o foco ao controle que abriu o drawer.
8. Respeitar `prefers-reduced-motion` nas transições.

### 4. Shell público e autenticação

1. Reestruturar o layout público com os mesmos tokens visuais.
2. Criar card de login centralizado sem utilizar os PNGs do SOL.
3. Disponibilizar áreas configuráveis de cabeçalho e rodapé de branding.
4. Reestilizar a alteração de senha dentro do shell autenticado.
5. Aplicar o padrão às páginas 403, 404 e 500.

### 5. Home demonstrativa

Manter o diagnóstico do banco e a demonstração HTMX, usando a Home como vitrine
dos componentes do template:

- título e introdução;
- cards de métricas;
- painel de filtros;
- formulário demonstrativo;
- tabela responsiva;
- badges de status;
- flashes;
- estado vazio;
- estados de carregamento e feedback HTMX quando aplicável.

Nenhuma consulta ou regra de negócio deve ser adicionada à view.

### 6. Telas administrativas

Reestilizar, preservando contratos, rotas, nomes de campos e comportamento
HTMX:

- segurança;
- usuários e reset de senha;
- grupos e associações;
- permissões;
- tipos de aplicação;
- aplicações;
- matriz de grants;
- manifesto;
- flash, erro de campo, confirmação e paginação.

### 7. Testes e acessibilidade

1. Atualizar os testes de infraestrutura visual existentes.
2. Adicionar testes para configuração de branding e contexto dos layouts.
3. Validar landmarks, logout POST, CSRF, labels, escape e `aria-expanded`.
4. Validar teclado, foco, Escape, drawer e grupos expansíveis.
5. Verificar as larguras de 320, 390, 860 e 1440 px.
6. Verificar os temas escuro e claro.
7. Confirmar overflow horizontal seguro para todas as tabelas.
8. Executar `make css-build`.
9. Executar `make validate`.
10. Criar `docs/tests/blackbox/sprint-13.md`.
11. Atualizar `changelog.MD` ao final da sprint, registrando implementações,
    testes, limitações e pendências.

## Inventário preliminar de arquivos

O inventário deverá ser confirmado novamente imediatamente antes da
implementação.

### Arquivos centrais a alterar

- `resources/css/input.css`;
- `app/View/layouts/app.php`;
- `app/View/layouts/public.php`;
- `resources/views/layouts/app.php`;
- `app/View/home/index.php`;
- `app/View/auth/login.php`;
- `app/View/auth/password.php`;
- views em `app/View/errors/`;
- views em `resources/views/admin/`;
- componentes em `resources/views/components/`;
- `app/Support/View.php`;
- `app/Support/ViewRenderer.php`;
- `app/Config/AppFactory.php`;
- `tests/Unit/UiInfrastructureTest.php`;
- `tests/Integration/HomePageTest.php`;
- `changelog.MD`, ao final da sprint;
- `docs/dev_changelog.MD`, no início autorizado da sprint.

### Arquivos novos previstos

- configuração ou classe de branding e contexto da UI;
- parciais compartilhados de sidebar e topbar;
- `public/assets/js/ui.js`;
- ativo genérico opcional de branding;
- testes específicos da configuração visual;
- `docs/tests/blackbox/sprint-13.md`.

## Fora de escopo

- Banco de dados, migrations e seeds.
- Repositories e consultas SQL.
- Regras de autenticação e autorização.
- Contratos de serviços e regras de negócio.
- Alteração de `.env` ou exposição de credenciais.
- Remoção de volumes Docker.
- Inclusão de frameworks JavaScript.
- Cópia de módulos, textos, rotas, entidades ou ativos específicos do SOL.

## Critérios de conclusão

A implementação será considerada tecnicamente concluída quando:

- o shell autenticado possuir sidebar, topbar e conteúdo centralizado;
- a navegação desktop e mobile estiver acessível e funcional;
- temas escuro e claro estiverem legíveis e persistentes;
- login e layout público compartilharem os tokens do sistema;
- a Home demonstrar os componentes previstos;
- formulários, tabelas, badges, flashes e estados vazios estiverem
  padronizados;
- a interface permanecer utilizável a partir de 320 px;
- todas as tabelas possuírem overflow horizontal seguro;
- `make css-build` e `make validate` passarem;
- o roteiro caixa-preta estiver disponível para homologação posterior;
- nenhum domínio ou ativo obrigatório do SOL tiver sido incorporado.

A homologação funcional dos stakeholders continuará pendente até que o roteiro
caixa-preta seja efetivamente executado e aprovado.
