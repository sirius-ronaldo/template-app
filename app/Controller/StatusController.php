<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Response;

final class StatusController
{
    public function show(): Response
    {
        $timestamp = date('d/m/Y H:i:s');
        $body = '<div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-900">'
            . sprintf('Fragmento HTMX atualizado em %s.', $timestamp)
            . '</div>';

        return Response::html($body);
    }
}
