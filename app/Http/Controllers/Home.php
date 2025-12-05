<?php
namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Field;
use App\Models\Paraf;
use App\Rules\KodeLink;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class Home extends Controller
{
    public function index()
    {
        $action = "DATA";
        $doc    = Doc::with('users')->get();
        return view('doc.document', compact('doc', 'action'));
    }

    public function create()
    {
        $action = "Tambah Data";
        return view('doc.form', compact('action'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            'doc'  => 'required|mimes:docx|max:2048',
        ]);

        $path = $request->file('doc')->store('docs', 'public');

        $doc            = new Doc;
        $doc->nomor     = $request->nomor;
        $doc->tanggal   = $request->tanggal;
        $doc->as_head   = $request->as_head;
        $doc->name_head = $request->name_head;
        $doc->as_name   = $request->as_name;
        $doc->nip       = $request->nip;
        $doc->file_path = $path;
        $doc->save();

        $paraf = $request->as;
        $nama  = $request->nama;
        $users = $request->name;
        $note  = $request->note;

        for ($i = 0; $i < count($users); $i++) {
            $kode          = rand(10000, 99999);
            $field         = new Field;
            $field->doc_id = $doc->id;
            $field->name   = $users[$i];
            $field->value  = $note[$i];
            $field->kode   = $kode;
            $field->save();
        }

        for ($i = 0; $i < count($paraf); $i++) {
            $kod           = rand(10000, 99999);
            $field         = new Paraf;
            $field->doc_id = $doc->id;
            $field->name   = $nama[$i];
            $field->as     = $paraf[$i];
            $field->kode   = $kod;
            $field->save();
        }

        return redirect()->route('dashboard');

    }

    public function storeLink(Request $request, $id)
    {
        $limit     = Carbon::now()->addHour()->timestamp;
        $doc       = Doc::findOrFail($id);
        $doc->link = $limit;
        $doc->save();

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "name" => "required",
            'doc'  => 'nullable|mimes:docx|max:2048',
        ]);

        $paraf = $request->as;
        $nama  = $request->nama;
        $users = $request->name;
        $note  = $request->note;
        $pile  = $request->file('doc');
        $doc   = Doc::where(DB::raw('md5(id)'), $id)->firstOrFail();

        if ($pile) {
            $path           = $pile->store('docs', 'public');
            $doc->file_path = $path;
        }

        $doc->nomor     = $request->nomor;
        $doc->tanggal   = $request->tanggal;
        $doc->as_head   = $request->as_head;
        $doc->name_head = $request->name_head;
        $doc->as_name   = $request->as_name;
        $doc->nip       = $request->nip;
        $doc->save();

        Field::where('doc_id', $doc->id)->delete();
        for ($i = 0; $i < count($users); $i++) {
            $kode          = rand(10000, 99999);
            $field         = new Field;
            $field->doc_id = $doc->id;
            $field->name   = $users[$i];
            $field->value  = $note[$i];
            $field->kode   = $kode;
            $field->save();
        }

        Paraf::where('doc_id', $doc->id)->delete();
        for ($i = 0; $i < count($paraf); $i++) {
            $kod           = rand(10000, 99999);
            $field         = new Paraf;
            $field->doc_id = $doc->id;
            $field->name   = $nama[$i];
            $field->as     = $paraf[$i];
            $field->kode   = $kod;
            $field->save();
        }

        return redirect()->route('dashboard');
    }

    public function edit($id)
    {
        $doc = Doc::where(DB::raw('md5(id)'), $id)->firstOrFail();

        $dinas = $doc->users->map(fn($user) => [
            'name' => $user->name,
            'note' => $user->value,
        ]);

        $paraf = $doc->paraf->map(fn($user) => [
            'name' => $user->name,
            'note' => $user->as,
        ]);

        $action = "Edit Data";
        return view('doc.form', compact('action', 'doc', 'dinas', 'paraf'));
    }

    private static function link($doc)
    {
        $pdfPath           = storage_path('app/public/' . $doc->file_path);
        $templateProcessor = new TemplateProcessor($pdfPath);

        $templateProcessor->setValue("nomor", $doc->nomor);
        $templateProcessor->setValue("tanggal", $doc->tanggal);
        $templateProcessor->setValue("as_head", $doc->as_head);
        $templateProcessor->setValue("as_name", $doc->as_name);
        $templateProcessor->setValue("name_head", $doc->name_head);
        $templateProcessor->setValue("nip", $doc->nip);

        $templateProcessor->cloneRow('val', count($doc->users));
        foreach ($doc->users as $index => $row) {
            $i = $index + 1;
            $templateProcessor->setValue("no#{$i}", $i);
            $templateProcessor->setValue("val#{$i}", $row->value);
            $templateProcessor->setValue("nama#{$i}", $row->name);
            if ($row->ttd) {
                $ttdPath = storage_path('app/public/' . $row->ttd);
                $templateProcessor->setImageValue("ttd#{$i}", [
                    'path'   => $ttdPath,
                    'width'  => 300,
                    'height' => 100,
                    'ratio'  => true,
                ]);
            } else {
                $templateProcessor->setValue("ttd#{$i}", $row->ttd);

            }
        }

        $templateProcessor->cloneRow('as', count($doc->paraf));
        foreach ($doc->paraf as $index => $row) {
            $i = $index + 1;
            $templateProcessor->setValue("nu#{$i}", $i);
            $templateProcessor->setValue("as#{$i}", $row->as);
            $templateProcessor->setValue("name#{$i}", $row->name);
            if($row->paraf)
            {
                $paraf = storage_path('app/public/' . $row->paraf);
                $templateProcessor->setImageValue("paraf#{$i}", [
                    'path'   => $paraf,
                    'width'  => 150,
                    'height' => 80,
                    'ratio'  => true,
                ]);
            }
            else
            {
                $templateProcessor->setValue("paraf#{$i}", null);
            }
        }

        $name       = $doc->nomor . '-' . date("YmdHis") . '.docx';
        $outputPath = storage_path('app/public/' . $name);
        $templateProcessor->saveAs($outputPath);

        $fileUrl = asset('storage/' . $name);

        return $fileUrl;

    }

    public function preview($id)
    {
        $doc     = Doc::where(DB::raw('md5(id)'), $id)->firstOrFail();
        $fileUrl = Home::link($doc);
        return view('doc', compact('fileUrl'));
    }

    public function showLink($id)
    {
        $doc     = Doc::where(DB::raw('md5(link)'), $id)->firstOrFail();
        $fileUrl = Home::link($doc);
        return view('ttd', compact('doc', 'fileUrl'));
    }

    public function signLink(Request $request, $id)
    {
        $doc = Doc::where(DB::raw('md5(link)'), $id)->firstOrFail();

        $request->validate([
            'data_url' => 'required|string',
            'kode'     => ['required', new KodeLink],
        ]);

        $field = Field::where('doc_id', $doc->id)->where('kode', $request->kode)->first();
        $paraf = Paraf::where('doc_id', $doc->id)->where('kode', $request->kode)->first();

        if (! $field && ! $paraf) {
            return response()->json(['message' => 'Kode tidak valid untuk dokumen ini.'], 422);
        }

        $dataUrl = $request->input('data_url');

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

        if ($field) {
            $field->update(['ttd' => $filename]);
        }

        if ($paraf) {
            $paraf->update(['paraf' => $filename]);
        }

        return response()->json([
            'message' => 'Saved',
            'url'     => Storage::disk('public')->url($filename),
        ]);
    }
}
