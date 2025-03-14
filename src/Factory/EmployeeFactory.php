<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Employee;

class EmployeeFactory
{
    public function createEmployer(
        string $name,
        string $surname
    ): Employee {
        $employer = new Employee();
        $employer->setName($name);
        $employer->setSurname($surname);

        return $employer;
    }

}