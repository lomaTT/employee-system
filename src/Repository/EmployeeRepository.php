<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function findByUuid(Uuid $id): ?Employee
    {
        return $this->find($id);
    }

    public function save(Employee $employee): void
    {
        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();
    }
}
