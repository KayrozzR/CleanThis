<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private $domPdf;
    private Environment $twig;
    private string $projectDir;

    public function __construct(){
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions ->set('defaultFont', 'Inter');
        // $pdfOptions ->set("chroot", realpath(''));
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
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }
}