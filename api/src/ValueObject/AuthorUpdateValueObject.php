<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class AuthorUpdateValueObject extends AbstractAuthorValueObject
{
    public function __construct(
        private int $id,
        private ?string $name = null
    ) {
        parent::__construct($this->name);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
