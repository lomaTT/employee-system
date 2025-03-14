<?php

namespace App\Repository;

use App\Entity\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<WorkTime>
 */
class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    public function save(WorkTime $workTime): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($workTime);
        $entityManager->flush();
    }

    public function getEmployeeTodayWorkTime(string $employerId, string $startDate): ?array
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder()
            ->select('wt')
            ->from(WorkTime::class, 'wt')
            ->where('wt.employee = :employerId')
            ->andWhere('wt.startDate = :startDate')
            ->setParameter('employerId', Uuid::fromString($employerId), UuidType::NAME)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getResult();
    }
}
