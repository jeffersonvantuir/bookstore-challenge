<?php

declare(strict_types=1);

namespace App\ValueObject;

abstract readonly class AbstractAuthorValueObject
{
    public function __construct(
        private ?string $name = null,
    ) {

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function validate(): void
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('Nome não pode ser vazio.');
        }

        if (strlen($this->name) > 40) {
            throw new \InvalidArgumentException('Nome não pode ultrapassar 40 caracteres.');
        }
    }
}
