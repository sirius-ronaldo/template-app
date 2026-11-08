<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\PermissionInput;
use App\Exception\NotFoundException;
use App\Service\PermissionManagementService;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class PermissionController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly PermissionManagementService $permissions,
        private readonly FlashBag $flash
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $result = $this->permissions->list($filter, Pagination::fromQuery($request->queryParams(), 0));
        $data = [
            'items' => $result['items'],
            'filter' => $filter,
            'pagination' => $result['pagination'],
            'query' => $request->queryParams(),
        ];
        $template = $request->isHtmx() ? 'admin/permissions/_table' : 'admin/permissions/index';
        return Response::html($this->views->render($template, $data, $request->isHtmx() ? null : 'layouts/app'));
    }

    public function form(Request $request, ?int $id = null): Response
    {
        $item = $id === null ? $this->empty() : $this->permissions->find($id);
        if ($item === null) {
            throw new NotFoundException('Permissao nao encontrada.');
        }
        return Response::html($this->views->render(
            'admin/permissions/form',
            ['item' => $item, 'id' => $id, 'errors' => []],
            $request->isHtmx() ? null : 'layouts/app'
        ));
    }

    public function save(Request $request, ?int $id = null): Response
    {
        $input = new PermissionInput(
            (string) $request->input('code', ''),
            (string) $request->input('name', ''),
            (string) $request->input('description', ''),
            filter_var($request->input('active', true), FILTER_VALIDATE_BOOL)
        );
        $errors = $id === null ? $this->permissions->create($input) : $this->permissions->update($id, $input);
        if ($errors !== []) {
            $item = [
                'code' => $input->code,
                'name' => $input->name,
                'description' => $input->description,
                'active' => $input->active,
            ];
            return Response::html($this->views->render(
                'admin/permissions/form',
                ['item' => $item, 'id' => $id, 'errors' => $errors],
                $request->isHtmx() ? null : 'layouts/app'
            ), 422);
        }
        $this->flash->add('success', $id === null ? 'Permissao criada.' : 'Permissao atualizada.');
        return Response::redirect('/configuracoes/permissoes');
    }

    public function toggle(Request $request, int $id): Response
    {
        $this->permissions->setActive($id, filter_var($request->input('active', false), FILTER_VALIDATE_BOOL));
        return Response::redirect('/configuracoes/permissoes');
    }

    public function delete(Request $request, int $id): Response
    {
        $errors = $this->permissions->delete($id);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel excluir.', 422);
        }
        return Response::redirect('/configuracoes/permissoes');
    }

    /** @return array<string,mixed> */
    private function empty(): array
    {
        return ['code' => '', 'name' => '', 'description' => '', 'active' => true];
    }
}
