<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractApiController extends AbstractController
{
    protected function getRequestData(Request $request): array
    {
        return $request->isMethod(Request::METHOD_GET)
            ? $request->query->all()
            : $this->getJsonData($request);
    }
    protected function getJsonData(Request $request): array
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON: ' . $e->getMessage());
        }

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON payload');
        }

        return $data;
    }

    protected function requireFields(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null) {
                throw new BadRequestHttpException(
                    sprintf('Field "%s" is required', $field)
                );
            }
        }
    }
}
