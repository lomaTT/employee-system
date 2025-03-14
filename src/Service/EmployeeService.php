<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CreateEmployeeRequestDto;
use App\Factory\EmployeeFactory;
use App\Repository\EmployeeRepository;
use Symfony\Component\Uid\Uuid;

class EmployeeService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private EmployeeFactory    $employeeFactory
    )
    {
    }

    public function createEmployer(CreateEmployeeRequestDto $createEmployeeRequestDto): Uuid
    {
        $employee = $this->employeeFactory->createEmployee(
            $createEmployeeRequestDto->getName(),
            $createEmployeeRequestDto->getSurname()
        );

        $this->employeeRepository->save($employee);

        return $employee->getId();
    }
}