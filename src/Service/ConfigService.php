<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ConfigSettingNotFoundException;
use App\Exception\InvalidConfigValueException;
use App\Repository\ConfigSettingRepository;

class ConfigService
{
    public function __construct(
        private readonly ConfigSettingRepository $configSettingRepository,
    ) {}

    /**
     * @throws ConfigSettingNotFoundException
     * @throws InvalidConfigValueException
     */
    public function getIntConfigValue(string $key): int
    {
        $value = $this->getConfigValue($key);

        if (!ctype_digit($value)) {
            throw new InvalidConfigValueException(sprintf('Value for key "%s" is not a valid integer.', $key));
        }

        return (int) $value;
    }

    /**
     * @throws ConfigSettingNotFoundException
     * @throws InvalidConfigValueException
     */
    public function getFloatConfigValue(string $key): float
    {
        $value = $this->getConfigValue($key);

        if (!is_numeric($value)) {
            throw new InvalidConfigValueException(sprintf('Value for key "%s" is not a valid float.', $key));
        }

        return (float) $value;
    }

    /**
     * @throws ConfigSettingNotFoundException
     */
    public function getStringConfigValue(string $key): string
    {
        return $this->getConfigValue($key);
    }

    /**
     * @throws ConfigSettingNotFoundException
     */
    private function getConfigValue(string $key): string
    {
        $configSetting = $this->configSettingRepository->find($key);

        if ($configSetting === null) {
            throw new ConfigSettingNotFoundException(sprintf('Configuration setting "%s" not found.', $key));
        }

        return $configSetting->getValue();
    }
}