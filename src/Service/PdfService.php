<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf;

    public function __construct(){
        $this->domPdf = new Dompdf();

        $pdfOptions = new Options();
        $pdfOptions ->set('defaultFont', 'Inter');
        $this->domPdf->setPaper("a4", "portrait");
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html) {
        $filename = "details.pdf";
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream($filename , [
            'Attachement' => false
        ]);
    }
    
    public function generateBinaryPDF($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}