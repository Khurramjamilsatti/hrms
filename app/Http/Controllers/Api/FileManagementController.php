<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FileCategory;
use App\Models\EmployeeFile;
use App\Models\FileAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    // Categories
    public function getCategories()
    {
        $categories = FileCategory::with('parent')
            ->where('is_active', true)
            ->get();
        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:file_categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $category = FileCategory::create($validated);
        return response()->json($category, 201);
    }

    // Files
    public function getFiles(Request $request)
    {
        $user = $request->user();
        $query = EmployeeFile::with(['employee', 'category', 'uploadedBy']);

        // Role-based filtering
        if ($user->hasRole('employee')) {
            // Employee can only see their own files
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                return response()->json(['data' => []]);
            }
        } elseif ($user->hasRole('manager')) {
            // Manager can see their team's files
            $query->whereHas('employee', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->hasRole('section_head')) {
            // Section head can see their department's files
            if ($user->employee && $user->employee->department_id) {
                $query->whereHas('employee', function($q) use ($user) {
                    $q->where('department_id', $user->employee->department_id);
                });
            }
        }
        // hr_admin, super_admin, and admin can see all files

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_confidential')) {
            $query->where('is_confidential', $request->boolean('is_confidential'));
        }

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('file_name', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $files = $query->latest()->paginate($perPage);
        return response()->json($files);
    }

    public function storeFile(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'category_id' => 'required|exists:file_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'is_confidential' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee_files', 'public');

        $employeeFile = EmployeeFile::create([
            'employee_id' => $validated['employee_id'] ?? null,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'is_confidential' => $validated['is_confidential'] ?? false,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'version' => 1,
        ]);

        return response()->json($employeeFile->load(['employee', 'category', 'uploadedBy']), 201);
    }

    public function getFile($id)
    {
        $file = EmployeeFile::with([
            'employee', 
            'category', 
            'uploadedBy', 
            'parentFile',
            'versions',
            'accessLogs.user'
        ])->findOrFail($id);

        // Log access
        FileAccessLog::create([
            'file_id' => $id,
            'user_id' => auth()->id(),
            'action' => 'view',
            'accessed_at' => now(),
        ]);

        return response()->json($file);
    }

    public function downloadFile($id)
    {
        $file = EmployeeFile::findOrFail($id);

        // Log download
        FileAccessLog::create([
            'file_id' => $id,
            'user_id' => auth()->id(),
            'action' => 'download',
            'accessed_at' => now(),
        ]);

        return response()->download(storage_path('app/public/' . $file->file_path), $file->file_name);
    }

    public function updateFile(Request $request, $id)
    {
        $file = EmployeeFile::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_confidential' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $file->update($validated);

        // Log edit
        FileAccessLog::create([
            'file_id' => $id,
            'user_id' => auth()->id(),
            'action' => 'edit',
            'accessed_at' => now(),
        ]);

        return response()->json($file->load(['employee', 'category', 'uploadedBy']));
    }

    public function deleteFile($id)
    {
        $file = EmployeeFile::findOrFail($id);

        // Log deletion
        FileAccessLog::create([
            'file_id' => $id,
            'user_id' => auth()->id(),
            'action' => 'delete',
            'accessed_at' => now(),
        ]);

        // Delete physical file
        Storage::disk('public')->delete($file->file_path);
        
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }

    public function uploadNewVersion(Request $request, $id)
    {
        $parentFile = EmployeeFile::findOrFail($id);
        
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee_files', 'public');

        $newVersion = EmployeeFile::create([
            'employee_id' => $parentFile->employee_id,
            'category_id' => $parentFile->category_id,
            'title' => $parentFile->title,
            'description' => $validated['description'] ?? $parentFile->description,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'is_confidential' => $parentFile->is_confidential,
            'expiry_date' => $parentFile->expiry_date,
            'version' => $parentFile->version + 1,
            'parent_file_id' => $parentFile->id,
        ]);

        return response()->json($newVersion->load(['employee', 'category', 'uploadedBy']), 201);
    }

    public function getAccessLogs($id)
    {
        $logs = FileAccessLog::where('file_id', $id)
            ->with('user')
            ->latest('accessed_at')
            ->paginate(50);

        return response()->json($logs);
    }
}
