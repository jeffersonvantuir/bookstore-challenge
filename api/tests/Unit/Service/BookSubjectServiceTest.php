<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Book;
use App\Entity\BookSubject;
use App\Entity\Subject;
use App\Repository\BookSubjectRepository;
use App\Service\BookSubjectService;
use App\Service\SubjectService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookSubjectServiceTest extends TestCase
{
    private BookSubjectService $bookSubjectService;

    private MockObject|EntityManagerInterface $entityManager;

    private MockObject|BookSubjectRepository $bookSubjectRepository;

    private MockObject|SubjectService $subjectService;

    public function testListWhenNotFound(): void
    {
        $bookId = 1;

        $this->bookSubjectRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['book' => $bookId],
            )
            ->willReturn([]);

        $return = $this->bookSubjectService->list($bookId);

        self::assertSame([], $return);
    }

    public function testList(): void
    {
        $bookId = 1;
        $subjectId = 3;

        $book = (new Book())
            ->setId($bookId);

        $subject = (new Subject())
            ->setId($subjectId)
            ->setDescription('Terror');

        $bookSubject = (new BookSubject())
            ->setBook($book)
            ->setSubject($subject);

        $this->bookSubjectRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['book' => $bookId],
            )
            ->willReturn([$bookSubject]);

        $return = $this->bookSubjectService->list($bookId);

        $expected = [
            [
                'id' => 3,
                'description' => 'Terror',
            ]
        ];

        self::assertSame($expected, $return);
    }

    public function testLinkBookToSubjectsWhenSubjectsIdsIsNotProvided(): void
    {
        $bookId = 1;
        $subjectsIds = [];

        $book = (new Book())
            ->setId($bookId);

        $this->bookSubjectRepository->expects(self::once())
            ->method('removeLinkNotInSubjectsIds')
            ->with(1, $subjectsIds);

        $this->bookSubjectRepository->expects(self::never())
            ->method('findOneBy');

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        $this->bookSubjectService->linkBookToSubjects($book, $subjectsIds);
    }

    public function testLinkBookToSubjectsWhenAllLinked(): void
    {
        $bookId = 1;
        $subjectsIds = [5, 6];

        $book = (new Book())
            ->setId($bookId);

        $this->bookSubjectRepository->expects(self::once())
            ->method('removeLinkNotInSubjectsIds')
            ->with(1, $subjectsIds);

        $subject1 = (new Subject())
            ->setId($subjectsIds[0]);

        $subject2 = (new Subject())
            ->setId($subjectsIds[1]);

        $bookSubject1 = (new BookSubject())
            ->SetBook($book)
            ->setSubject($subject1);

        $bookSubject2 = (new BookSubject())
            ->SetBook($book)
            ->setSubject($subject2);

        $this->bookSubjectRepository->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function ($index) use ($book, $subjectsIds) {
                return [
                    'book' => $book,
                    'subject' => $subjectsIds[$index],
                ];
            })
            ->willReturn(
                $bookSubject1,
                $bookSubject2,
            );

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookSubjectService->linkBookToSubjects($book, $subjectsIds);
    }

    public function testLinkBookToSubjects(): void
    {
        $bookId = 1;
        $subjectsIds = [5, 6];

        $book = (new Book())
            ->setId($bookId);

        $this->bookSubjectRepository->expects(self::once())
            ->method('removeLinkNotInSubjectsIds')
            ->with(1, $subjectsIds);

        $subject1 = (new Subject())
            ->setId($subjectsIds[0]);

        $subject2 = (new Subject())
            ->setId($subjectsIds[1]);

        $bookSubject1 = (new BookSubject())
            ->SetBook($book)
            ->setSubject($subject1);

        $this->bookSubjectRepository->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function ($index) use ($book, $subjectsIds) {
                return [
                    'book' => $book,
                    'subject' => $subjectsIds[$index],
                ];
            })
            ->willReturn(
                $bookSubject1,
                null,
            );

        $this->subjectService->expects(self::once())
            ->method('getEntity')
            ->with(6)
            ->willReturn($subject2);

        $bookSubject = (new BookSubject())
            ->setBook($book)
            ->setSubject($subject2);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($bookSubject);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->bookSubjectService->linkBookToSubjects($book, $subjectsIds);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bookSubjectRepository = $this->createMock(BookSubjectRepository::class);
        $this->subjectService = $this->createMock(SubjectService::class);

        $this->bookSubjectService = new BookSubjectService(
            $this->entityManager,
            $this->bookSubjectRepository,
            $this->subjectService
        );
    }
}
