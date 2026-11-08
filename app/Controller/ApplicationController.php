<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ApplicationInput;
use App\Exception\NotFoundException;
use App\Service\ApplicationManagementService;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class ApplicationController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly ApplicationManagementService $applications,
        private readonly FlashBag $flash
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $result = $this->applications->list($filter, Pagination::fromQuery($request->queryParams(), 0));
        $data = [
            'items' => $result['items'],
            'filter' => $filter,
            'pagination' => $result['pagination'],
            'query' => $request->queryParams(),
        ];
        $template = $request->isHtmx() ? 'admin/applications/_table' : 'admin/applications/index';
        return Response::html($this->views->render($template, $data, $request->isHtmx() ? null : 'layouts/app'));
    }

    public function form(Request $request, ?int $id = null): Response
    {
        $item = $id === null
            ? ['code' => '', 'name' => '', 'description' => '', 'type_id' => 0, 'active' => true]
            : $this->applications->find($id);
        if ($item === null) {
            throw new NotFoundException('Aplicacao nao encontrada.');
        }
        return Response::html($this->views->render('admin/applications/form', [
            'item' => $item,
            'id' => $id,
            'types' => $this->applications->availableTypes($id),
            'errors' => [],
        ], $request->isHtmx() ? null : 'layouts/app'));
    }

    public function save(Request $request, ?int $id = null): Response
    {
        $input = new ApplicationInput(
            (string) $request->input('code', ''),
            (string) $request->input('name', ''),
            (string) $request->input('description', ''),
            (int) $request->input('type_id', 0),
            filter_var($request->input('active', true), FILTER_VALIDATE_BOOL)
        );
        $errors = $id === null ? $this->applications->create($input) : $this->applications->update($id, $input);
        if ($errors !== []) {
            return Response::html($this->views->render('admin/applications/form', [
                'item' => [
                    'code' => $input->code,
                    'name' => $input->name,
                    'description' => $input->description,
                    'type_id' => $input->typeId,
                    'active' => $input->active,
                ],
                'id' => $id,
                'types' => $this->applications->availableTypes($id),
                'errors' => $errors,
            ], $request->isHtmx() ? null : 'layouts/app'), 422);
        }
        $this->flash->add('success', $id === null ? 'Aplicacao criada.' : 'Aplicacao atualizada.');
        return Response::redirect('/configuracoes/aplicacoes');
    }

    public function toggle(Request $request, int $id): Response
    {
        $active = filter_var($request->input('active', false), FILTER_VALIDATE_BOOL);
        $errors = $this->applications->setActive($id, $active);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel atualizar.', 422);
        }
        return Response::redirect('/configuracoes/aplicacoes');
    }

    public function delete(Request $request, int $id): Response
    {
        $errors = $this->applications->delete($id);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel excluir.', 422);
        }
        return Response::redirect('/configuracoes/aplicacoes');
    }
}
