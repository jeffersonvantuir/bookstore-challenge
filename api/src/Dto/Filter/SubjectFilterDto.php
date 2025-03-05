<?php

declare(strict_types=1);

namespace App\Dto\Filter;

/**
 * @codeCoverageIgnore
 */
readonly class SubjectFilterDto
{
    public function __construct(
        private ?string $description = null,
    ) {
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
