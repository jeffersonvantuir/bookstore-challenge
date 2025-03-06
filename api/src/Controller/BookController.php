<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Filter\BookFilterDto;
use App\Service\BookService;
use App\ValueObject\BookCreateValueObject;
use App\ValueObject\BookUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/book', name:'api_book')]
class BookController extends AbstractController
{
    use EmptyContentValidatorTrait;

    public function __construct(
        private readonly BookService $bookService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: '_list', methods: [Request::METHOD_GET])]
    public function index(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        try {
            $filterDto = new BookFilterDto(
                $request->query->get('title'),
                $request->query->get('publisher'),
                $request->query->get('edition'),
                $request->query->get('publicationYear'),
                $request->query->get('authorName'),
                $request->query->get('subjectDescription'),
            );

            $list = $this->bookService->list($filterDto);

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
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: '_get', methods: [Request::METHOD_GET])]
    public function get(
        int $id,
    ): JsonResponse {
        try {
            return $this->json(
                ['data' => $this->bookService->get($id)]
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('', name: '_create', methods: [Request::METHOD_POST])]
    public function create(
        Request $request,
    ): JsonResponse {
        try {
            $this->entityManager->beginTransaction();
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new BookCreateValueObject(
                $data['title'] ?? null,
                $data['publisher'] ?? null,
                false === empty($data['edition']) ? (int) $data['edition'] : null,
                false === empty($data['publicationYear']) ? (string) $data['publicationYear'] : null,
                false === empty($data['amount']) ? (float) $data['amount'] : null,
                $data['authorsIds'] ?? [],
                $data['subjectsIds'] ?? [],
            );

            $this->bookService->create($valueObject);
            $this->entityManager->commit();

            return $this->json(
                ['message' => 'Livro criado com sucesso.'],
                Response::HTTP_CREATED
            );
        } catch (\DomainException $exception) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: '_update', methods: [Request::METHOD_PUT])]
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        try {
            $this->entityManager->beginTransaction();
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new BookUpdateValueObject(
                $id,
                $data['title'] ?? null,
                $data['publisher'] ?? null,
                false === empty($data['edition']) ? (int) $data['edition'] : null,
                false === empty($data['publicationYear']) ? (string) $data['publicationYear'] : null,
                false === empty($data['amount']) ? (float) $data['amount'] : null,
                $data['authorsIds'] ?? [],
                $data['subjectsIds'] ?? [],
            );

            $this->bookService->update($valueObject);
            $this->entityManager->commit();

            return $this->json(
                ['message' => 'Livro atualizado com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\DomainException $exception) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: '_delete', methods: [Request::METHOD_DELETE])]
    public function delete(
        int $id
    ): JsonResponse {
        try {
            $this->entityManager->beginTransaction();
            $this->bookService->delete($id);
            $this->entityManager->commit();

            return $this->json(
                ['message' => 'Livro excluído com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\DomainException $exception) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            $this->entityManager->rollback();

            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
