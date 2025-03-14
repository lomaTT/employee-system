<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\CreateEmployerRequestDto;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeController extends AbstractController
{
    public function __construct(
        public EmployeeRepository     $employeeRepository,
        public EntityManagerInterface $entityManager,
        public ValidatorInterface     $validator,
        public SerializerInterface    $serializer,
        private readonly EmployeeService $employeeService,
        private readonly LoggerInterface $logger
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
        /** @var CreateEmployerRequestDto $createEmployeeRequestDto */
        $createEmployeeRequestDto = $this->serializer->deserialize(
            $request->getContent(),
            CreateEmployerRequestDto::class,
            'json'
        );

        $violations = $this->validator->validate($createEmployeeRequestDto);

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
            $employeeId = $this->employeeService->createEmployer($createEmployeeRequestDto);
            $this->logger->info('Employer created', ['id' => $employeeId->toRfc4122()]);

            return new JsonResponse([
                'id' => $employeeId->toRfc4122(),
            ]);
        } catch (Exception $e) {
            $this->logger->error('Employer creation failed', ['error' => $e->getMessage()]);

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
