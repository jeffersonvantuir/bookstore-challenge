<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Repository\BookAuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class BookAuthorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookAuthorRepository $bookAuthorRepository,
        private AuthorService $authorService,
    ) {
    }

    /**
     * @return array<array<string, int|string>>
     */
    public function list(int $bookId): array
    {
        $list = $this->bookAuthorRepository->findBy(
            ['book' => $bookId],
        );

        if (empty($list)) {
            return [];
        }

        return array_map(function (BookAuthor $bookAuthor) {
            $author = $bookAuthor->getAuthor();

            return [
                'id' => $author->getId(),
                'name' => $author->getName(),
            ];
        }, $list);
    }

    /**
     * @param int[] $authorsIds
     */
    public function linkBookToAuthors(Book $book, array $authorsIds = []): void
    {
        $this->bookAuthorRepository->removeLinkNotInAuthorsIds($book->getId(), $authorsIds);

        if (empty($authorsIds)) {
            return;
        }

        foreach ($authorsIds as $authorId) {
            $bookAuthor = $this->bookAuthorRepository->findOneBy([
                'book' => $book,
                'author' => $authorId
            ]);

            if (null !== $bookAuthor) {
                continue;
            }

            $author = $this->authorService->getEntity($authorId);

            $bookAuthor = (new BookAuthor())
                ->setBook($book)
                ->setAuthor($author);

            $this->entityManager->persist($bookAuthor);
        }

        $this->entityManager->flush();
    }
}
