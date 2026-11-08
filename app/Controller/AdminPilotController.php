<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class AdminPilotController
{
    public function __construct(private readonly ViewRenderer $views)
    {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $items = ['Usuários', 'Grupos', 'Permissões', 'Aplicações'];
        if ($filter !== '') {
            $items = array_values(array_filter(
                $items,
                static fn (string $item): bool => str_contains(mb_strtolower($item), mb_strtolower($filter))
            ));
        }
        $pagination = Pagination::fromQuery($request->queryParams(), count($items));
        $items = array_slice($items, $pagination->offset(), $pagination->perPage);
        $template = $request->isHtmx() ? 'admin/security/_table' : 'admin/security/index';
        $body = $this->views->render($template, [
            'items' => $items,
            'filter' => $filter,
            'pagination' => $pagination,
            'query' => $request->queryParams(),
        ], $request->isHtmx() ? null : 'layouts/app');
        return Response::html($body);
    }
}
