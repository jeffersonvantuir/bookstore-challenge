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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/author', name:'api_author')]
class AuthorController extends AbstractController
{
    public function __construct(
        private readonly AuthorService $authorService,
    ) {
    }

    #[Route('', name: '_list', methods: [Request::METHOD_GET])]
    public function index(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
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
    }

    #[Route('', name: '_create', methods: [Request::METHOD_POST])]
    public function create(
        Request $request,
    ): JsonResponse {
        try {
            if ('' === $request->getContent()) {
                throw new BadRequestHttpException('Nenhum dado foi enviado na requisição.');
            }

            $data = $request->toArray();

            $valueObject = new AuthorCreateValueObject($data['name'] ?? null);

            $this->authorService->create($valueObject);

            return $this->json(
                ['message' => 'Autor criado com sucesso.'],
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
            if ('' === $request->getContent()) {
                throw new BadRequestHttpException('Nenhum dado foi enviado na requisição.');
            }

            $data = $request->toArray();

            $valueObject = new AuthorUpdateValueObject($id, $data['name'] ?? null);

            $this->authorService->update($valueObject);

            return $this->json(
                ['message' => 'Autor atualizado com sucesso.'],
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
            $this->authorService->delete($id);

            return $this->json(
                ['message' => 'Autor excluído com sucesso.'],
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
