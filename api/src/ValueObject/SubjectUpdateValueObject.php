<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class SubjectUpdateValueObject extends AbstractSubjectValueObject
{
    public function __construct(
        private int $id,
        private ?string $description = null
    ) {
        parent::__construct($this->description);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
