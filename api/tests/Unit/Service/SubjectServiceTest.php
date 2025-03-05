<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\Filter\SubjectFilterDto;
use App\Entity\Book;
use App\Entity\BookSubject;
use App\Entity\Subject;
use App\Repository\BookSubjectRepository;
use App\Repository\SubjectRepository;
use App\Service\SubjectService;
use App\ValueObject\SubjectCreateValueObject;
use App\ValueObject\SubjectUpdateValueObject;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SubjectServiceTest extends TestCase
{
    private SubjectService $subjectService;

    private MockObject|EntityManagerInterface $entityManager;

    private MockObject|SubjectRepository $subjectRepository;

    public function testList(): void
    {
        $subjects = [
            ['id' => 1, 'name' => 'Drama'],
            ['id' => 2, 'name' => 'Suspense'],
            ['id' => 3, 'name' => 'Programação'],
            ['id' => 4, 'name' => 'Terror'],
        ];

        $dto = new SubjectFilterDto();
        $this->subjectRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn($subjects);

        $list = $this->subjectService->list($dto);
        self::assertSame($subjects, $list);
        self::assertCount(4, $list);
    }

    public function testListWhenHasNoResult(): void
    {
        $dto = new SubjectFilterDto();
        $this->subjectRepository->expects(self::once())
            ->method('findByFilterDto')
            ->with($dto)
            ->willReturn([]);

        $list = $this->subjectService->list($dto);
        self::assertSame([], $list);
        self::assertCount(0, $list);
    }

    public function testCreateWhenDescriptionIsNotProvided(): void
    {
        $valueObject = new SubjectCreateValueObject();

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Descrição não pode ser vazia.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->subjectService->create($valueObject);
    }

    public function testCreateWhenDescriptionExceeds20Characters(): void
    {
        $valueObject = new SubjectCreateValueObject(
            'Lorem ipsum dolor sit amet'
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Descrição não pode ultrapassar 20 caracteres.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->subjectService->create($valueObject);
    }

    public function testCreateWhenAlreadyExistsWithDescription(): void
    {
        $valueObject = new SubjectCreateValueObject('Suspense');

        $alreadySubject = (new Subject())
            ->setId(1)
            ->setDescription('Suspense');

        $this->subjectRepository->expects(self::once())
            ->method('findOneBy')
            ->with(
                ['description' => 'Suspense']
            )
            ->willReturn($alreadySubject);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Já existe um assunto Suspense criado no sistema');

        $this->subjectService->create($valueObject);
    }

    public function testCreate(): void
    {
        $valueObject = new SubjectCreateValueObject('Suspense');

        $subject = (new Subject())
            ->setDescription($valueObject->getDescription());

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($subject);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->subjectService->create($valueObject);
    }

    public function testUpdateWhenDescriptionIsNotProvided(): void
    {
        $valueObject = new SubjectUpdateValueObject(
            1,
            null
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Descrição não pode ser vazia.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->subjectService->update($valueObject);
    }

    public function testUpdateWhenDescriptionExceeds20Characters(): void
    {
        $valueObject = new SubjectUpdateValueObject(
            1,
            'Lorem ipsum dolor sit amet'
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Descrição não pode ultrapassar 20 caracteres.');

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        $this->subjectService->update($valueObject);
    }

    public function testUpdateWhenSubjectNotFound(): void
    {
        $valueObject = new SubjectUpdateValueObject(
            5,
            'Suspense'
        );

        $this->subjectRepository->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Assunto não encontrado com ID 5');

        $this->subjectService->update($valueObject);
    }

    public function testUpdateWhenAlreadyExistsWithDescription(): void
    {
        $valueObject = new SubjectUpdateValueObject(
            1,
            'Suspense'
        );

        $alreadySubject = (new Subject())
            ->setId(2)
            ->setDescription('Suspense');

        $this->subjectRepository->expects(self::once())
            ->method('findOneBy')
            ->with(
                ['description' => 'Suspense']
            )
            ->willReturn($alreadySubject);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Já existe um assunto Suspense criado no sistema');

        $this->subjectService->update($valueObject);
    }

    public function testUpdate(): void
    {
        $valueObject = new SubjectUpdateValueObject(
            1,
            'Suspense'
        );

        $subject = (new Subject())
            ->setId($valueObject->getId())
            ->setDescription('Terror');

        $this->subjectRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($subject);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->subjectService->update($valueObject);
    }

    public function testDeleteWhenSubjectNotFound(): void
    {
        $this->subjectRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method(self::anything());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Assunto não encontrado com ID 1');

        $this->subjectService->delete(1);
    }

    public function testDeleteWhenSubjectIsLinkedToBook(): void
    {
        $subject = (new Subject())
            ->setId(1)
            ->setDescription('Terror');

        $this->subjectRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($subject);

        $bookSubjectRepository = $this->createMock(BookSubjectRepository::class);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(BookSubject::class)
            ->willReturn($bookSubjectRepository);

        $book = (new Book())
            ->setId(rand(1, 10));

        $bookSubject = (new BookSubject())
            ->setBook($book)
            ->setSubject($subject);

        $bookSubjectRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['subject' => 1]
            )
            ->willReturn([$bookSubject]);

        $this->entityManager->expects(self::never())
            ->method('remove');

        $this->entityManager->expects(self::never())
            ->method('flush');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Existem livros vinculados ao assunto. Remova os vínculos primeiro.');

        $this->subjectService->delete(1);
    }

    public function testDelete(): void
    {
        $subject = (new Subject())
            ->setId(1)
            ->setDescription('Terror');

        $this->subjectRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($subject);

        $bookSubjectRepository = $this->createMock(BookSubjectRepository::class);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(BookSubject::class)
            ->willReturn($bookSubjectRepository);

        $bookSubjectRepository->expects(self::once())
            ->method('findBy')
            ->with(
                ['subject' => 1]
            )
            ->willReturn([]);

        $this->entityManager->expects(self::once())
            ->method('remove')
            ->with($subject);

        $this->entityManager->expects(self::once())
            ->method('flush');

        $this->subjectService->delete(1);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->subjectRepository = $this->createMock(SubjectRepository::class);

        $this->subjectService = new SubjectService($this->entityManager, $this->subjectRepository);
    }
}
