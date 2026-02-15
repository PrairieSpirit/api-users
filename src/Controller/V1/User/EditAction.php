<?php

declare(strict_types=1);

namespace App\Controller\V1\User;

use App\Controller\AbstractApiController;
use App\DTO\UpdateUserDTO;
use App\Http\ApiRoutes;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: ApiRoutes::USERS_V1,
    name: self::class,
    methods: [Request::METHOD_PUT],
)]
final class EditAction extends AbstractApiController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        $dto = UpdateUserDTO::fromArray($data);

        $user = $this->userService->update($dto);

        return new JsonResponse([
            'id' => $user->getId(),
        ]);
    }
}
