<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\CreateEmployeeRequestDto;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class EmployeeController extends AbstractController
{
    public function __construct(
        public EmployeeRepository          $employeeRepository,
        public EntityManagerInterface      $entityManager,
        public SerializerInterface         $serializer,
        private readonly EmployeeService   $employeeService,
        private readonly LoggerInterface   $logger,
        private readonly ValidationService $validationService
    )
    {
    }

    #[Route('/employee/{id}', name: 'employee', methods: ['GET'])]
    public function employer(Uuid $id): Response
    {
        $employee = $this->employeeRepository->findByUuid($id);

        return new JsonResponse([
            'id' => $employee->getId()->toRfc4122(),
            'name' => $employee->getName(),
            'surname' => $employee->getSurname(),
        ]);
    }

    #[Route('/employee', name: 'employee_create', methods: ['POST'])]
    public function create(
        Request $request,
    ): Response
    {
        /** @var CreateEmployeeRequestDto $createEmployeeRequestDto */
        $createEmployeeRequestDto = $this->serializer->deserialize(
            $request->getContent(),
            CreateEmployeeRequestDto::class,
            'json'
        );

        $validationResponse = $this->validationService->validate($createEmployeeRequestDto);

        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $employeeId = $this->employeeService->createEmployer($createEmployeeRequestDto);
            $this->logger->info('Employee created', ['id' => $employeeId->toRfc4122()]);

            return new JsonResponse([
                'id' => $employeeId->toRfc4122(),
            ]);
        } catch (Exception $e) {
            $this->logger->error('Employee creation failed', ['error' => $e->getMessage()]);

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
