<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    /**
     * @var Collection<int, WorkTime>
     */
    #[ORM\OneToMany(targetEntity: WorkTime::class, mappedBy: 'employee', orphanRemoval: true)]
    private Collection $workTimes;

    public function __construct()
    {
        $this->workTimes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Collection<int, WorkTime>
     */
    public function getWorkTimes(): Collection
    {
        return $this->workTimes;
    }

    public function addWorkTime(WorkTime $workTime): static
    {
        if (!$this->workTimes->contains($workTime)) {
            $this->workTimes->add($workTime);
            $workTime->setEmployee($this);
        }

        return $this;
    }

    public function removeWorkTime(WorkTime $workTime): static
    {
        if ($this->workTimes->removeElement($workTime)) {
            // set the owning side to null (unless already changed)
            if ($workTime->getEmployee() === $this) {
                $workTime->setEmployee(null);
            }
        }

        return $this;
    }
}
