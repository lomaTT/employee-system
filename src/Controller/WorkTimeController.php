<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\CreateWorkTimeRequestDto;
use App\Service\WorkTimeService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkTimeController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly WorkTimeService $workTimeService,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Route('/work-time', name: 'work_time', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $createWorkTimeRequestDto = $this->serializer->deserialize(
            $request->getContent(),
            CreateWorkTimeRequestDto::class,
            'json'
        );

        $violations = $this->validator->validate($createWorkTimeRequestDto);

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

        try {
            $this->workTimeService->addWorkTime($createWorkTimeRequestDto);

            return new JsonResponse([
                'message' => 'Work time added successfully.',
            ]);
        } catch (Exception $e) {
            $this->logger->error('Work time creation failed.', ['error' => $e->getMessage()]);

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}