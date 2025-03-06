<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ViewReportAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ViewReportAuthor>
 */
class ViewReportAuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ViewReportAuthor::class);
    }
}
