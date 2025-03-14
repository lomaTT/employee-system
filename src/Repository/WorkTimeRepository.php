<?php

namespace App\Repository;

use App\Entity\WorkTime;
use DateTime;
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

    public function getEmployeeTodayWorkTime(string $employeeId, string $startDate): ?array
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder()
            ->select('wt')
            ->from(WorkTime::class, 'wt')
            ->where('wt.employee = :employeeId')
            ->andWhere('wt.startDate = :startDate')
            ->setParameter('employeeId', Uuid::fromString($employeeId), UuidType::NAME)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getResult();
    }

    public function getEmployeeDailyWorkTime(string $employeeId, string $startDate): ?WorkTime
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder()
            ->select('wt')
            ->from(WorkTime::class, 'wt')
            ->where('wt.employee = :employeeId')
            ->andWhere('wt.startDate = :startDate')
            ->setParameter('employeeId', Uuid::fromString($employeeId), UuidType::NAME)
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getEmployeeMonthlyWorkTime(string $employeeId, DateTime $date): array
    {
        $date = $date->format('Y-m');

        $entityManager = $this->getEntityManager();

        return $entityManager->createQueryBuilder()
            ->select('wt')
            ->from(WorkTime::class, 'wt')
            ->where('wt.employee = :employeeId')
            ->andWhere('wt.startDate LIKE :date')
            ->setParameter('employeeId', Uuid::fromString($employeeId), UuidType::NAME)
            ->setParameter('date', "$date%")
            ->getQuery()
            ->getResult();
    }
}
