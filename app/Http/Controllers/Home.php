<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

class Home extends Controller
{
    public function uploadForm()
    {
        return view('upload');
    }

    public function processUpload(Request $request)
    {
        // Validasi file
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        $path    = $request->file('pdf')->store('pdfs');
        $pdfPath = storage_path('app/private/' . $path);

        // Buat FPDI instance
        $pdf       = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $size   = $pdf->getTemplateSize($tplIdx);

            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx);

            // Footer
            $marginBottom = 8;
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor(100, 100, 100);

            $lineY  = $size['height'] - ($marginBottom + 4);
            $lineYs = $size['height'] - ($marginBottom + 3);

            $pdf->SetDrawColor(150, 150, 150);
            $pdf->SetLineWidth(0.8);
            $pdf->Line(10, $lineY, $size['width'] - 10, $lineY);

            $pdf->SetDrawColor(150, 150, 150);
            $pdf->SetLineWidth(0.2);
            $pdf->Line(10, $lineYs, $size['width'] - 10, $lineYs);

            $text = "Footer halaman {$pageNo}";
            $pdf->Text(10, $lineYs + 4, $text);
            $rightText = "Footer Data";
            $textWidth = $pdf->GetStringWidth($rightText);
            $xRight    = $size['width'] - $textWidth - 10;
            $pdf->Text($xRight, $lineYs + 4, $rightText);
        }

        // Tambah halaman baru di akhir
        $pdf->AddPage();
        $size = [
            'width'  => $pdf->GetPageWidth(),
            'height' => $pdf->GetPageHeight(),
        ];
        // Footer
        $marginBottom = 8;
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);

        $lineY  = $size['height'] - ($marginBottom + 4);
        $lineYs = $size['height'] - ($marginBottom + 3);

        $pdf->SetDrawColor(150, 150, 150);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, $lineY, $size['width'] - 10, $lineY);

        $pdf->SetDrawColor(150, 150, 150);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, $lineYs, $size['width'] - 10, $lineYs);

        $text = "Footer halaman {$pageNo}";
        $pdf->Text(10, $lineYs + 4, $text);
        $rightText = "Footer Data";
        $textWidth = $pdf->GetStringWidth($rightText);
        $xRight    = $size['width'] - $textWidth - 10;
        $pdf->Text($xRight, $lineYs + 4, $rightText);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Halaman Tambahan', 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(0, 10, 'Ini adalah halaman tambahan yang disisipkan di akhir PDF.', 0, 'L');

        // Simpan hasil PDF
        $outputPath = storage_path('app/public/output.pdf');
        $pdf->Output($outputPath, 'F');

        // return response()->download($outputPath)->deleteFileAfterSend(true);

        $pdfContent = $pdf->Output('S'); // Simpan PDF di memory
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="output.pdf"')
            ->header('Content-Length', strlen($pdfContent));

    }
}
