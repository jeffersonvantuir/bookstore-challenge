<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class BookUpdateValueObject extends AbstractBookValueObject
{
    /**
     * @param int[] $authorsIds
     * @param int[] $subjectsIds
     */
    public function __construct(
        private int $id,
        private ?string $title = null,
        private ?string $publisher = null,
        private ?int $edition = null,
        private ?string $publicationYear = null,
        private ?float $amount = null,
        private array $authorsIds = [],
        private array $subjectsIds = [],
    ) {
        parent::__construct(
            $this->title,
            $this->publisher,
            $this->edition,
            $this->publicationYear,
            $this->amount,
            $this->authorsIds,
            $this->subjectsIds
        );
    }

    public function getId(): int
    {
        return $this->id;
    }
}
