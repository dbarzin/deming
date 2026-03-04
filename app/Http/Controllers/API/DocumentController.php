<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documents = Document::all();

        return response()->json($documents);
    }

    public function store(Request $request)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        abort(Response::HTTP_NOT_IMPLEMENTED, '501 Not Implemented');
    }
    public function show(Document $document)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($document);
    }

    public function update(Request $request, Document $document)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        abort(500, 'Not implemented');
    }

    public function destroy(Document $document)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->delete();

        return response()->json();
    }
}
