<?php
namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Field;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;
use setasign\Fpdi\Fpdi;

class Home extends Controller
{
    public function index()
    {
        $doc = Doc::with('users')->get();
        return view('doc.document', compact('doc'));
    }

    public function create()
    {
        return view('doc.form');
    }

    public function store(Request $request)
    {
        // Validasi file
        $request->validate([
            "doc" => "required",
            'pdf' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        // dd($request->input());
        $path = $request->file('pdf')->store('pdfs', 'public');

        $doc            = new Doc;
        $doc->name      = $request->doc;
        $doc->file_path = $path;
        $doc->save();

        $users = $request->name;
        $note  = $request->note;

        for ($i = 0; $i < count($users); $i++) {
            $field         = new Field;
            $field->doc_id = $doc->id;
            $field->name   = $users[$i];
            $field->value  = $note[$i];
            $field->save();
        }

        return back();

    }

    public function storeLink(Request $request, $id)
    {
        $limit     = Carbon::now()->addHour()->timestamp;
        $doc       = Doc::findOrFail($id);
        $doc->link = $limit;
        $doc->save();

        return back();
    }

    public function preview($id)
    {
        $doc     = Doc::where(DB::raw('md5(id)'), $id)->firstOrFail();
        $pdfPath = storage_path('app/public/' . $doc->file_path);

        // === 1. Render halaman tambahan dari Blade pakai Barryvdh ===
        $extraPdfPath = storage_path('app/public/temp_extra.pdf');
        $extraPdf     = PDF::loadView('user', [
            'title' => 'Daftar Participant',
            'items' => $doc->users,
        ])->setPaper('a4', 'portrait');

        // return $extraPdf->stream('laporan.pdf');
        $extraPdf->save($extraPdfPath);

        // === 2. Gabungkan PDF asli + halaman tambahan dengan FPDI ===
        $pdf = new Fpdi();

        // --- PDF asli ---
        $pageCount = $pdf->setSourceFile($pdfPath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx      = $pdf->importPage($pageNo);
            $size        = $pdf->getTemplateSize($tplIdx);
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

            $text = "Halaman {$pageNo}";
            $pdf->Text(10, $lineYs + 4, $text);
            $rightText = $doc->name;
            $textWidth = $pdf->GetStringWidth($rightText);
            $xRight    = $size['width'] - $textWidth - 10;
            $pdf->Text($xRight, $lineYs + 4, $rightText);
        }

        // --- Halaman tambahan (hasil render Blade) ---
        $extraPageCount = $pdf->setSourceFile($extraPdfPath);
        for ($pageNo = 1; $pageNo <= $extraPageCount; $pageNo++) {
            $tplIdx      = $pdf->importPage($pageNo);
            $size        = $pdf->getTemplateSize($tplIdx);
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx);

            // Footer halaman tambahan
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

            $text = "Halaman " . ($pageCount + $pageNo);
            $pdf->Text(10, $lineYs + 4, $text);
            $rightText = $doc->name;
            $textWidth = $pdf->GetStringWidth($rightText);
            $xRight    = $size['width'] - $textWidth - 10;
            $pdf->Text($xRight, $lineYs + 4, $rightText);
        }

        // === 3. Output PDF gabungan ===
        $outputPath = storage_path('app/public/output.pdf');
        // $pdf->Output($outputPath, 'F');

        $pdfContent = $pdf->Output('S');
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="output.pdf"')
            ->header('Content-Length', strlen($pdfContent));
    }

    // public function preview($id)
    // {
    //     $doc = Doc::where(DB::raw('md5(id)'), $id)->firstOrFail();

    //     $pdfPath = storage_path('app/public/' . $doc->file_path);

    //     // Buat FPDI instance
    //     $pdf       = new Fpdi();
    //     $pageCount = $pdf->setSourceFile($pdfPath);

    //     for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //         $tplIdx = $pdf->importPage($pageNo);
    //         $size   = $pdf->getTemplateSize($tplIdx);

    //         $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
    //         $pdf->AddPage($orientation, [$size['width'], $size['height']]);
    //         $pdf->useTemplate($tplIdx);

    //         // Footer
    //         $marginBottom = 8;
    //         $pdf->SetFont('Helvetica', '', 8);
    //         $pdf->SetTextColor(100, 100, 100);

    //         $lineY  = $size['height'] - ($marginBottom + 4);
    //         $lineYs = $size['height'] - ($marginBottom + 3);

    //         $pdf->SetDrawColor(150, 150, 150);
    //         $pdf->SetLineWidth(0.8);
    //         $pdf->Line(10, $lineY, $size['width'] - 10, $lineY);

    //         $pdf->SetDrawColor(150, 150, 150);
    //         $pdf->SetLineWidth(0.2);
    //         $pdf->Line(10, $lineYs, $size['width'] - 10, $lineYs);

    //         $text = "Halaman {$pageNo}";
    //         $pdf->Text(10, $lineYs + 4, $text);
    //         $rightText = $doc->name;
    //         $textWidth = $pdf->GetStringWidth($rightText);
    //         $xRight    = $size['width'] - $textWidth - 10;
    //         $pdf->Text($xRight, $lineYs + 4, $rightText);
    //     }

    //     // Tambah halaman baru di akhir
    //     $pdf->AddPage();
    //     $size = [
    //         'width'  => $pdf->GetPageWidth(),
    //         'height' => $pdf->GetPageHeight(),
    //     ];
    //     // Footer
    //     $marginBottom = 8;
    //     $pdf->SetFont('Helvetica', '', 8);
    //     $pdf->SetTextColor(100, 100, 100);

    //     $lineY  = $size['height'] - ($marginBottom + 4);
    //     $lineYs = $size['height'] - ($marginBottom + 3);

    //     $pdf->SetDrawColor(150, 150, 150);
    //     $pdf->SetLineWidth(0.8);
    //     $pdf->Line(10, $lineY, $size['width'] - 10, $lineY);

    //     $pdf->SetDrawColor(150, 150, 150);
    //     $pdf->SetLineWidth(0.2);
    //     $pdf->Line(10, $lineYs, $size['width'] - 10, $lineYs);

    //     $text = "Halaman {$pageNo}";
    //     $pdf->Text(10, $lineYs + 4, $text);
    //     $rightText = $doc->name;
    //     $textWidth = $pdf->GetStringWidth($rightText);
    //     $xRight    = $size['width'] - $textWidth - 10;
    //     $pdf->Text($xRight, $lineYs + 4, $rightText);
    //     $pdf->SetFont('Helvetica', 'B', 16);
    //     $pdf->Cell(0, 10, 'Halaman Tambahan', 0, 1, 'C');
    //     $pdf->SetFont('Helvetica', '', 12);
    //     $pdf->MultiCell(0, 10, 'Ini adalah halaman tambahan yang disisipkan di akhir PDF.', 0, 'L');

    //     // Simpan hasil PDF
    //     $outputPath = storage_path('app/public/output.pdf');
    //     $pdf->Output($outputPath, 'F');

    //     // return response()->download($outputPath)->deleteFileAfterSend(true);

    //     $pdfContent = $pdf->Output('S'); // Simpan PDF di memory
    //     return response($pdfContent)
    //         ->header('Content-Type', 'application/pdf')
    //         ->header('Content-Disposition', 'inline; filename="output.pdf"')
    //         ->header('Content-Length', strlen($pdfContent));

    // }

    public function showLink($id)
    {
        $doc = Doc::where(DB::raw('md5(link)'), $id)->first();
        return view('ttd', compact('doc'));
    }

    public function signLink(Request $request, $id)
    {
        $request->validate([
            'data_url' => 'required|string',
            'user'     => 'required|string',
        ]);

        $doc = Doc::where(DB::raw('md5(link)'), $id)->firstOrFail();

        $dataUrl = $request->input('data_url');

        // format: data:image/png;base64,....
        if (! preg_match('/^data:image\/png;base64,/', $dataUrl)) {
            return response()->json(['message' => 'Invalid data URL'], 422);
        }

        $base64  = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $decoded = base64_decode($base64);

        if ($decoded === false) {
            return response()->json(['message' => 'Decoding failed'], 422);
        }

        $filename = 'signatures/' . Str::random(40) . '.png';
        Storage::disk('public')->put($filename, $decoded);

        $user      = Field::where('id', $request->user)->firstOrFail();
        $user->ttd = $filename;
        $user->save();

        return response()->json([
            'message' => 'Saved',
            'id'      => $user->id,
            'url'     => Storage::disk('public')->url($filename),
        ]);
    }
}
