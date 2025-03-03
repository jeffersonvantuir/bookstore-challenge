<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'Livro')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Codl')]
    private int $id;

    #[ORM\Column(name: 'Titulo', length: 40, options: ['comment' => 'Título do livro'])]
    private string $title;

    #[ORM\Column(name: 'Editora', length: 40, options: ['comment' => 'Editora do livro'])]
    private string $publisher;

    #[ORM\Column(name: 'Edicao', options: ['unsigned' => true, 'comment' => 'Edição do livro'])]
    private int $edition;

    #[ORM\Column(name: 'AnoPublicacao', length: 4, options: ['unsigned' => true, 'comment' => 'Ano de Publicação do livro'])]
    private string $publicationYear;

    #[ORM\Column(name: 'Valor', type: 'decimal', precision: 10, scale: 2, options: ['comment' => 'Valor (R$) do livro'])]
    private float $amount;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getEdition(): int
    {
        return $this->edition;
    }

    public function setEdition(int $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getPublicationYear(): string
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(string $publicationYear): self
    {
        $this->publicationYear = $publicationYear;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
