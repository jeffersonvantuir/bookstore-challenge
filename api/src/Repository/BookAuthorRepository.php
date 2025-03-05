<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookAuthor>
 */
class BookAuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookAuthor::class);
    }

    /**
     * @param int[]|null $authorIds
     */
    public function removeLinkNotInAuthorsIds(int $bookId, ?array $authorIds = []): void
    {
        $queryBuilder = $this->createQueryBuilder('bookAuthor');

        $queryBuilder->delete()
            ->where('bookAuthor.book = :book')
            ->setParameter('book', $bookId);

        if (false === empty($authorIds)) {
            $queryBuilder
                ->andWhere('bookAuthor.author NOT IN (:authors)')
                ->setParameter('authors', $authorIds);
        }

        $queryBuilder->getQuery()
            ->execute();
    }
}
