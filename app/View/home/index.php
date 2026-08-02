<?php

declare(strict_types=1);

/** @var array{database:string,user:string,server_version:string,now:string} $diagnostics */
/** @var App\Support\View $this */
ob_start();
?>
<section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
    <article class="rounded-3xl bg-white p-8 shadow-sm">
        <h2 class="text-2xl font-semibold">Ambiente pronto para evoluir</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            Este template preserva a base tecnica do projeto `sol` sem carregar modulos de negocio.
            Ele entrega Docker, Apache, PDO, roteamento, HTMX, Tailwind CSS, testes e automacao.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">PHP 8.2</span>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-900">PostgreSQL</span>
            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-900">HTMX</span>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-900">Tailwind CSS</span>
        </div>
    </article>

    <article class="rounded-3xl bg-white p-8 shadow-sm">
        <h2 class="text-lg font-semibold">Diagnostico do banco</h2>
        <dl class="mt-4 space-y-3 text-sm">
            <div>
                <dt class="font-medium text-slate-500">Database</dt>
                <dd><?= $this->escape($diagnostics['database']) ?></dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">Usuario</dt>
                <dd><?= $this->escape($diagnostics['user']) ?></dd>
            </div>
            <div>
                <dt class="font-medium text-slate-500">Horario no banco</dt>
                <dd><?= $this->escape($diagnostics['now']) ?></dd>
            </div>
        </dl>
    </article>
</section>

<section class="mt-6 grid gap-6 md:grid-cols-2">
    <article class="rounded-3xl bg-white p-8 shadow-sm">
        <h2 class="text-lg font-semibold">Fluxo em camadas</h2>
        <ul class="mt-4 space-y-2 text-sm text-slate-600">
            <li>Controller recebe a requisicao e devolve a resposta.</li>
            <li>Service concentra a regra de negocio.</li>
            <li>Repository concentra o SQL e o acesso via PDO.</li>
            <li>View renderiza HTML escapando a saida.</li>
        </ul>
    </article>

    <article class="rounded-3xl bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold">Demo HTMX</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Atualiza apenas um fragmento da pagina sem recarregar tudo.
                </p>
            </div>
            <button
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white"
                hx-get="/status"
                hx-target="#status-panel"
                hx-swap="innerHTML"
                type="button"
            >
                Atualizar fragmento
            </button>
        </div>
        <div
            id="status-panel"
            class="mt-4 rounded-2xl border border-dashed border-slate-300 p-4 text-sm text-slate-500"
        >
            Aguardando acao HTMX.
        </div>
    </article>
</section>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
