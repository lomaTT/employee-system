<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CreateEmployerRequestDto;
use App\Factory\EmployeeFactory;
use App\Repository\EmployeeRepository;
use Symfony\Component\Uid\Uuid;

class EmployeeService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private EmployeeFactory $employeeFactory
    )
    {
    }

    public function createEmployer(CreateEmployerRequestDto $createEmployerRequestDto): Uuid
    {
        $employee = $this->employeeFactory->createEmployer(
            $createEmployerRequestDto->getName(),
            $createEmployerRequestDto->getSurname()
        );

        $this->employeeRepository->save($employee);

        return $employee->getId();
    }
}