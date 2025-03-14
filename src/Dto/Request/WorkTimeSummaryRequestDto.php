<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Enum\DateType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class WorkTimeSummaryRequestDto
{
    private const FORMAT_DAY = 'Y-m-d';
    private const FORMAT_MONTH = 'Y-m';

    #[Assert\Uuid(message: 'Employee id must be a valid UUID.')]
    #[Assert\NotNull(message: 'Employee id is required.')]
    private ?string $employeeId;

    #[Assert\NotNull(message: 'Date is required.')]
    private ?string $date;

    #[Assert\Callback]
    public function validateDate(ExecutionContextInterface $context): void
    {
        if (!$this->isValidDateFormat(self::FORMAT_DAY) && !$this->isValidDateFormat(self::FORMAT_MONTH)) {
            $context->buildViolation('Date must be in Y-m or Y-m-d format.')
                ->atPath('date')
                ->addViolation();
        }
    }

    private function isValidDateFormat(string $format): bool
    {
        $date = \DateTime::createFromFormat($format, $this->date);
        return $date && $date->format($format) === $this->date;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setEmployeeId(string $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDateType(): DateType
    {
        return $this->isValidDateFormat(self::FORMAT_DAY) ? DateType::DAILY : DateType::MONTHLY;
    }
}