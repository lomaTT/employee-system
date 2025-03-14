<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Employee;

class EmployeeFactory
{
    public function createEmployee(
        string $name,
        string $surname
    ): Employee {
        $employee = new Employee();
        $employee->setName($name);
        $employee->setSurname($surname);

        return $employee;
    }

}