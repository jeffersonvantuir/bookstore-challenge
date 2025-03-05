<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Filter\SubjectFilterDto;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function findByFilterDto(SubjectFilterDto $filterDto): array
    {
        $queryBuilder = $this->createQueryBuilder('subject');

        $query = $queryBuilder->select('subject.id, subject.description');

        if (false === empty($filterDto->getDescription())) {
            $query->andWhere('subject.description LIKE :description')
                ->setParameter('description', '%' . $filterDto->getDescription() . '%');
        }

        $query = $query->orderBy('subject.description', Order::Ascending->value)
            ->getQuery();

        return $query->getArrayResult();
    }
}
