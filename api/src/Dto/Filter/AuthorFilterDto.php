<?php

declare(strict_types=1);

namespace App\Dto\Filter;

readonly class AuthorFilterDto
{
    public function __construct(
        private ?string $name = null,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
