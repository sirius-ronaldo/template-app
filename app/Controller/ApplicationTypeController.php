<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ApplicationTypeInput;
use App\Exception\NotFoundException;
use App\Service\ApplicationTypeManagementService;
use App\Service\PermissionManagementService;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class ApplicationTypeController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly ApplicationTypeManagementService $types,
        private readonly PermissionManagementService $permissions,
        private readonly FlashBag $flash
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $result = $this->types->list($filter, Pagination::fromQuery($request->queryParams(), 0));
        $data = [
            'items' => $result['items'],
            'filter' => $filter,
            'pagination' => $result['pagination'],
            'query' => $request->queryParams(),
        ];
        $template = $request->isHtmx() ? 'admin/types/_table' : 'admin/types/index';
        return Response::html($this->views->render($template, $data, $request->isHtmx() ? null : 'layouts/app'));
    }

    public function form(Request $request, ?int $id = null): Response
    {
        $item = $id === null
            ? ['code' => '', 'name' => '', 'description' => '', 'active' => true]
            : $this->types->find($id);
        if ($item === null) {
            throw new NotFoundException('Tipo de aplicacao nao encontrado.');
        }
        return Response::html($this->views->render(
            'admin/types/form',
            ['item' => $item, 'id' => $id, 'errors' => []],
            $request->isHtmx() ? null : 'layouts/app'
        ));
    }

    public function save(Request $request, ?int $id = null): Response
    {
        $input = new ApplicationTypeInput(
            (string) $request->input('code', ''),
            (string) $request->input('name', ''),
            (string) $request->input('description', ''),
            filter_var($request->input('active', true), FILTER_VALIDATE_BOOL)
        );
        $errors = $id === null ? $this->types->create($input) : $this->types->update($id, $input);
        if ($errors !== []) {
            $item = [
                'code' => $input->code,
                'name' => $input->name,
                'description' => $input->description,
                'active' => $input->active,
            ];
            return Response::html($this->views->render(
                'admin/types/form',
                ['item' => $item, 'id' => $id, 'errors' => $errors],
                $request->isHtmx() ? null : 'layouts/app'
            ), 422);
        }
        $this->flash->add('success', $id === null ? 'Tipo criado.' : 'Tipo atualizado.');
        return Response::redirect('/configuracoes/tipos');
    }

    public function relationForm(Request $request, int $id): Response
    {
        $type = $this->types->find($id);
        if ($type === null) {
            throw new NotFoundException('Tipo de aplicacao nao encontrado.');
        }
        $selected = array_column($this->permissions->activeForType($id), 'id');
        return Response::html($this->views->render('admin/types/permissions', [
            'type' => $type,
            'permissions' => $this->permissions->list('', new Pagination(1, 100, 0))['items'],
            'selected' => $selected,
            'errors' => [],
            'id' => $id,
        ], $request->isHtmx() ? null : 'layouts/app'));
    }

    public function syncRelation(Request $request, int $id): Response
    {
        $selected = $request->input('permission_ids', []);
        $selected = is_array($selected) ? array_map('intval', $selected) : [];
        $errors = $this->permissions->syncTypePermissions($id, $selected);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel atualizar.', 422);
        }
        $this->flash->add('success', 'Permissoes do tipo atualizadas.');
        return Response::redirect('/configuracoes/tipos');
    }

    public function toggle(Request $request, int $id): Response
    {
        $this->types->setActive($id, filter_var($request->input('active', false), FILTER_VALIDATE_BOOL));
        return Response::redirect('/configuracoes/tipos');
    }

    public function delete(Request $request, int $id): Response
    {
        $errors = $this->types->delete($id);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel excluir.', 422);
        }
        return Response::redirect('/configuracoes/tipos');
    }
}
