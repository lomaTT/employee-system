<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CreateWorkTimeRequestDto;
use App\Dto\Request\WorkTimeSummaryRequestDto;
use App\Entity\WorkTime;
use App\Enum\DateType;
use App\Exception\EmployeeNotFoundException;
use App\Exception\InvalidWorkTimeException;
use App\Factory\WorkTimeFactory;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Uid\Uuid;

class WorkTimeService
{
    public function __construct(
        private EmployeeRepository       $employeeRepository,
        private readonly WorkTimeFactory $workTimeFactory,
        private WorkTimeRepository       $workTimeRepository,
    )
    {
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function addWorkTime(CreateWorkTimeRequestDto $createWorkTimeRequestDto): void
    {
        $employee = $this->employeeRepository->findByUuid(Uuid::fromString($createWorkTimeRequestDto->getEmployeeId()));

        if (!$employee) {
            throw new EmployeeNotFoundException('Employee not found.');
        }

        $this->checkProperWorkTime($createWorkTimeRequestDto);

        $workTime = $this->workTimeFactory->createWorkTime(
            new DateTime($createWorkTimeRequestDto->getStartDateTime()),
            new DateTime($createWorkTimeRequestDto->getEndDateTime()),
            $employee
        );

        $this->workTimeRepository->save($workTime);
    }

    /**
     * @throws Exception
     */
    private function checkProperWorkTime(CreateWorkTimeRequestDto $createWorkTimeRequestDto): void
    {
        $startDateTime = new DateTime($createWorkTimeRequestDto->getStartDateTime());
        $endDateTime = new DateTime($createWorkTimeRequestDto->getEndDateTime());

        if ($startDateTime->diff($endDateTime)->h > 12) {
            throw new InvalidWorkTimeException('Work time cannot be longer than 12 hours.');
        }

        $todayWorkTime = $this->workTimeRepository->getEmployeeTodayWorkTime(
            $createWorkTimeRequestDto->getEmployeeId(),
            date($startDateTime->format('Y-m-d'))
        );

        if ($todayWorkTime) {
            throw new InvalidWorkTimeException('Employee already has work time for today.');
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getTimeSummary(WorkTimeSummaryRequestDto $workTimeSummaryRequestDto): float
    {
        $employerId = $workTimeSummaryRequestDto->getEmployeeId();
        $date = $workTimeSummaryRequestDto->getDate();
        $dateType = $workTimeSummaryRequestDto->getDateType();

        if ($dateType === DateType::DAILY) {
            return $this->calculateDailyWorkTime($employerId, $date);
        } else {
            return $this->calculateMonthlyWorkTime($employerId, new DateTime($date));
        }
    }

    private function calculateDailyWorkTime(string $employeeId, string $date): float
    {
        $workTime = $this->workTimeRepository->getEmployeeDailyWorkTime($employeeId, $date);

        if (!$workTime) {
            return 0.0;
        }

        return $this->roundWorkHours($workTime->getStartDateTime(), $workTime->getEndDateTime());
    }

    private function calculateMonthlyWorkTime(string $employeeId, DateTime $date): float
    {
        $workTimes = $this->workTimeRepository->getEmployeeMonthlyWorkTime($employeeId, $date);
        $totalHours = 0.0;

        foreach ($workTimes as $dailyWorkTime) {
            /** @var WorkTime $dailyWorkTime */
            $totalHours += $this->roundWorkHours($dailyWorkTime->getStartDateTime(), $dailyWorkTime->getEndDateTime());
        }

        return $totalHours;
    }

    private function roundWorkHours(DateTimeInterface $start, DateTimeInterface $end): float
    {
        $diff = $start->diff($end);
        $hours = $diff->h;
        $minutes = $diff->i;

        if ($minutes < 15) {
            return $hours;
        } elseif ($minutes < 45) {
            return $hours + 0.5;
        } else {
            return $hours + 1;
        }
    }
}