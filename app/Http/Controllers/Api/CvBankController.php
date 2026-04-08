<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvBankController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeCv::with(['employee.user', 'employee.department', 'uploader']);

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('is_current')) {
            $query->where('is_current', $request->boolean('is_current'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $cvs = $query->orderBy('uploaded_at', 'desc')->paginate(20);

        return response()->json($cvs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'summary' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'education_level' => 'nullable|string',
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('cvs', $fileName, 'public');

        // Mark previous CVs as not current
        EmployeeCv::where('employee_id', $validated['employee_id'])
            ->update(['is_current' => false]);

        // Get the latest version number
        $latestVersion = EmployeeCv::where('employee_id', $validated['employee_id'])
            ->max('version') ?? 0;

        $cv = EmployeeCv::create([
            'employee_id' => $validated['employee_id'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'version' => $latestVersion + 1,
            'is_current' => true,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
            'summary' => $validated['summary'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'education_level' => $validated['education_level'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'certifications' => $validated['certifications'] ?? null,
            'languages' => $validated['languages'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'CV uploaded successfully',
            'cv' => $cv->load(['employee.user', 'uploader'])
        ], 201);
    }

    public function show(EmployeeCv $cv)
    {
        return response()->json($cv->load([
            'employee.user',
            'employee.department',
            'uploader',
            'updater'
        ]));
    }

    public function update(Request $request, EmployeeCv $cv)
    {
        $validated = $request->validate([
            'summary' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'education_level' => 'nullable|string',
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_current' => 'nullable|boolean',
        ]);

        if (isset($validated['is_current']) && $validated['is_current']) {
            // Mark other CVs as not current
            EmployeeCv::where('employee_id', $cv->employee_id)
                ->where('id', '!=', $cv->id)
                ->update(['is_current' => false]);
        }

        $validated['last_updated_by'] = auth()->id();
        $cv->update($validated);

        return response()->json([
            'message' => 'CV updated successfully',
            'cv' => $cv->load(['employee.user', 'uploader'])
        ]);
    }

    public function download(EmployeeCv $cv)
    {
        $filePath = storage_path('app/public/' . $cv->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, $cv->file_name);
    }

    public function destroy(EmployeeCv $cv)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($cv->file_path)) {
            Storage::disk('public')->delete($cv->file_path);
        }

        $cv->delete();

        return response()->json(['message' => 'CV deleted successfully']);
    }

    public function getEmployeeCvHistory($employeeId)
    {
        $cvs = EmployeeCv::where('employee_id', $employeeId)
            ->with(['uploader', 'updater'])
            ->orderBy('version', 'desc')
            ->get();

        return response()->json($cvs);
    }
}
