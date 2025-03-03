<?php

declare(strict_types=1);

namespace App\ValueObject;

abstract readonly class AbstractSubjectValueObject
{
    public function __construct(
        private ?string $description = null,
    ) {

    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function validate(): void
    {
        if (empty($this->description)) {
            throw new \InvalidArgumentException('Descrição não pode ser vazia.');
        }

        if (strlen($this->description) > 20) {
            throw new \InvalidArgumentException('Descrição não pode ultrapassar 20 caracteres.');
        }
    }
}
