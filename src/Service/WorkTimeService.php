<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CreateWorkTimeRequestDto;
use App\Exception\EmployeeNotFoundException;
use App\Exception\InvalidWorkTimeException;
use App\Factory\WorkTimeFactory;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use DateMalformedStringException;
use DateTime;
use Exception;
use Symfony\Component\Uid\Uuid;

class WorkTimeService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private readonly WorkTimeFactory $workTimeFactory,
        private WorkTimeRepository $workTimeRepository
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
}