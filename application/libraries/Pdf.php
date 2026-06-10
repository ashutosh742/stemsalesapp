<?php
require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class Pdf {

    public function createPDF($html, $outputPath = '', $paper = 'A4', $orientation = 'portrait') {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        if ($outputPath) {
            file_put_contents($outputPath, $dompdf->output());
        } else {
            $dompdf->stream("document.pdf", array("Attachment" => 1));
        }
    }
}
