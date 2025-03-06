<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Filter\SubjectFilterDto;
use App\Service\SubjectService;
use App\ValueObject\SubjectCreateValueObject;
use App\ValueObject\SubjectUpdateValueObject;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/subject', name:'api_subject')]
class SubjectController extends AbstractController
{
    use EmptyContentValidatorTrait;

    public function __construct(
        private readonly SubjectService $subjectService,
    ) {
    }

    #[Route('', name: '_list', methods: [Request::METHOD_GET])]
    public function index(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        $filterDto = (new SubjectFilterDto($request->query->get('description')));

        $list = $this->subjectService->list($filterDto);

        $pagination = $paginator->paginate(
            $list,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->json(
            [
                'data' => $pagination->getItems(),
                'pagination' => [
                    'page' => $pagination->getCurrentPageNumber(),
                    'limit' => $pagination->getItemNumberPerPage(),
                    'total' => $pagination->getTotalItemCount(),
                    'pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
                ]
            ]
        );
    }

    #[Route('/{id}', name: '_get', methods: [Request::METHOD_GET])]
    public function get(
        int $id,
    ): JsonResponse {
        try {
            $subject = $this->subjectService->getEntity($id);

            return $this->json(
                [
                    'data' => [
                        'id' => $subject->getId(),
                        'description' => $subject->getDescription(),
                    ],
                ]
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => $throwable->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('', name: '_create', methods: [Request::METHOD_POST])]
    public function create(
        Request $request,
    ): JsonResponse {
        try {
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new SubjectCreateValueObject($data['description'] ?? null);

            $this->subjectService->create($valueObject);

            return $this->json(
                ['message' => 'Assunto criado com sucesso.'],
                Response::HTTP_CREATED
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => $throwable->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id}', name: '_update', methods: [Request::METHOD_PUT])]
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        try {
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new SubjectUpdateValueObject($id, $data['description'] ?? null);

            $this->subjectService->update($valueObject);

            return $this->json(
                ['message' => 'Assunto atualizado com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => $throwable->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id}', name: '_delete', methods: [Request::METHOD_DELETE])]
    public function delete(
        int $id
    ): JsonResponse {
        try {
            $this->subjectService->delete($id);

            return $this->json(
                ['message' => 'Assunto excluído com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => $throwable->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
