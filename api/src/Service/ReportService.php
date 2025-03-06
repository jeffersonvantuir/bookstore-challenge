<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ViewReportAuthor;
use App\Repository\ViewReportAuthorRepository;
use Twig\Environment;

class ReportService
{
    public function __construct(
        private ViewReportAuthorRepository $reportRepository,
        private Environment $twig,
    ) {
    }

    public function byAuthors(): string
    {
        $authors = $this->reportRepository->findAll();

        if (empty($authors)) {
            throw new \DomainException('Nenhum dado encontrado para gerar o relatório.');
        }

        $structuredData = [];
        foreach ($authors as $author) {
            if (empty($structuredData[$author->getAuthorId()])) {
                $structuredData[$author->getAuthorId()] = [
                    'authorId' => $author->getAuthorId(),
                    'authorName' => $author->getAuthorName(),
                    'books' => []
                ];
            }

            $bookData = $this->getBookData($author);
            if (false === empty($bookData)) {
                $structuredData[$author->getAuthorId()]['books'][] = $this->getBookData($author);
            }
        }

        return $this->twig->render(
            'report/authors.html.twig',
            [
                'authors' => $structuredData,
            ]
        );
    }

    private function getBookData(ViewReportAuthor $reportAuthor): array
    {
        if (null === $reportAuthor->getBookId()) {
            return [];
        }

        return [
            'bookId' => $reportAuthor->getBookId(),
            'bookTitle' => $reportAuthor->getBookTitle(),
            'publisher' => $reportAuthor->getPublisher(),
            'edition' => $reportAuthor->getEdition(),
            'publicationYear' => $reportAuthor->getPublicationYear(),
            'amount' => 'R$ ' . number_format($reportAuthor->getAmount() ?? 0, 2, ',', ''),
            'subjects' => $reportAuthor->getSubjects() ? explode(',', $reportAuthor->getSubjects()) : [],
        ];
    }
}