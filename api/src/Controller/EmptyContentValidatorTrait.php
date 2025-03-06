<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait EmptyContentValidatorTrait
{
    private function validateRequest(Request $request): void
    {
        if ('' === $request->getContent()) {
            throw new BadRequestHttpException('Nenhum dado foi enviado na requisição.');
        }
    }
}
