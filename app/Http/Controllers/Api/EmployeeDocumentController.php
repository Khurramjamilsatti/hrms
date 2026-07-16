<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function index(Employee $employee)
    {
        $documents = $employee->documents()->orderBy('created_at', 'desc')->get();

        return response()->json($documents);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
            'expiry_date' => 'nullable|date',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('employee-documents/' . $employee->id, $fileName, 'public');

        $document = EmployeeDocument::create([
            'employee_id' => $employee->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_path' => $filePath,
            'expiry_date' => $validated['expiry_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document,
        ], 201);
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        if ($document->employee_id !== $employee->id) {
            return response()->json(['message' => 'Document does not belong to this employee'], 404);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}
