<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getTemplate(Request $request)
    {
        // nothing to do
        $id = (int) $request->get('id');

        if ($id === 1) {
            // check exists new model
            if (file_exists(storage_path('app/models/control_.docx'))) {
                // return new model
                return response()->download(storage_path('app/models/control_.docx'));
            }
            // return default model
            return response()->download(storage_path('app/models/control.docx'));
        }
        if ($id === 2) {
            // check exists new model
            if (file_exists(storage_path('app/models/pilotage_.docx'))) {
                // return new model
                return response()->download(storage_path('app/models/pilotage_.docx'));
            }
            // return default model
            return response()->download(storage_path('app/models/pilotage.docx'));
        }
        return null;
    }

    public function saveTemplate(Request $request)
    {
        $message = null;

        if ($request->has('template1')) {
            // Get image file
            $template = $request->file('template1');
            // Upload image
            $template->storeAs('models', 'control_.docx');

            $message = 'Template updated !';
        }

        if ($request->has('template2')) {
            // Get image file
            $template = $request->file('template2');
            // Upload image
            $template->storeAs('models', 'pilotage_.docx');

            $message = 'Template updated !';
        }

        return redirect()->back()->with('message', $message);
    }

    public function get(int $id)
    {
        $document = Document::Find($id);
        $path = storage_path('docs/' . $id);
        $file_contents = file_get_contents($path);

        return response($file_contents)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', $document->mimetype)
            ->header('Content-length', strlen($file_contents))
            ->header('Content-Disposition', 'attachment; filename="' . $document->filename .'"')
            ->header('Content-Transfer-Encoding', 'binary');
    }

    public function store(Request $request)
    {
        //Log::Alert("store called");
        $file = $request->file('file');
        $control_id = $request->get('control');

        // Log::Alert($control_id);
        $doc = new Document();
        $doc->control_id = $control_id;
        $doc->filename = $file->getClientOriginalName();
        // Log::Alert("store filenale ".$file->getClientOriginalName());
        $doc->mimetype = $file->getClientMimeType();
        // Log::Alert("store mimetype ".$file->getClientMimeType());
        $doc->size = $file->getSize();
        // Log::Alert("store size ".$file->getSize());
        // Log::Alert("store path ".$file->path());
        $doc->hash = hash_file('sha256', $file->path());
        $doc->save();

        // Log::Alert("store Doc saved");

        $file->move(storage_path('docs'), $doc->id);

        // Log::Alert("store Done.");

        return response()->json(
            ['success' => $doc->filename,
                'id' => $doc->id,
            ]
        );
    }

    public function delete(int $id)
    {
        // Log::Alert("delete called");
        $document = Document::Find($id);
        if ($document === null) {
            return redirect('image/list')
                ->with('errorMessage', 'File not found !');
        }

        $path = storage_path('docs/'.$document->id);
        // Log::Alert($path);

        // Log::Alert("delete file ".$path);
        if (file_exists($path)) {
            unlink($path);
        }
        $document->delete();

        // Log::Alert("delete done");
        return null;
    }

    public function index()
    {
        $count = Document::count();
        $sum = Document::sum('size');

        return view('/documents/index')
            ->with('count', $count)
            ->with('sum', $sum);
    }

    public function check()
    {
        $documents = Document::with('control')->get();

        return view('/documents/check')
            ->with('documents', $documents);
    }
}
