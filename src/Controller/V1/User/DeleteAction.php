<?php

declare(strict_types=1);

namespace App\Controller\V1\User;

use App\Controller\AbstractApiController;
use App\HTTp\ApiRoutes;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: ApiRoutes::USERS_V1,
    name: self::class,
    methods: [Request::METHOD_DELETE],
)]
final class DeleteAction extends AbstractApiController
{
    private const REQUIRED_FIELDS = ['id'];

    public function __construct(
        private readonly UserService $userService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        $this->requireFields($data, self::REQUIRED_FIELDS);

        $this->userService->delete((int) $data['id']);

        return new JsonResponse(['status' => 'DELETE']);
    }

}


