<?php

namespace App\Http\Controllers;

// Models
use App\Models\Document;
use App\Models\Control;
// Framework
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function getTemplate(Request $request)
    {
        //  Get document teample id
        $id = (int) $request->get('id');

        if ($id === 1) {
            // return default model
            return response()->download(storage_path('app/models/control_'. Auth::user()->language .'.docx'));
        }
        if ($id === 2) {
            // check exists new model
            if (file_exists(storage_path('app/models/control_.docx'))) {
                // return new model
                return response()->download(storage_path('app/models/control_.docx'));
            }
        }
        if ($id === 3) {
            // return default model
            return response()->download(storage_path('app/models/pilotage_'. Auth::user()->language .'.docx'));
        }
        if ($id === 4) {
            // check exists new model
            if (file_exists(storage_path('app/models/pilotage_.docx'))) {
                // return new model
                return response()->download(storage_path('app/models/pilotage_.docx'));
            }
        }
        return null;
    }

    public function saveTemplate(Request $request)
    {
        // Only for administrator
        abort_if(
            (Auth::User()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $messages = Collect();

        if ($request->has('template1')) {
            // Get image file
            $template = $request->file('template1');
            // Upload image
            $template->storeAs('models', 'control_.docx');

            $messages->push('Template updated !');
        }

        if ($request->has('template2')) {
            // Get image file
            $template = $request->file('template2');
            // Upload image
            $template->storeAs('models', 'pilotage_.docx');

            $messages->push('Template updated !');
        }

        return redirect()
            ->back()
            ->with('messages', $messages);
    }

    public function get(int $id)
    {
        // Not for API
        abort_if(
            (Auth::User()->role === 4),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $document = Document::Find($id);

        // Document not found
        abort_if($document === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Auditee may get documents from assigned controls only
        abort_if(
            (Auth::User()->role === 5) &&
            ! DB::table('control_user')
                ->where('user_id', Auth::User()->id)
                ->where('control_id', $document->control_id)
                ->exists(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get path
        $path = storage_path('docs/' . $id);

        // check file exists
        abort_if(! file_exists($path), Response::HTTP_NOT_FOUND, '404 Not Found');

        // get file content
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
        // Not for API
        abort_if((Auth::User()->role === 4), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get file
        $file = $request->file('file');

        // Get Control
        $control_id = $request->get('control');

        // Auditee may save document to assigned control only
        abort_if(
            Auth::User()->role === 5 &&
                ! (DB::table('control_user')
                    ->where('control_id', $id)
                    ->where('user_id', Auth::User()->id)
                    ->exists()
                    ||
                DB::table('control_user_group')
                    ->join('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
                    ->where('control_user_group.control_id', $id)
                    ->where('user_user_group.user_id', Auth::User()->id)
                    ->exists()),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Save document
        $doc = new Document();
        $doc->control_id = $control_id;
        $doc->filename = $file->getClientOriginalName();
        $doc->mimetype = $file->getClientMimeType();
        $doc->size = $file->getSize();
        $doc->hash = hash_file('sha256', $file->path());
        $doc->save();

        // Move file to storage folder
        $file->move(storage_path('docs'), $doc->id);

        // response
        return response()->json(
            ['success' => $doc->filename,
                'id' => $doc->id,
            ]
        );
    }

    public function delete(int $id)
    {
        // Not for API and Auditor
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Find the document
        $document = Document::Find($id);

        if ($document === null) {
            return response()
                ->with('errorMessage', 'File not found !');
        }

        // Auditee may delete documents from assigned controls
        // when control has not been made
        abort_if(
            (Auth::User()->role === 5) &&
            ! DB::table('control_user')
                ->where('user_id', Auth::User()->id)
                ->where('control_id', $document->control_id)
                ->leftjoin('controls', 'controls.id', '=', 'control_user.control_id')
                ->whereNull('controls.realisation_date')
                ->exists(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $path = storage_path('docs/'.$document->id);

        if (file_exists($path)) {
            unlink($path);
        }
        $document->delete();

        return null;
    }

    public function index()
    {
        // Only for administrator
        abort_if(
            (Auth::User()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $count = Document::count();
        $sum = Document::sum('size');

        $duration = config('deming.cleanup-duration');

        return view('/documents/index')
            ->with('count', $count)
            ->with('sum', $sum)
            ->with('duration', $duration);
    }

    public function check()
    {
        // Only for administrator
        abort_if(
            (Auth::User()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get all documents
        $documents = Document::with('control')->get();

        // show view
        return view('/documents/check')
            ->with('documents', $documents);
    }

    public function saveConfig(Request $request)
    {
        // Only for administrator
        abort_if(
            (Auth::User()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get duration from request
        $duration = $request->get('duration');

        // Get action
        $action = $request->input('action');

        // Act
        if ($action =='save') {


            // Set duration
            config(['deming.cleanup-duration' => $duration]);

            // Save configuration
            $text = '<?php return ' . var_export(config('deming'), true) . ';';
            file_put_contents(config_path('deming.php'), $text);

            // Message
            $messages = Collect('Configuration saved !');
            }
        else if ($action =='test') {
            $dateLimit = Carbon::now()->subMonths($duration)->toDateString();

            $result = Control::cleanup($dateLimit, true);

            $messages = Collect(
                    [
                        "{$result['documentCount']} document(s) will be deleted.",
                        "{$result['controlCount']} control(s) will be deleted."
                    ]
                );
            }
        else if ($action == 'delete') {
            $dateLimit = Carbon::now()->subMonths($duration)->toDateString();

            $result = Control::cleanup($dateLimit, false);

            $messages = Collect(
                    [
                        "{$result['documentCount']} document(s) deleted.",
                        "{$result['controlCount']} control(s) deleted."
                    ]
                );
        }
        else
            $messages = null;

        // get previous fields
        $count = Document::count();
        $sum = Document::sum('size');

        // Redirect
        return view('/documents/index')
            ->with('count', $count)
            ->with('sum', $sum)
            ->with('duration', $duration)
            ->with('messages', $messages);
    }
}
