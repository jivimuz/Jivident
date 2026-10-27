<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

abstract class Controller
{
    /**
     * Render a print-style Blade view either as a normal HTML page (for browser
     * print) or, when the request asks for `?pdf=1`, as a downloadable PDF.
     * Keeps every print view usable both ways without duplicating templates.
     */
    protected function viewOrPdf(string $view, array $data, string $pdfFilename): Response
    {
        if (! request()->boolean('pdf')) {
            return response(view($view, $data));
        }

        return Pdf::loadView($view, $data)->download($pdfFilename);
    }
}
