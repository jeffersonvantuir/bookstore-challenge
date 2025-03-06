<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Filter\AuthorFilterDto;
use App\Service\AuthorService;
use App\ValueObject\AuthorCreateValueObject;
use App\ValueObject\AuthorUpdateValueObject;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/author', name:'api_author')]
class AuthorController extends AbstractController
{
    use EmptyContentValidatorTrait;

    public function __construct(
        private readonly AuthorService $authorService,
    ) {
    }

    #[Route('', name: '_list', methods: [Request::METHOD_GET])]
    public function index(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        try {
            $filterDto = new AuthorFilterDto($request->query->get('name'));

            $list = $this->authorService->list($filterDto);

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
        } catch (\Throwable) {
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
            $author = $this->authorService->getEntity($id);

            return $this->json(
                [
                    'data' => [
                        'id' => $author->getId(),
                        'name' => $author->getName(),
                    ],
                ]
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable) {
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
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new AuthorCreateValueObject($data['name'] ?? null);

            $this->authorService->create($valueObject);

            return $this->json(
                ['message' => 'Autor criado com sucesso.'],
                Response::HTTP_CREATED
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable) {
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
            $this->validateRequest($request);

            $data = $request->toArray();

            $valueObject = new AuthorUpdateValueObject($id, $data['name'] ?? null);

            $this->authorService->update($valueObject);

            return $this->json(
                ['message' => 'Autor atualizado com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable) {
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
            $this->authorService->delete($id);

            return $this->json(
                ['message' => 'Autor excluído com sucesso.'],
                Response::HTTP_OK
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable) {
            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
