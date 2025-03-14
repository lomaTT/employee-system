<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\DateType;

class PaymentService
{
    public function __construct(
        private readonly ConfigService   $configService
    )
    {
    }

    public function calculatePayment(
        float $timeSummary,
        DateType $dateType
    ): array
    {
        $hourlyRate = $this->configService->getFloatConfigValue('hourly_rate');
        $overTimePercent = $this->configService->getIntConfigValue('overtime_percent');
        $monthlyHours = $this->configService->getIntConfigValue('monthly_hours');

        if ($dateType == DateType::DAILY) {
            return $this->calculateDailyPayment($timeSummary, $hourlyRate);
        } else {
            return $this->calculateMonthlyPayment($timeSummary, $hourlyRate, $overTimePercent, $monthlyHours);
        }
    }

    private function calculateMonthlyPayment(
        float $timeSummary,
        float $hourlyRate,
        int $overTimePercent,
        int $monthlyHours
    ): array
    {
        $normalHoursFromThisMonth = min($monthlyHours, $timeSummary);
        $overTimeHours = max(0, $timeSummary - $normalHoursFromThisMonth);
        $overTimeRate = $hourlyRate * $overTimePercent / 100;
        $totalPayment = $normalHoursFromThisMonth * $hourlyRate + $overTimeHours * $overTimeRate;

        return [
            'normalHours' => $normalHoursFromThisMonth,
            'hourlyRate' => $hourlyRate,
            'overtimeHours' => $overTimeHours,
            'overtimeRate' => $overTimeRate,
            'totalPayment' => $totalPayment,
        ];
    }

    private function calculateDailyPayment(float $timeSummary, float $hourlyRate): array
    {
        $totalPayment = $timeSummary * $hourlyRate;

        return [
            'totalPayment' => $totalPayment,
            'hoursCount' => $timeSummary,
            'hourlyRate' => $hourlyRate
        ];
    }
}