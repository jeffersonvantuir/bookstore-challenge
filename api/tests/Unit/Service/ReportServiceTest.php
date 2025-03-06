<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ViewReportAuthor;
use App\Repository\ViewReportAuthorRepository;
use App\Service\ReportService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class ReportServiceTest extends TestCase
{
    private MockObject|ViewReportAuthorRepository $reportAuthorRepository;

    private MockObject|Environment $twig;

    private ReportService $reportService;

    public function testReportByAuthorWhenEmptyData(): void
    {
        $this->reportAuthorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Nenhum dado encontrado para gerar o relatório.');

        $this->twig->expects(self::never())
            ->method(self::anything());

        $this->reportService->byAuthors();
    }

    public function testReportWhenBookDataIsEmpty(): void
    {
        $viewReport1 = (new ViewReportAuthor())
            ->setId(1)
            ->setAuthorId(1)
            ->setAuthorName('Jefferson')
            ->setPublisher('Editora')
            ->setEdition(1)
            ->setPublicationYear('2024')
            ->setAmount(0)
            ->setBookTitle('Três porquinhos')
            ->setBookId(null)
            ->setSubjects(null);

        $returnAuthors = [
            $viewReport1,
        ];

        $this->reportAuthorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($returnAuthors);

        $expected = [
            1 => [
                'authorId' => 1,
                'authorName' => 'Jefferson',
                'books' => []
            ],
        ];

        $this->twig->expects(self::once())
            ->method('render')
            ->with(
                'report/authors.html.twig',
                [
                    'authors' => $expected,
                ]
            );

        $this->reportService->byAuthors();
    }

    public function testReportByAuthor(): void
    {
        $viewReport1 = (new ViewReportAuthor())
            ->setId(1)
            ->setAuthorId(1)
            ->setAuthorName('Jefferson')
            ->setPublisher('Editora')
            ->setEdition(1)
            ->setPublicationYear('2024')
            ->setAmount(0)
            ->setBookTitle('Três porquinhos')
            ->setBookId(50)
            ->setSubjects(null);

        $viewReport2 = (new ViewReportAuthor())
            ->setId(2)
            ->setAuthorId(1)
            ->setAuthorName('Jefferson')
            ->setPublisher('ABC Editora')
            ->setEdition(2)
            ->setPublicationYear('2020')
            ->setAmount(22.60)
            ->setBookTitle('João e Maria')
            ->setBookId(51)
            ->setSubjects('Suspense,Terror');

        $viewReport3 = (new ViewReportAuthor())
            ->setId(3)
            ->setAuthorId(3)
            ->setAuthorName('Maiara')
            ->setPublisher('ABC Editora')
            ->setEdition(2)
            ->setPublicationYear('2020')
            ->setAmount(22.60)
            ->setBookTitle('João e Maria')
            ->setBookId(51)
            ->setSubjects('Suspense,Terror');

        $returnAuthors = [
            $viewReport1,
            $viewReport2,
            $viewReport3,
        ];

        $this->reportAuthorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($returnAuthors);

        $expected = [
            1 => [
                'authorId' => 1,
                'authorName' => 'Jefferson',
                'books' => [
                    [
                        'bookId' => 50,
                        'bookTitle' => 'Três porquinhos',
                        'publisher' => 'Editora',
                        'edition' => 1,
                        'publicationYear' => '2024',
                        'amount' => 'R$ 0,00',
                        'subjects' => []
                    ],
                    [
                        'bookId' => 51,
                        'bookTitle' => 'João e Maria',
                        'publisher' => 'ABC Editora',
                        'edition' => 2,
                        'publicationYear' => '2020',
                        'amount' => 'R$ 22,60',
                        'subjects' => [
                            'Suspense',
                            'Terror'
                        ]
                    ]
                ]
            ],
            3 => [
                'authorId' => 3,
                'authorName' => 'Maiara',
                'books' => [
                    [
                        'bookId' => 51,
                        'bookTitle' => 'João e Maria',
                        'publisher' => 'ABC Editora',
                        'edition' => 2,
                        'publicationYear' => '2020',
                        'amount' => 'R$ 22,60',
                        'subjects' => [
                            'Suspense',
                            'Terror'
                        ]
                    ]
                ]
            ]
        ];

        $this->twig->expects(self::once())
            ->method('render')
            ->with(
                'report/authors.html.twig',
                [
                    'authors' => $expected,
                ]
            );

        $this->reportService->byAuthors();
    }

    protected function setUp(): void
    {
        $this->reportAuthorRepository = $this->createMock(ViewReportAuthorRepository::class);
        $this->twig = $this->createMock(Environment::class);

        $this->reportService = new ReportService(
            $this->reportAuthorRepository,
            $this->twig,
        );
    }
}
