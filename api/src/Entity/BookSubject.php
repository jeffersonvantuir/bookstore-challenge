<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookSubjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookSubjectRepository::class)]
#[ORM\Table(name: 'Livro_Assunto')]
#[ORM\Index(name: 'Livro_Assunto_FKIndex1', columns: ['Livro_Codl'])]
#[ORM\Index(name: 'Livro_Assunto_FKIndex2', columns: ['Assunto_CodAs'])]
class BookSubject
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl', nullable: false)]
    private Book $book;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'CodAs', nullable: false)]
    private Subject $subject;

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
