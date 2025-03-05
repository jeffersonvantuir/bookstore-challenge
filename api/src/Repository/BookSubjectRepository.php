<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookSubject>
 */
class BookSubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookSubject::class);
    }

    /**
     * @param int[]|null $subjectsIds
     */
    public function removeLinkNotInSubjectsIds(int $bookId, ?array $subjectsIds = []): void
    {
        $queryBuilder = $this->createQueryBuilder('bookSubject');

        $queryBuilder->delete()
            ->where('bookSubject.book = :book')
            ->setParameter('book', $bookId);

        if (false === empty($subjectsIds)) {
            $queryBuilder
                ->andWhere('bookSubject.subject NOT IN (:subjects)')
                ->setParameter('subjects', $subjectsIds);
        }

        $queryBuilder->getQuery()
            ->execute();
    }
}
