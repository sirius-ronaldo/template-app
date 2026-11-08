<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Service\GrantManagementService;
use App\Service\GroupManagementService;
use App\Support\FlashBag;
use App\Support\Request;
use App\Support\Response;
use App\Support\ViewRenderer;

final class GrantController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly GrantManagementService $grants,
        private readonly GroupManagementService $groups,
        private readonly FlashBag $flash
    ) {
    }

    public function form(Request $request, int $groupId): Response
    {
        $group = $this->groups->find($groupId);
        if ($group === null) {
            throw new NotFoundException('Grupo nao encontrado.');
        }
        $filter = trim((string) $request->input('filter', ''));
        return Response::html($this->views->render('admin/grants/form', [
            'group' => $group,
            'rows' => $this->grants->matrix($groupId, $filter),
            'filter' => $filter,
            'errors' => [],
        ], $request->isHtmx() ? null : 'layouts/app'));
    }

    public function sync(Request $request, int $groupId): Response
    {
        $group = $this->groups->find($groupId);
        if ($group === null) {
            throw new NotFoundException('Grupo nao encontrado.');
        }
        $raw = $request->input('permissions', []);
        $permissions = [];
        if (is_array($raw)) {
            foreach ($raw as $applicationId => $permissionIds) {
                $permissions[(int) $applicationId] = is_array($permissionIds)
                    ? array_map('intval', $permissionIds)
                    : [];
            }
        }
        foreach ($this->grants->matrix($groupId, '') as $row) {
            $applicationId = (int) $row['application_id'];
            $permissions[$applicationId] ??= [];
        }
        $errors = $this->grants->sync($groupId, $permissions);
        if ($errors !== []) {
            return Response::html($this->views->render('admin/grants/form', [
                'group' => $group,
                'rows' => $this->grants->matrix($groupId, ''),
                'filter' => '',
                'errors' => $errors,
            ], $request->isHtmx() ? null : 'layouts/app'), 422);
        }
        $this->flash->add('success', 'Permissoes do grupo atualizadas.');
        return Response::redirect('/configuracoes/grupos');
    }
}
