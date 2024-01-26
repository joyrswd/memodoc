<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use App\Http\Requests\DocumentRequest;


class DocumentController extends Controller
{
    private DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DocumentRequest $request)
    {
        return view('document.index', [
            'page' => $this->documentService->getDocuments(auth()->id(), [
                'title' => $request->input('doc_title'),
                'content' => $request->input('doc_content'),
                'from' => $request->input('doc_from'),
                'to' => $request->input('doc_to'),
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('document.edit', [
            'document' => $this->documentService->getDocument(auth()->id(), $id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentRequest $request, string $id)
    {
        $this->documentService->updateDocument(auth()->id(), $id, [
            'title' => $request->input('doc_title'),
            'content' => $request->input('doc_content'),
        ]);
        return back()->with('success', __('updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->documentService->deleteDocument(auth()->id(), $id);
        return back()->with('success', __('deleted'));
    }
}
