<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function validate(mixed $object): ?JsonResponse
    {
        $violations = $this->validator->validate($object);

        if (count($violations) > 0) {
            $violationMessages = [];

            foreach ($violations as $violation) {
                $violationMessages[] = $violation->getMessage();
            }

            return new JsonResponse(
                ['errors' => $violationMessages],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return null;
    }
}