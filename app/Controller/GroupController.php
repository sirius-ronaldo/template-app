<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\GroupInput;
use App\Exception\NotFoundException;
use App\Service\GroupManagementService;
use App\Service\UserManagementService;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class GroupController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly GroupManagementService $groups,
        private readonly UserManagementService $users,
        private readonly FlashBag $flash
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $result = $this->groups->list($filter, Pagination::fromQuery($request->queryParams(), 0));
        $data = [
            'groups' => $result['items'],
            'filter' => $filter,
            'pagination' => $result['pagination'],
            'query' => $request->queryParams(),
        ];
        $template = $request->isHtmx() ? 'admin/groups/_table' : 'admin/groups/index';
        return Response::html(
            $this->views->render($template, $data, $request->isHtmx() ? null : 'layouts/app')
        );
    }

    public function createForm(Request $request): Response
    {
        return $this->formResponse($request, ['mode' => 'create', 'group' => $this->emptyGroup(), 'errors' => []]);
    }

    public function create(Request $request): Response
    {
        $input = $this->input($request);
        $errors = $this->groups->create($input);
        if ($errors !== []) {
            return $this->formResponse(
                $request,
                ['mode' => 'create', 'group' => $this->groupData($input), 'errors' => $errors],
                422
            );
        }
        $this->flash->add('success', 'Grupo criado com sucesso.');
        return Response::redirect('/configuracoes/grupos');
    }

    public function editForm(Request $request, int $groupId): Response
    {
        $group = $this->groups->find($groupId);
        if ($group === null) {
            throw new NotFoundException('Grupo nao encontrado.');
        }
        return $this->formResponse(
            $request,
            ['mode' => 'edit', 'group' => $group, 'errors' => [], 'groupId' => $groupId]
        );
    }

    public function update(Request $request, int $groupId): Response
    {
        $input = $this->input($request);
        $errors = $this->groups->update($groupId, $input);
        if ($errors !== []) {
            return $this->formResponse(
                $request,
                [
                    'mode' => 'edit',
                    'group' => $this->groupData($input),
                    'errors' => $errors,
                    'groupId' => $groupId,
                ],
                422
            );
        }
        $this->flash->add('success', 'Grupo atualizado com sucesso.');
        return Response::redirect('/configuracoes/grupos');
    }

    public function toggle(Request $request, int $groupId): Response
    {
        $active = filter_var($request->input('active', false), FILTER_VALIDATE_BOOL);
        $errors = $active ? $this->groups->activate($groupId) : $this->groups->deactivate($groupId);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Dados invalidos.', 422);
        }
        $this->flash->add('success', $active ? 'Grupo ativado.' : 'Grupo inativado.');
        return Response::redirect('/configuracoes/grupos');
    }

    public function delete(Request $request, int $groupId): Response
    {
        $errors = $this->groups->delete($groupId);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel excluir o grupo.', 422);
        }
        $this->flash->add('success', 'Grupo excluido.');
        return Response::redirect('/configuracoes/grupos');
    }

    public function userGroupsForm(Request $request, int $userId): Response
    {
        if ($this->users->find($userId) === null) {
            throw new NotFoundException('Usuario nao encontrado.');
        }
        return Response::html($this->views->render('admin/groups/user-groups', [
            'user' => $this->users->find($userId),
            'groups' => $this->groups->list('', new Pagination(1, 100, 0))['items'],
            'selected' => array_column($this->groups->userGroups($userId), 'id'),
            'errors' => [],
            'userId' => $userId,
        ], $request->isHtmx() ? null : 'layouts/app'));
    }

    public function syncUserGroups(Request $request, int $userId): Response
    {
        $selected = $request->input('group_ids', []);
        $selected = is_array($selected) ? array_map('intval', $selected) : [];
        $errors = $this->groups->syncUserGroups($userId, $selected);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel atualizar os grupos.', 422);
        }
        $this->flash->add('success', 'Grupos do usuario atualizados.');
        return Response::redirect('/configuracoes/usuarios');
    }

    /** @param array<string,mixed> $data */
    private function formResponse(Request $request, array $data, int $status = 200): Response
    {
        return Response::html(
            $this->views->render('admin/groups/form', $data, $request->isHtmx() ? null : 'layouts/app'),
            $status
        );
    }

    private function input(Request $request): GroupInput
    {
        return new GroupInput(
            (string) $request->input('code', ''),
            (string) $request->input('name', ''),
            (string) $request->input('description', ''),
            filter_var($request->input('active', true), FILTER_VALIDATE_BOOL)
        );
    }

    /** @return array<string,mixed> */
    private function groupData(GroupInput $input): array
    {
        return [
            'code' => $input->code,
            'name' => $input->name,
            'description' => $input->description,
            'active' => $input->active,
        ];
    }

    /** @return array<string,mixed> */
    private function emptyGroup(): array
    {
        return ['code' => '', 'name' => '', 'description' => '', 'active' => true];
    }
}
