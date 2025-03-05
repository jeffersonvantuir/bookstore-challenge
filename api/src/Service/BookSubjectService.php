<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookSubject;
use App\Repository\BookSubjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookSubjectService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookSubjectRepository $bookSubjectRepository,
        private SubjectService $subjectService,
    ) {
    }

    /**
     * @return array<array<string, int|string>>
     */
    public function list(int $bookId): array
    {
        $list = $this->bookSubjectRepository->findBy(
            ['book' => $bookId],
        );

        if (empty($list)) {
            return [];
        }

        return array_map(function (BookSubject $bookSubject) {
            $subject = $bookSubject->getSubject();

            return [
                'id' => $subject->getId(),
                'description' => $subject->getDescription(),
            ];
        }, $list);
    }

    /**
     * @param int[] $subjectsIds
     */
    public function linkBookToSubjects(Book $book, array $subjectsIds = []): void
    {
        $this->bookSubjectRepository->removeLinkNotInSubjectsIds($book->getId(), $subjectsIds);

        if (empty($subjectsIds)) {
            return;
        }

        foreach ($subjectsIds as $subjectId) {
            $bookSubject = $this->bookSubjectRepository->findOneBy([
                'book' => $book,
                'subject' => $subjectId
            ]);

            if (null !== $bookSubject) {
                continue;
            }

            $subject = $this->subjectService->getEntity($subjectId);

            $bookSubject = (new BookSubject())
                ->setBook($book)
                ->setSubject($subject);

            $this->entityManager->persist($bookSubject);
        }

        $this->entityManager->flush();
    }
}
