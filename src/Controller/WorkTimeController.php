<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\CreateWorkTimeRequestDto;
use App\Dto\Request\WorkTimeSummaryRequestDto;
use App\Service\PaymentService;
use App\Service\ValidationService;
use App\Service\WorkTimeService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class WorkTimeController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly WorkTimeService     $workTimeService,
        private readonly LoggerInterface     $logger,
        private readonly ValidationService   $validationService,
        private readonly PaymentService      $paymentService
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

        $validationResponse = $this->validationService->validate($createWorkTimeRequestDto);

        if ($validationResponse) {
            return $validationResponse;
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

    #[Route('/work-time/summary/', name: 'work_times_summary', methods: ['GET'])]
    public function getWorkTimesSummary(Request $request): Response
    {
        $queryParameters = $request->query->all();

        /** @var WorkTimeSummaryRequestDto $workTimeSummaryRequestDto */
        $workTimeSummaryRequestDto = $this->serializer->deserialize(
            json_encode($queryParameters),
            WorkTimeSummaryRequestDto::class,
            'json'
        );

        $validationResponse = $this->validationService->validate($workTimeSummaryRequestDto);

        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $timeSummary = $this->workTimeService->getTimeSummary($workTimeSummaryRequestDto);

            $paymentDetails = $this->paymentService->calculatePayment(
                $timeSummary,
                $workTimeSummaryRequestDto->getDateType()
            );

            return new JsonResponse($paymentDetails);
        } catch (Exception $e) {
            $this->logger->error('Work time summary request failed.', ['error' => $e->getMessage()]);

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}