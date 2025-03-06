<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Filter\BookFilterDto;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\ValueObject\BookCreateValueObject;
use App\ValueObject\BookUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;

readonly class BookService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookRepository $bookRepository,
        private BookAuthorService $bookAuthorService,
        private BookSubjectService $bookSubjectService,
    ) {
    }

    /**
     * @return array<array<string, array<array<string, int|string>>|string>>
     */
    public function list(BookFilterDto $filterDto): array
    {
        $books = $this->bookRepository
            ->findByFilterDto($filterDto);

        if (empty($books)) {
            return [];
        }

        $returnData = [];

        foreach ($books as $book) {
            $book['subjects'] = $this->bookSubjectService->list((int) $book['id']);
            $book['authors'] = $this->bookAuthorService->list((int) $book['id']);
            $returnData[] = $book;
        }

        return $returnData;
    }

    public function create(BookCreateValueObject $createValueObject): void
    {
        $createValueObject->validate();

        $book = (new Book())
            ->setTitle($createValueObject->getTitle())
            ->setAmount($createValueObject->getAmount())
            ->setPublisher($createValueObject->getPublisher())
            ->setEdition($createValueObject->getEdition())
            ->setPublicationYear($createValueObject->getPublicationYear());

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->bookSubjectService->linkBookToSubjects($book, $createValueObject->getSubjectsIds());
        $this->bookAuthorService->linkBookToAuthors($book, $createValueObject->getAuthorsIds());
    }

    public function update(BookUpdateValueObject $updateValueObject): void
    {
        $updateValueObject->validate();

        $book = $this->getEntity($updateValueObject->getId());
        $book
            ->setTitle($updateValueObject->getTitle())
            ->setAmount($updateValueObject->getAmount())
            ->setPublisher($updateValueObject->getPublisher())
            ->setEdition($updateValueObject->getEdition())
            ->setPublicationYear($updateValueObject->getPublicationYear());

        $this->bookSubjectService->linkBookToSubjects($book, $updateValueObject->getSubjectsIds());
        $this->bookAuthorService->linkBookToAuthors($book, $updateValueObject->getAuthorsIds());

        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $book = $this->getEntity($id);

        $this->bookSubjectService->linkBookToSubjects($book);
        $this->bookAuthorService->linkBookToAuthors($book);

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int $id): array
    {
        $book = $this->getEntity($id);

        $authors = $this->bookAuthorService->list($book->getId());
        $subjects = $this->bookSubjectService->list($book->getId());

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'amount' => $book->getAmount(),
            'publisher' => $book->getPublisher(),
            'edition' => $book->getEdition(),
            'publicationYear' => $book->getPublicationYear(),
            'authors' => $authors,
            'subjects' => $subjects,
        ];
    }

    private function getEntity(int $id): Book
    {
        $book = $this->bookRepository->find($id);

        if (null === $book) {
            throw new \DomainException(sprintf('Livro não encontrado com ID %d', $id));
        }

        return $book;
    }
}
