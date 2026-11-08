<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\UserInput;
use App\Exception\NotFoundException;
use App\Service\UserManagementService;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;
use App\Support\ViewRenderer;

final class UserController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly UserManagementService $users,
        private readonly SessionManager $sessions,
        private readonly FlashBag $flash
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = trim((string) $request->input('filter', ''));
        $pagination = Pagination::fromQuery($request->queryParams(), 0);
        $result = $this->users->list($filter, $pagination);
        $data = [
            'users' => $result['items'],
            'filter' => $filter,
            'pagination' => $result['pagination'],
            'query' => $request->queryParams(),
        ];
        $template = $request->isHtmx() ? 'admin/users/_table' : 'admin/users/index';
        return Response::html($this->views->render($template, $data, $request->isHtmx() ? null : 'layouts/app'));
    }

    public function createForm(Request $request): Response
    {
        return $this->formResponse($request, ['mode' => 'create', 'user' => $this->emptyUser(), 'errors' => []]);
    }

    public function create(Request $request): Response
    {
        $input = $this->input($request);
        $errors = $this->users->create($input);
        if ($errors !== []) {
            return $this->formResponse(
                $request,
                ['mode' => 'create', 'user' => $this->userData($input), 'errors' => $errors],
                422
            );
        }
        $this->flash->add('success', 'Usuario criado com sucesso.');
        return Response::redirect('/configuracoes/usuarios');
    }

    public function editForm(Request $request, int $userId): Response
    {
        $user = $this->users->find($userId);
        if ($user === null) {
            throw new NotFoundException('Usuario nao encontrado.');
        }
        return $this->formResponse($request, ['mode' => 'edit', 'user' => $user, 'errors' => [], 'userId' => $userId]);
    }

    public function update(Request $request, int $userId): Response
    {
        $actorId = $this->actorId();
        $input = $this->input($request);
        $errors = $this->users->update($actorId, $userId, $input);
        if ($errors !== []) {
            return $this->formResponse(
                $request,
                [
                    'mode' => 'edit',
                    'user' => $this->userData($input),
                    'errors' => $errors,
                    'userId' => $userId,
                ],
                422
            );
        }
        $this->flash->add('success', 'Usuario atualizado com sucesso.');
        return Response::redirect('/configuracoes/usuarios');
    }

    public function resetForm(Request $request, int $userId): Response
    {
        if ($this->users->find($userId) === null) {
            throw new NotFoundException('Usuario nao encontrado.');
        }
        return $this->resetResponse($request, $userId, [], 200);
    }

    public function reset(Request $request, int $userId): Response
    {
        $errors = $this->users->resetPassword(
            $userId,
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', '')
        );
        if ($errors !== []) {
            return $this->resetResponse($request, $userId, $errors, 422);
        }
        $this->flash->add('success', 'Senha redefinida com sucesso.');
        return Response::redirect('/configuracoes/usuarios');
    }

    public function toggle(Request $request, int $userId): Response
    {
        $active = filter_var($request->input('active', false), FILTER_VALIDATE_BOOL);
        $errors = $active
            ? $this->users->activate($userId)
            : $this->users->deactivate($this->actorId(), $userId);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Dados invalidos.', 422);
        }
        $this->flash->add('success', $active ? 'Usuario ativado.' : 'Usuario inativado.');
        return Response::redirect('/configuracoes/usuarios');
    }

    public function delete(Request $request, int $userId): Response
    {
        $errors = $this->users->delete($this->actorId(), $userId);
        if ($errors !== []) {
            return Response::html($errors['global'] ?? 'Nao foi possivel excluir o usuario.', 422);
        }
        $this->flash->add('success', 'Usuario excluido.');
        return Response::redirect('/configuracoes/usuarios');
    }

    /** @param array<string,mixed> $data */
    private function formResponse(Request $request, array $data, int $status = 200): Response
    {
        $layout = $request->isHtmx() ? null : 'layouts/app';
        return Response::html($this->views->render('admin/users/form', $data, $layout), $status);
    }

    /** @param array<string,string> $errors */
    private function resetResponse(Request $request, int $userId, array $errors, int $status): Response
    {
        $layout = $request->isHtmx() ? null : 'layouts/app';
        return Response::html($this->views->render('admin/users/reset', [
            'userId' => $userId,
            'errors' => $errors,
        ], $layout), $status);
    }

    private function actorId(): int
    {
        return $this->sessions->credentials()['user_id'];
    }

    private function input(Request $request): UserInput
    {
        return new UserInput(
            trim((string) $request->input('login', '')),
            trim((string) $request->input('name', '')),
            trim((string) $request->input('email', '')),
            filter_var($request->input('active', true), FILTER_VALIDATE_BOOL),
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', '')
        );
    }

    /** @return array<string,mixed> */
    private function userData(UserInput $input): array
    {
        return ['login' => $input->login, 'name' => $input->name, 'email' => $input->email, 'active' => $input->active];
    }

    /** @return array<string,mixed> */
    private function emptyUser(): array
    {
        return ['login' => '', 'name' => '', 'email' => '', 'active' => true];
    }
}
