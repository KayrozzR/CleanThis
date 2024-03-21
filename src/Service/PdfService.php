<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Operation;
use App\Entity\Devis;
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
        $this->domPdf->setPaper("a4", "landscape");
        $this->domPdf->setOptions($pdfOptions);
    }

    public function generateInvoice(Devis $operation): string
    {
        $logoPath = $this->projectDir . '/public/images/logo.png';

        if (!file_exists($logoPath)) {
            throw new \Exception('Le fichier logo n\'existe pas.');
        }

        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        $html = $this->twig->render('invoice/invoice_template.html.twig', [
            'operation' => $operation,
            'logo_base64' => $logoBase64,
        ]);

        return $html;
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
        // $dompdf->output();
        return $dompdf->output();
    }




}