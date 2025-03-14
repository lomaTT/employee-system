<?php

declare(strict_types=1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateEmployeeRequestDto
{
    public function __construct(
        #[Assert\NotNull(message: 'Name is required.')]
        #[Assert\Length(
            min: 1,
            max: 255,
            minMessage: 'Name must be at least {{ limit }} characters long.',
            maxMessage: 'Name cannot be longer than {{ limit }} characters.'
        )]
        private ?string $name,

        #[Assert\NotNull(message: 'Surname is required.')]
        #[Assert\Length(
            min: 1,
            max: 255,
            minMessage: 'Surname must be at least {{ limit }} characters long.',
            maxMessage: 'Surname cannot be longer than {{ limit }} characters.'
        )]
        private ?string $surname
    )
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }
}