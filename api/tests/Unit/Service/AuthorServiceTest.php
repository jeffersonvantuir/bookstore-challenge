<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\Filter\AuthorFilterDto;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Repository\AuthorRepository;
use App\Repository\BookAuthorRepository;
use App\Service\AuthorService;
use App\ValueObject\AuthorCreateValueObject;
use App\ValueObject\AuthorUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthorServiceTest extends TestCase
{
    private AuthorService $authorService;

    private MockObject|EntityManagerInterface $entityManager;

    private MockObject|AuthorRepository $authorRepository;

    public function testList(): void
    {
        $authors = [
            ['id' => 1, 'name' => 'José Silva'],
            ['id' => 2, 'name' => 'Maria Joaquina'],
            ['id' => 3, 'name' => 'Carlos Silva'],
            ['id' => 4, 'name' => 'Jefferson Vantuir'],
        ];

        $dto = new AuthorFilterDto();
        $this->authorRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn($authors);

        $list = $this->authorService->list($dto);
        self::assertSame($authors, $list);
        self::assertCount(4, $list);
    }

    public function testListWhenHasNoResult(): void
    {
        $dto = new AuthorFilterDto();
        $this->authorRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn([]);

        $list = $this->authorService->list($dto);
        self::assertSame([], $list);
        self::assertCount(0, $list);
    }

    public function testCreateWhenNameIsNotProvided(): void
    {
        $valueObject = (new AuthorCreateValueObject());

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Nome não pode ser vazio.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->authorService->create($valueObject);
    }

    public function testCreateWhenNameExceeds40Characters(): void
    {
        $valueObject = new AuthorCreateValueObject(
            'José Augusto Fernando da Costa e Silva Oliveira'
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Nome não pode ultrapassar 40 caracteres.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->authorService->create($valueObject);
    }

    public function testCreate(): void
    {
        $valueObject = new AuthorCreateValueObject('Jefferson Vantuir');

        $author = (new Author())
            ->setName($valueObject->getName());

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($author);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->authorService->create($valueObject);
    }

    public function testUpdateWhenNameIsNotProvided(): void
    {
        $valueObject = new AuthorUpdateValueObject(
            1,
            null
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Nome não pode ser vazio.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->authorService->update($valueObject);
    }

    public function testUpdateWhenNameExceeds40Characters(): void
    {
        $valueObject = new AuthorUpdateValueObject(
            1,
            'José Augusto Fernando da Costa e Silva Oliveira'
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Nome não pode ultrapassar 40 caracteres.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->authorService->update($valueObject);
    }

    public function testUpdateWhenAuthorNotFound(): void
    {
        $valueObject = new AuthorUpdateValueObject(
            5,
            'Maria Joaquina'
        );

        $this->authorRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Autor não encontrado com ID 5');

        $this->authorService->update($valueObject);
    }

    public function testUpdate(): void
    {
        $valueObject = new AuthorUpdateValueObject(
            1,
            'Jefferson Vantuir'
        );

        $author = (new Author())
            ->setId($valueObject->getId())
            ->setName('Jefferson Behling');

        $this->authorRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($author);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->authorService->update($valueObject);
    }

    public function testDeleteWhenAuthorNotFound(): void
    {
        $this->authorRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Autor não encontrado com ID 1');

        $this->authorService->delete(1);
    }

    public function testDeleteWhenAuthorIsLinkedToBook(): void
    {
        $author = (new Author())
            ->setId(1)
            ->setName('Jefferson Behling');

        $this->authorRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($author);

        $bookAuthorRepository = $this->createMock(BookAuthorRepository::class);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(BookAuthor::class)
            ->willReturn($bookAuthorRepository);

        $book = (new Book())
            ->setId(rand(1, 10));

        $bookAuthor = (new BookAuthor())
            ->setBook($book)
            ->setAuthor($author);

        $bookAuthorRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['author' => 1]
            )
            ->willReturn([$bookAuthor]);

        $this->entityManager->expects(self::never())
            ->method('remove');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Existem livros vinculados ao autor. Remova os vínculos primeiro.');

        $this->authorService->delete(1);
    }

    public function testDelete(): void
    {
        $author = (new Author())
            ->setId(1)
            ->setName('Jefferson Behling');

        $this->authorRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($author);

        $bookAuthorRepository = $this->createMock(BookAuthorRepository::class);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(BookAuthor::class)
            ->willReturn($bookAuthorRepository);

        $bookAuthorRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['author' => 1]
            )
            ->willReturn([]);

        $this->entityManager->expects(self::once())
            ->method('remove')
            ->with($author);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->authorService->delete(1);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->authorRepository = $this->createMock(AuthorRepository::class);

        $this->authorService = new AuthorService($this->entityManager, $this->authorRepository);
    }
}
