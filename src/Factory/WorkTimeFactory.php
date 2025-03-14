<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Employee;
use App\Entity\WorkTime;
use DateTime;

class WorkTimeFactory
{
    public function createWorkTime(
        DateTime $startDateTime,
        DateTime $endDateTime,
        Employee $employee
    ): WorkTime {
        $workTime = new WorkTime();
        $workTime->setStartDateTime($startDateTime);
        $workTime->setEndDateTime($endDateTime);
        $workTime->setEmployee($employee);
        $workTime->setStartDate($startDateTime);

        return $workTime;
    }
}