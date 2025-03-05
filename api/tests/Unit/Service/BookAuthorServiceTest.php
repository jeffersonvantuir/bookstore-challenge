<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Entity\Author;
use App\Repository\BookAuthorRepository;
use App\Service\BookAuthorService;
use App\Service\AuthorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookAuthorServiceTest extends TestCase
{
    private BookAuthorService $bookAuthorService;

    private MockObject|EntityManagerInterface $entityManager;

    private MockObject|BookAuthorRepository $bookAuthorRepository;

    private MockObject|AuthorService $authorService;

    public function testListWhenNotFound(): void
    {
        $bookId = 1;

        $this->bookAuthorRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['book' => $bookId],
            )
            ->willReturn([]);

        $return = $this->bookAuthorService->list($bookId);

        self::assertSame([], $return);
    }

    public function testList(): void
    {
        $bookId = 1;
        $authorId = 3;

        $book = (new Book())
            ->setId($bookId);

        $author = (new Author())
            ->setId($authorId)
            ->setName('Jefferson');

        $bookAuthor = (new BookAuthor())
            ->setBook($book)
            ->setAuthor($author);

        $this->bookAuthorRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['book' => $bookId],
            )
            ->willReturn([$bookAuthor]);

        $return = $this->bookAuthorService->list($bookId);

        $expected = [
            [
                'id' => 3,
                'name' => 'Jefferson',
            ]
        ];

        self::assertSame($expected, $return);
    }

    public function testLinkBookToAuthorsWhenAuthorsIdsIsNotProvided(): void
    {
        $bookId = 1;
        $authorsIds = [];

        $book = (new Book())
            ->setId($bookId);

        $this->bookAuthorRepository->expects(self::once())
            ->method('removeLinkNotInAuthorsIds')
            ->with(1, $authorsIds);

        $this->bookAuthorRepository->expects(self::never())
            ->method('findOneBy');

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        $this->bookAuthorService->linkBookToAuthors($book, $authorsIds);
    }

    public function testLinkBookToAuthorsWhenAllLinked(): void
    {
        $bookId = 1;
        $authorsIds = [5, 6];

        $book = (new Book())
            ->setId($bookId);

        $this->bookAuthorRepository->expects(self::once())
            ->method('removeLinkNotInAuthorsIds')
            ->with(1, $authorsIds);

        $author1 = (new Author())
            ->setId($authorsIds[0]);

        $author2 = (new Author())
            ->setId($authorsIds[1]);

        $bookAuthor1 = (new BookAuthor())
            ->SetBook($book)
            ->setAuthor($author1);

        $bookAuthor2 = (new BookAuthor())
            ->SetBook($book)
            ->setAuthor($author2);

        $this->bookAuthorRepository->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function ($index) use ($book, $authorsIds) {
                return [
                    'book' => $book,
                    'author' => $authorsIds[$index],
                ];
            })
            ->willReturn(
                $bookAuthor1,
                $bookAuthor2,
            );

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookAuthorService->linkBookToAuthors($book, $authorsIds);
    }

    public function testLinkBookToAuthors(): void
    {
        $bookId = 1;
        $authorsIds = [5, 6];

        $book = (new Book())
            ->setId($bookId);

        $this->bookAuthorRepository->expects(self::once())
            ->method('removeLinkNotInAuthorsIds')
            ->with(1, $authorsIds);

        $author1 = (new Author())
            ->setId($authorsIds[0]);

        $author2 = (new Author())
            ->setId($authorsIds[1]);

        $bookAuthor1 = (new BookAuthor())
            ->SetBook($book)
            ->setAuthor($author1);

        $this->bookAuthorRepository->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function ($index) use ($book, $authorsIds) {
                return [
                    'book' => $book,
                    'author' => $authorsIds[$index],
                ];
            })
            ->willReturn(
                $bookAuthor1,
                null,
            );

        $this->authorService->expects(self::once())
            ->method('getEntity')
            ->with(6)
            ->willReturn($author2);

        $bookAuthor = (new BookAuthor())
            ->setBook($book)
            ->setAuthor($author2);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($bookAuthor);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookAuthorService->linkBookToAuthors($book, $authorsIds);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bookAuthorRepository = $this->createMock(BookAuthorRepository::class);
        $this->authorService = $this->createMock(AuthorService::class);

        $this->bookAuthorService = new BookAuthorService(
            $this->entityManager,
            $this->bookAuthorRepository,
            $this->authorService
        );
    }
}
