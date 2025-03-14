<?php

declare(strict_types=1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWorkTimeRequestDto
{
    public function __construct(
        #[Assert\DateTime(format: 'd.m.Y H:i', message: 'Start date time must be in date.month.year hours:minutes format.')]
        #[Assert\NotBlank(message: 'Start date time must be not blank.')]
        private ?string $startDateTime,

        #[Assert\DateTime(format: 'd.m.Y H:i', message: 'End date time must be in date.month.year hours:minutes format.')]
        #[Assert\NotBlank(message: 'End date time must be not blank.')]
        #[Assert\GreaterThan(propertyPath: 'startDateTime', message: 'End date time must be greater than start date time.')]
        private ?string $endDateTime,

        #[Assert\Uuid(message: 'Employee id must be a valid UUID.')]
        #[Assert\NotBlank(message: 'Employee id must be not blank.')]
        private ?string $employeeId
    )
    {
    }

    public function getStartDateTime(): ?string
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): ?string
    {
        return $this->endDateTime;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function setStartDateTime(string $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function setEndDateTime(string $endDateTime): static
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function setEmployeeId(string $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }
}