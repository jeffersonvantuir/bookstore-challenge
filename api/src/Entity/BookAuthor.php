<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookAuthorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 */
#[ORM\Entity(repositoryClass: BookAuthorRepository::class)]
#[ORM\Table(name: 'Livro_Autor')]
#[ORM\Index(name: 'Livro_Autor_FKIndex1', columns: ['Livro_Codl'])]
#[ORM\Index(name: 'Livro_Autor_FKIndex2', columns: ['Autor_CodAu'])]
class BookAuthor
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'Livro_Autor')]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl', nullable: false)]
    private Book $book;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu', nullable: false)]
    private Author $author;

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
