<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ConfigSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigSettingRepository::class)]
#[ORM\Table(name: 'config_settings')]
class ConfigSetting
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $key;

    #[ORM\Column(length: 255)]
    private string $value;

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }
}