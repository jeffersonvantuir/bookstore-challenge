<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\Filter\BookFilterDto;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookAuthorService;
use App\Service\BookService;
use App\Service\BookSubjectService;
use App\ValueObject\BookCreateValueObject;
use App\ValueObject\BookUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    private BookService $bookService;

    private MockObject|EntityManagerInterface $entityManager;

    private MockObject|BookRepository $bookRepository;

    private MockObject|BookSubjectService $bookSubjectService;

    private MockObject|BookAuthorService $bookAuthorService;

    public function testListWhenNotFind(): void
    {
        $dto = new BookFilterDto();
        $this->bookRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn([]);

        $return = $this->bookService->list($dto);
        self::assertCount(0, $return);
        self::assertSame([], $return);
    }

    public function testList(): void
    {
        $books = [
            [
                'id' => 1,
                'title' => 'Galinha Pintadinha',
                'publisher' => 'ABC Editora',
                'edition' => 1,
                'publicationYear' => '2024'
            ],
            [
                'id' => 2,
                'title' => 'O Pequeno Príncipe',
                'publisher' => 'XYZ Publicações',
                'edition' => 3,
                'publicationYear' => '2020'
            ],
            [
                'id' => 3,
                'title' => 'Aventuras de João e Maria',
                'publisher' => 'Editora Infantil',
                'edition' => 2,
                'publicationYear' => '2019'
            ],
            [
                'id' => 4,
                'title' => 'Mundo dos Dinossauros',
                'publisher' => 'Futura Kids',
                'edition' => 1,
                'publicationYear' => '2021'
            ],
            [
                'id' => 5,
                'title' => 'Contos de Fadas Modernos',
                'publisher' => 'Histórias & Magia',
                'edition' => 4,
                'publicationYear' => '2018'
            ],
        ];

        $dto = new BookFilterDto();
        $this->bookRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn($books);

        $this->bookSubjectService->expects(self::exactly(5))
            ->method('list')
            ->willReturnCallback(function ($index) use ($books) {
                return $books[$index];
            })
            ->willReturnOnConsecutiveCalls(
                [
                    ['id' => 1, 'description' => 'Infantil']
                ],
                [
                    ['id' => 1, 'description' => 'Infantil'],
                    ['id' => 2, 'description' => 'História']
                ],
                [
                    ['id' => 3, 'description' => 'Aventura']
                ],
                [],
                [
                    ['id' => 4, 'description' => 'Ficção']
                ]
            );

        $this->bookAuthorService->expects(self::exactly(5))
            ->method('list')
            ->willReturnCallback(function ($index) use ($books) {
                return $books[$index];
            })
            ->willReturnOnConsecutiveCalls(
                [
                    ['id' => 1, 'name' => 'João']
                ],
                [
                    ['id' => 1, 'name' => 'João'],
                ],
                [
                    ['id' => 2, 'name' => 'Maria'],
                ],
                [
                    ['id' => 1, 'name' => 'João'],
                    ['id' => 3, 'name' => 'Jefferson'],
                ],
                []
            );

        $expected = [
            [
                'id' => 1,
                'title' => 'Galinha Pintadinha',
                'publisher' => 'ABC Editora',
                'edition' => 1,
                'publicationYear' => '2024',
                'subjects' => [
                    ['id' => 1, 'description' => 'Infantil']
                ],
                'authors' => [
                    ['id' => 1, 'name' => 'João']
                ]
            ],
            [
                'id' => 2,
                'title' => 'O Pequeno Príncipe',
                'publisher' => 'XYZ Publicações',
                'edition' => 3,
                'publicationYear' => '2020',
                'subjects' => [
                    ['id' => 1, 'description' => 'Infantil'],
                    ['id' => 2, 'description' => 'História'],
                ],
                'authors' => [
                    ['id' => 1, 'name' => 'João']
                ]
            ],
            [
                'id' => 3,
                'title' => 'Aventuras de João e Maria',
                'publisher' => 'Editora Infantil',
                'edition' => 2,
                'publicationYear' => '2019',
                'subjects' => [
                    ['id' => 3, 'description' => 'Aventura'],
                ],
                'authors' => [
                    ['id' => 2, 'name' => 'Maria'],
                ]
            ],
            [
                'id' => 4,
                'title' => 'Mundo dos Dinossauros',
                'publisher' => 'Futura Kids',
                'edition' => 1,
                'publicationYear' => '2021',
                'subjects' => [
                ],
                'authors' => [
                    ['id' => 1, 'name' => 'João'],
                    ['id' => 3, 'name' => 'Jefferson'],
                ]
            ],
            [
                'id' => 5,
                'title' => 'Contos de Fadas Modernos',
                'publisher' => 'Histórias & Magia',
                'edition' => 4,
                'publicationYear' => '2018',
                'subjects' => [
                    ['id' => 4, 'description' => 'Ficção'],
                ],
                'authors' => [
                ]
            ],
        ];

        $return = $this->bookService->list($dto);
        self::assertCount(5, $return);
        self::assertSame($expected, $return);
    }

    public function testCreateWhenDataIsNotProvided(): void
    {
        $dto = new BookCreateValueObject();

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->bookSubjectService->expects(self::never())
            ->method(self::anything());

        $this->bookAuthorService->expects(self::never())
            ->method(self::anything());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage(
            '[title] Título é obrigatório. [amount] Valor do livro é obrigatório. ' .
            '[publisher] Editora é obrigatória. [edition] Edição é obrigatória. [publicationYear] O Ano da ' .
            'Publicação é obrigatória. [authorsIds] Os autores precisam ser informados. [authorsIds] O livro deve ' .
            'estar vinculado a pelo menos 1 autor. [subjectsIds] Os assuntos precisam ser informados. ' .
            '[subjectsIds] O livro deve estar vinculado a pelo menos 1 assunto.'
        );

        $this->bookService->create($dto);
    }

    public function testCreate(): void
    {
        $dto = new BookCreateValueObject(
            'Galinha Pintadinha',
            'ABC Editora',
            1,
            '2024',
            10.00,
            [1],
            [2],
        );

        $book = (new Book())
            ->setTitle($dto->getTitle())
            ->setPublisher($dto->getPublisher())
            ->setEdition($dto->getEdition())
            ->setPublicationYear($dto->getPublicationYear())
            ->setAmount($dto->getAmount());

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($book);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookSubjectService->expects(self::once())
            ->method('linkBookToSubjects')
            ->with(
                $book,
                $dto->getSubjectsIds()
            );

        $this->bookAuthorService->expects(self::once())
            ->method('linkBookToAuthors')
            ->with(
                $book,
                $dto->getAuthorsIds()
            );

        $this->bookService->create($dto);
    }

    public function testUpdateWhenBookNotFound(): void
    {
        $dto = new BookUpdateValueObject(
            1,
            'Galinha Pintadinha',
            'ABC Editora',
            1,
            '2024',
            10.00,
            [1],
            [2],
        );

        $this->bookRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Livro não encontrado com ID 1');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->bookSubjectService->expects(self::never())
            ->method(self::anything());

        $this->bookAuthorService->expects(self::never())
            ->method(self::anything());

        $this->bookService->update($dto);
    }

    public function testUpdate(): void
    {
        $dto = new BookUpdateValueObject(
            1,
            'Galinha Pintadinha',
            'ABC Editora',
            1,
            '2024',
            10.00,
            [1],
            [2],
        );

        $book = (new Book())
            ->setId($dto->getId())
            ->setTitle($dto->getTitle())
            ->setPublisher($dto->getPublisher())
            ->setEdition($dto->getEdition())
            ->setPublicationYear($dto->getPublicationYear())
            ->setAmount($dto->getAmount());

        $this->bookRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($book);

        $this->bookSubjectService->expects(self::once())
            ->method('linkBookToSubjects')
            ->with(
                $book,
                $dto->getSubjectsIds()
            );

        $this->bookAuthorService->expects(self::once())
            ->method('linkBookToAuthors')
            ->with(
                $book,
                $dto->getAuthorsIds()
            );

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookService->update($dto);
    }

    public function testDeleteWhenBookNotFound(): void
    {
        $bookId = 1;
        $this->bookRepository->expects(self::once())
            ->method('find')
            ->with($bookId)
            ->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Livro não encontrado com ID 1');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->bookSubjectService->expects(self::never())
            ->method(self::anything());

        $this->bookAuthorService->expects(self::never())
            ->method(self::anything());

        $this->bookService->delete($bookId);
    }

    public function testDelete(): void
    {
        $bookId = 1;

        $book = (new Book())
            ->setId($bookId);

        $this->bookRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($book);

        $this->bookSubjectService->expects(self::once())
            ->method('linkBookToSubjects')
            ->with(
                $book
            );

        $this->bookAuthorService->expects(self::once())
            ->method('linkBookToAuthors')
            ->with(
                $book
            );

        $this->entityManager->expects(self::once())
            ->method('remove')
            ->with($book);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookService->delete($bookId);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookSubjectService = $this->createMock(BookSubjectService::class);
        $this->bookAuthorService = $this->createMock(BookAuthorService::class);

        $this->bookService = new BookService(
            $this->entityManager,
            $this->bookRepository,
            $this->bookAuthorService,
            $this->bookSubjectService,
        );
    }
}
