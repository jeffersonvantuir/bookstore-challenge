<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ReportService;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/report', name:'api_report')]
class ReportController extends AbstractController
{
    public function __construct(
        private ReportService $reportService,
    ) {
    }

    #[Route('/author', name: '_author', methods: [Request::METHOD_GET])]
    public function author(
        Pdf $pdf,
    ): Response {
        try {
            $html = $this->reportService->byAuthors();

            return new PdfResponse(
                $pdf->getOutputFromHtml($html),
                sprintf('report_author_%s.pdf', date('YmdHis'))
            );
        } catch (\DomainException $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $throwable) {
            return $this->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
