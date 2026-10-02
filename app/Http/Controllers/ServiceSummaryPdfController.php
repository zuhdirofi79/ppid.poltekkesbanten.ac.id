<?php

namespace App\Http\Controllers;

use App\Support\ServiceSummaryPdf;
use App\Support\ServiceSummaryReport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceSummaryPdfController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $report = ServiceSummaryReport::fromRequest($request);
        $year = $report['selectedYear'] ?: 'tanpa-tahun';
        $filename = 'ringkasan-layanan-informasi-PPID-Poltekkes-Banten-'.$year.'.pdf';

        return response(ServiceSummaryPdf::render($report), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
