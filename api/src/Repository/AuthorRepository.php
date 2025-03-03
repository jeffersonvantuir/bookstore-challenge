<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Filter\AuthorFilterDto;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findByFilterDto(AuthorFilterDto $filterDto): array
    {
        $queryBuilder = $this->createQueryBuilder('author');

        $query = $queryBuilder->select('author.id, author.name');

        if (false === empty($filterDto->getName())) {
            $query->andWhere('author.name LIKE :name')
                ->setParameter('name', '%' . $filterDto->getName() . '%');
        }

        $query = $query->orderBy('author.name', Order::Ascending->value)
            ->getQuery();

        return $query->getArrayResult();
    }
}
