<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Form;
use App\Models\UserDocument;
use App\Models\Document;
use App\Models\Service;

class DocumentController extends Controller
{
    public function getDocumentsByService($serviceId)
    {
        try {
            $service = Service::find($serviceId);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $documents = Document::where('service_id', $serviceId)->get();

            return response()->json([
                'success' => true,
                'service' => $service->name,
                'documents' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadDocument(Request $request)  
    {  
        $request->validate([  
            'service_id' => 'required|exists:services,id',  
            'document_id' => 'required|exists:documents,id',  
            'file.*' => 'required|file|max:5120', // Validate each file in the array  
        ]);  

        $user = Auth::user();  
        $serviceId = $request->service_id;  
        $documentId = $request->document_id;  

        // Check if a form already exists for this service & user  
        $form = Form::where('user_id', $user->id)->where('service_id', $serviceId)->first();  
        if (!$form) {  
            // Create a new form if it doesn't exist  
            $form = Form::create([  
                'user_id' => $user->id,  
                'service_id' => $serviceId,  
                'status' => 'pending',  
            ]);  
        }  

        foreach ($request->file('file') as $file) { // Loop through the array of files  
            $filePath = $file->store("documents/{$user->id}/service_{$serviceId}", 'public');  
            $originalName = $file->getClientOriginalName();  
            $mimeType = $file->getMimeType();  
            $fileSize = $file->getSize();  

            // For service IDs 1, 2, 3 - override existing document  
            if (in_array($serviceId, [1, 2, 3])) {  
                $existingDoc = UserDocument::where('form_id', $form->id)  
                    ->where('document_id', $documentId)  
                    ->first();  
                
                if ($existingDoc) {  
                    // Delete the old file  
                    Storage::disk('public')->delete($existingDoc->file_path);  
                    $existingDoc->delete();  
                }  
            }  

            // Create a new user document record for each uploaded file  
            UserDocument::create([  
                'form_id' => $form->id,  
                'document_id' => $documentId,  
                'file_path' => $filePath,  
                'original_name' => $originalName,  
                'mime_type' => $mimeType,  
                'file_size' => $fileSize,  
            ]);  
        }  

        return response()->json(['message' => 'Documents uploaded successfully!']);  
    }  

    public function deleteDocument(Request $request, $id)
    {
        $userDocument = UserDocument::findOrFail($id);
        $formId = $userDocument->form_id;

        Storage::disk('public')->delete($userDocument->file_path);
        $userDocument->delete();

        $remainingDocs = UserDocument::where('form_id', $formId)->count();
        if ($remainingDocs === 0) {
            Form::where('id', $formId)->delete();
        }

        return response()->json(['message' => 'Document deleted successfully!']);
    }

}
