<?php

declare(strict_types=1);

namespace App\Dto\Filter;

readonly class BookFilterDto
{
    public function __construct(
        private ?string $title = null,
        private ?string $publisher = null,
        private ?string $edition = null,
        private ?string $publicationYear = null,
        private ?string $authorName = null,
        private ?string $subjectDescription = null,
    ) {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function getPublicationYear(): ?string
    {
        return $this->publicationYear;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function getSubjectDescription(): ?string
    {
        return $this->subjectDescription;
    }
}
