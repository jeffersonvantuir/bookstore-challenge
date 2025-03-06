<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ViewReportAuthorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 */
#[ORM\Entity(readOnly: true, repositoryClass: ViewReportAuthorRepository::class)]
#[ORM\Table(name: 'view_report_author')]
class ViewReportAuthor
{
    #[ORM\Id]
    #[ORM\Column(name: 'id')]
    private int $id;

    #[ORM\Column(name: 'author_id')]
    private int $authorId;

    #[ORM\Column(name: 'author_name')]
    private string $authorName;

    #[ORM\Column(name: 'book_id')]
    private ?int $bookId = null;

    #[ORM\Column(name: 'book_title')]
    private ?string $bookTitle = null;

    #[ORM\Column(name: 'publisher')]
    private ?string $publisher = null;

    #[ORM\Column(name: 'edition')]
    private ?int $edition = null;

    #[ORM\Column(name: 'publication_year')]
    private ?string $publicationYear = null;

    #[ORM\Column(name: 'amount')]
    private ?float $amount = null;

    #[ORM\Column(name: 'subjects')]
    private ?string $subjects = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getBookId(): ?int
    {
        return $this->bookId;
    }

    public function getBookTitle(): ?string
    {
        return $this->bookTitle;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getEdition(): ?int
    {
        return $this->edition;
    }

    public function getPublicationYear(): ?string
    {
        return $this->publicationYear;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getSubjects(): ?string
    {
        return $this->subjects;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function setAuthorName(string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function setBookId(?int $bookId): self
    {
        $this->bookId = $bookId;

        return $this;
    }

    public function setBookTitle(?string $bookTitle): self
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function setEdition(?int $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function setPublicationYear(?string $publicationYear): self
    {
        $this->publicationYear = $publicationYear;

        return $this;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setSubjects(?string $subjects): self
    {
        $this->subjects = $subjects;

        return $this;
    }
}
