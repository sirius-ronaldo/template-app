<?php

declare(strict_types=1);

/** @var array{database:string,user:string,server_version:string,now:string} $diagnostics */
/** @var App\Support\View $this */
ob_start();
?>
<header class="ui-page-header">
    <div>
        <p class="ui-eyebrow">Visão geral</p>
        <h1 class="ui-page-title">Ambiente pronto para evoluir</h1>
        <p class="ui-page-description">
            Base técnica em PHP 8.2 com camadas simples, PostgreSQL, HTMX e componentes reutilizáveis.
        </p>
    </div>
    <span class="ui-badge ui-badge--success">● Operacional</span>
</header>

<section class="ui-grid ui-grid--metrics" aria-label="Tecnologias disponíveis">
    <article class="ui-metric"><span>Runtime</span><strong>8.2</strong><small>PHP</small></article>
    <article class="ui-metric"><span>Persistência</span><strong>PDO</strong><small>PostgreSQL</small></article>
    <article class="ui-metric"><span>Interação</span><strong>HTMX</strong><small>Sem framework JS</small></article>
    <article class="ui-metric"><span>Interface</span><strong>CSS</strong><small>Tailwind v4</small></article>
    <article class="ui-metric"><span>Qualidade</span><strong>56</strong><small>Testes no baseline</small></article>
</section>

<div class="ui-stack ui-section-gap">
    <section class="ui-card ui-card--padded" aria-labelledby="filters-title">
        <div class="ui-card__header">
            <div>
                <h2 class="ui-card__title" id="filters-title">Filtros demonstrativos</h2>
                <p class="ui-card__description">Exemplo responsivo de busca, seleção e período.</p>
            </div>
            <span class="ui-badge">Demonstração</span>
        </div>
        <form class="ui-filter-grid" action="/Home" method="get">
            <div class="ui-field">
                <label class="ui-label" for="demo-search">Busca</label>
                <input class="ui-input" id="demo-search" name="search" placeholder="Nome, código ou descrição">
            </div>
            <div class="ui-field">
                <label class="ui-label" for="demo-status">Status</label>
                <select class="ui-select" id="demo-status" name="status">
                    <option>Todos</option><option>Ativos</option><option>Inativos</option>
                </select>
            </div>
            <div class="ui-field">
                <label class="ui-label" for="demo-start">Data inicial</label>
                <input class="ui-input" id="demo-start" name="start" type="date">
            </div>
            <div class="ui-field">
                <label class="ui-label" for="demo-end">Data final</label>
                <input class="ui-input" id="demo-end" name="end" type="date">
            </div>
            <div class="ui-actions ui-field--full">
                <button class="ui-button ui-button--primary" type="submit">Aplicar filtros</button>
                <button class="ui-button ui-button--secondary" type="reset">Limpar</button>
            </div>
        </form>
    </section>

    <div class="ui-grid ui-grid--2">
        <section class="ui-card ui-card--padded" aria-labelledby="database-title">
            <div class="ui-card__header">
                <div>
                    <h2 class="ui-card__title" id="database-title">Diagnóstico do banco</h2>
                    <p class="ui-card__description">Conexão ativa usada pelo ambiente.</p>
                </div>
                <span class="ui-badge ui-badge--success">Conectado</span>
            </div>
            <dl class="ui-stack">
                <div><dt class="ui-help">Database</dt><dd><?= $this->escape($diagnostics['database']) ?></dd></div>
                <div><dt class="ui-help">Usuário</dt><dd><?= $this->escape($diagnostics['user']) ?></dd></div>
                <div>
                    <dt class="ui-help">Versão do servidor</dt>
                    <dd><?= $this->escape($diagnostics['server_version']) ?></dd>
                </div>
                <div><dt class="ui-help">Horário no banco</dt><dd><?= $this->escape($diagnostics['now']) ?></dd></div>
            </dl>
        </section>
        <section class="ui-card ui-card--padded" aria-labelledby="htmx-title">
            <div class="ui-card__header">
                <div>
                    <h2 class="ui-card__title" id="htmx-title">Atualização HTMX</h2>
                    <p class="ui-card__description">Atualiza apenas o fragmento abaixo.</p>
                </div>
            </div>
            <button
                class="ui-button ui-button--primary"
                hx-get="/status"
                hx-target="#status-panel"
                hx-swap="innerHTML"
                type="button"
            >Atualizar fragmento</button>
            <div class="ui-empty" id="status-panel" aria-live="polite">Aguardando uma ação HTMX.</div>
        </section>
    </div>

    <section class="ui-card ui-card--padded" aria-labelledby="table-title">
        <div class="ui-card__header">
            <div>
                <h2 class="ui-card__title" id="table-title">Catálogo de componentes</h2>
                <p class="ui-card__description">Tabela, badges e ações com rolagem segura.</p>
            </div>
            <a class="ui-button ui-button--secondary" href="/configuracoes/seguranca">Ver administração</a>
        </div>
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead><tr><th>Componente</th><th>Uso</th><th>Status</th><th>Ação</th></tr></thead>
                <tbody>
                    <tr>
                        <td>Shell autenticado</td><td>Sidebar, topbar e conteúdo</td>
                        <td><span class="ui-badge ui-badge--success">Disponível</span></td>
                        <td><a class="ui-link" href="/Home">Visualizar</a></td>
                    </tr>
                    <tr>
                        <td>Formulários</td><td>Campos, selects e validação</td>
                        <td><span class="ui-badge ui-badge--success">Disponível</span></td>
                        <td><a class="ui-link" href="/minha-senha">Visualizar</a></td>
                    </tr>
                    <tr>
                        <td>Estado vazio</td><td>Listas sem resultados</td>
                        <td><span class="ui-badge ui-badge--warning">Exemplo</span></td>
                        <td><button class="ui-link-button" type="button">Detalhes</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
