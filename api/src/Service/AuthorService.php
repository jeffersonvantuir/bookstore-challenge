<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Filter\AuthorFilterDto;
use App\Entity\Author;
use App\Entity\BookAuthor;
use App\ValueObject\AuthorCreateValueObject;
use App\ValueObject\AuthorUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;

readonly class AuthorService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function list(AuthorFilterDto $filterDto): array
    {
        return $this->entityManager
            ->getRepository(Author::class)
            ->findByFilterDto($filterDto);
    }

    public function create(AuthorCreateValueObject $createValueObject): void
    {
        $createValueObject->validate();

        $author = (new Author())
            ->setName($createValueObject->getName());

        $this->entityManager->persist($author);
        $this->entityManager->flush();
    }

    public function update(AuthorUpdateValueObject $updateValueObject): void
    {
        $updateValueObject->validate();

        $author = $this->getEntity($updateValueObject->getId());
        $author->setName($updateValueObject->getName());

        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $author = $this->getEntity($id);

        $linkedBooks = $this->entityManager->getRepository(BookAuthor::class)->findBy(
            ['author' => $id]
        );

        if (false === empty($linkedBooks)) {
            throw new \DomainException('Existem livros vinculados ao autor. Remova os vínculos primeiro.');
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }

    private function getEntity(int $id): Author
    {
        $author = $this->entityManager->getRepository(Author::class)->find($id);
        if (null === $author) {
            throw new \DomainException(sprintf('Autor não encontrado com ID %d', $id));
        }

        return $author;
    }
}
