<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Filter\SubjectFilterDto;
use App\Entity\BookSubject;
use App\Entity\Subject;
use App\Repository\SubjectRepository;
use App\ValueObject\SubjectCreateValueObject;
use App\ValueObject\SubjectUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;

readonly class SubjectService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubjectRepository $subjectRepository,
    ) {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function list(SubjectFilterDto $filterDto): array
    {
        return $this->subjectRepository
            ->findByFilterDto($filterDto);
    }

    public function create(SubjectCreateValueObject $createValueObject): void
    {
        $createValueObject->validate();

        $alreadyExists = $this->getSubjectByDescription($createValueObject->getDescription());

        if (null !== $alreadyExists) {
            throw new \DomainException(
                sprintf('Já existe um assunto %s criado no sistema', $createValueObject->getDescription())
            );
        }

        $subject = (new Subject())
            ->setDescription($createValueObject->getDescription());

        $this->entityManager->persist($subject);
        $this->entityManager->flush();
    }

    public function update(SubjectUpdateValueObject $updateValueObject): void
    {
        $updateValueObject->validate();
        $alreadyExists = $this->getSubjectByDescription($updateValueObject->getDescription());

        if (null !== $alreadyExists && $alreadyExists->getId() !== $updateValueObject->getId()) {
            throw new \DomainException(
                sprintf('Já existe um assunto %s criado no sistema', $alreadyExists->getDescription())
            );
        }

        $subject = $this->getEntity($updateValueObject->getId());
        $subject->setDescription($updateValueObject->getDescription());
        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $subject = $this->getEntity($id);

        $linkedBooks = $this->entityManager->getRepository(BookSubject::class)->findBy(
            ['subject' => $id]
        );

        if (false === empty($linkedBooks)) {
            throw new \DomainException('Existem livros vinculados ao assunto. Remova os vínculos primeiro.');
        }

        $this->entityManager->remove($subject);
        $this->entityManager->flush();
    }

    public function getEntity(int $id): Subject
    {
        $subject = $this->subjectRepository->find($id);
        if (null === $subject) {
            throw new \DomainException(sprintf('Assunto não encontrado com ID %d', $id));
        }

        return $subject;
    }

    private function getSubjectByDescription(string $description): ?Subject
    {
        return $this->subjectRepository->findOneBy(
            ['description' => $description],
        );
    }
}
