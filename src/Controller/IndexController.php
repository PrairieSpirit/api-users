<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/', name: 'index', methods: [Request::METHOD_GET])]
class IndexController
{
    public function __invoke(): Response
    {
        return new JsonResponse(['status' => 'ok']);
    }
}

