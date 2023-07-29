<?php

namespace App\Http\Controllers\API;

use App\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documents = Document::all();

        return response()->json($documents);
    }

    public function store(Request $request)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document = Document::create($request->all());

        return response()->json($document, 201);
    }

    public function show(Document $document)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($document);
    }

    public function update(Request $request, Document $document)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->update($request->all());

        return response()->json();
    }

    public function destroy(Document $document)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $document->delete();

        return response()->json();
    }
}
