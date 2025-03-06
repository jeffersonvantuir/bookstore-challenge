<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Filter\BookFilterDto;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Entity\BookSubject;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function findByFilterDto(BookFilterDto $filterDto): array
    {
        $queryBuilder = $this->createQueryBuilder('book');

        $query = $queryBuilder->select('book.id, book.title, book.publisher, book.edition, book.publicationYear, book.amount')
            ->distinct()
            ->leftJoin(BookAuthor::class, 'bookAuthor', Join::WITH, 'bookAuthor.book = book.id')
            ->leftJoin(Author::class, 'author', Join::WITH, 'author.id = bookAuthor.author')
            ->leftJoin(BookSubject::class, 'bookSubject', Join::WITH, 'bookSubject.book = book.id')
            ->leftJoin(Subject::class, 'subject', Join::WITH, 'bookSubject.subject = subject.id');

        if (false === empty($filterDto->getTitle())) {
            $query->andWhere('book.title LIKE :title')
                ->setParameter('title', '%' . $filterDto->getTitle() . '%');
        }

        if (false === empty($filterDto->getPublisher())) {
            $query->andWhere('book.publisher LIKE :publisher')
                ->setParameter('publisher', '%' . $filterDto->getPublisher() . '%');
        }

        if (false === empty($filterDto->getEdition())) {
            $query->andWhere('book.edition = :edition')
                ->setParameter('edition', $filterDto->getEdition());
        }

        if (false === empty($filterDto->getPublicationYear())) {
            $query->andWhere('book.publicationYear = :publicationYear')
                ->setParameter('publicationYear', $filterDto->getPublicationYear());
        }

        if (false === empty($filterDto->getAuthorName())) {
            $query->andWhere('author.name LIKE :authorName')
                ->setParameter('authorName', '%' . $filterDto->getAuthorName() . '%');
        }

        if (false === empty($filterDto->getSubjectDescription())) {
            $query->andWhere('subject.description LIKE :subjectDescription')
                ->setParameter('subjectDescription', '%' . $filterDto->getSubjectDescription() . '%');
        }

        $query = $query->orderBy('book.title', Order::Ascending->value)
            ->getQuery();

        return $query->getArrayResult();
    }
}
