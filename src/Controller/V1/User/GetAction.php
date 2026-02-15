<?php

declare(strict_types=1);

namespace App\Controller\V1\User;

use App\Controller\AbstractApiController;
use App\HTTp\ApiRoutes;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: ApiRoutes::USERS_V1,
    name: self::class,
    methods: [Request::METHOD_GET],
)]
final class GetAction extends AbstractApiController
{
    private const REQUIRED_FIELDS = ['id'];

    public function __construct(
        private readonly UserService $userService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        $this->requireFields($data, self::REQUIRED_FIELDS);

        $user = $this->userService->getUser((int) $data['id']);

        return new JsonResponse([
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
            'pass'  => $user->getPass(),
        ]);
    }
}
