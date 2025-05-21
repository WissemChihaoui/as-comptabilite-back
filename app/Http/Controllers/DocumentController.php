<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Form;
use App\Models\Service;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function getDocumentsByService($serviceId)
    {
        try {
            $service = Service::find($serviceId);

            if (! $service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ], 404);
            }

            $documents = Document::where('service_id', $serviceId)->get();

            return response()->json([
                'success'   => true,
                'service'   => $service->name,
                'documents' => $documents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'service_id'  => 'required|exists:services,id',
            'document_id' => 'required|exists:documents,id',
            'file.*'      => 'required|file|max:5120', // Validate each file in the array
        ]);

        $user       = Auth::user();
        $serviceId  = $request->service_id;
        $documentId = $request->document_id;

        // Check if a form already exists for this service & user
        $form = Form::where('user_id', $user->id)->where('service_id', $serviceId)->first();
        if (! $form) {
            // Create a new form if it doesn't exist
            $form = Form::create([
                'user_id'    => $user->id,
                'service_id' => $serviceId,
                'status'     => 'pending',
            ]);
        }

        if ($form->status === 'accepted' || $form->status === 'review') {
            return response()->json(['message' => 'You cannot upload any files'], 401);
        }

        foreach ($request->file('file') as $file) { // Loop through the array of files
            $filePath     = $file->store("documents/{$user->id}/service_{$serviceId}", 'public');
            $originalName = $file->getClientOriginalName();
            $mimeType     = $file->getMimeType();
            $fileSize     = $file->getSize();

            // For service IDs 1, 2, 3 - override existing document
            if (in_array($serviceId, [1, 2, 3])) {
                $existingDoc = UserDocuments::where('form_id', $form->id)
                    ->where('document_id', $documentId)
                    ->first();

                if ($existingDoc) {
                    // Delete the old file
                    Storage::disk('public')->delete($existingDoc->file_path);
                    $existingDoc->delete();
                }
            }

            // Create a new user document record for each uploaded file
            UserDocuments::create([
                'form_id'       => $form->id,
                'document_id'   => $documentId,
                'file_path'     => $filePath,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'file_size'     => $fileSize,
            ]);
        }

        return response()->json(['message' => 'Documents uploaded successfully!']);
    }

    public function deleteDocument(Request $request, $id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Find the document and ensure the user has access
        $userDocuments = UserDocuments::where('document_id', $id)
            ->whereHas('form', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (! $userDocuments) {
            return response()->json(['message' => 'Document not found or unauthorized.'], 404); // Not Found
        }

        // Delete the document's file from storage
        Storage::disk('public')->delete($userDocuments->file_path);

        // Delete the document
        $formId = $userDocuments->form_id;
        $userDocuments->delete();

        // Check if there are remaining documents for the form
        $remainingDocs = UserDocuments::where('form_id', $formId)->count();
        if ($remainingDocs === 0) {
            // If no documents remain, delete the associated form
            Form::where('id', $formId)->delete();
        }

        return response()->json(['message' => 'Document deleted successfully!'], 200); // Success response
    }

    public function getUserDocumentsByService(Request $request, $serviceId, $id)
    {
        $user = $request->user();

        $form = Form::where('user_id', $user->id)
            ->where('service_id', $serviceId)
            ->first();

        if (! $form) {
            return response()->json(['message' => 'No form found for this service'], 404);
        }

        $documents = UserDocuments::where('document_id', $id)->where('form_id', $form->id)->with('document')->get();

        return response()->json($documents);
    }

    public function destroy($userDocumentId)
    {
        $user = Auth::user();
        \Log::info('Authenticated User ID: ' . ($user ? $user->id : 'No User Authenticated'));

        if (! $user) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401); // Unauthorized
        }

        // Retrieve the UserDocument by ID with the related form
        $userDocument = UserDocuments::with('form')->find($userDocumentId);

        if (! $userDocument) {
            return response()->json(['message' => 'User document introuvable.'], 404); // Not Found
        }

        // Check if the user is authorized (owner OR admin)
        if ($user->isAdmin !== 1 && $userDocument->form->user_id !== $user->id) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer ce document.'], 403); // Forbidden
        }

        // Get the file path to delete it
        $filePath = $userDocument->file_path;

        // Delete the file from storage if it exists
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Save form ID before deleting
        $formId = $userDocument->form_id;

        // Delete the document
        $userDocument->delete();

        // If no documents remain, delete the form too
        $remainingDocuments = UserDocuments::where('form_id', $formId)->count();
        if ($remainingDocuments === 0) {
            Form::where('id', $formId)->delete();
            \Log::info("Form ID $formId deleted because no documents remain.");
        }

        return response()->json(['message' => 'Document et fichier supprimés avec succès'], 200);
    }

    public function getDocument($id)
    {
        $user = Auth::user(); // Get the authenticated user

        $document = UserDocuments::where('document_id', $id)
            ->whereHas('form', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first(); // Get the first matching document

        if (! $document) {
            return response()->json(['message' => 'Document not found or unauthorized.'], 404); // Not Found
        }

        return response()->json($document);
    }

    public function getStatus(Request $request, $serviceId)
    {
        $user = Auth::user();

        $form = Form::where('user_id', $user->id)
            ->where('service_id', $serviceId)
            ->select('status', 'note')
            ->first();

        return response()->json([
            'status'    => $form->status ?? 'none',
            'note'      => $form->note ?? null,
            'serviceId' => $serviceId,
        ]);
    }

    public function getStatusById($id)
    {
        $user = Auth::user();

        $form = Form::select('status', 'note')->find($id);

        if (! $form) {
            return response()->json(['message' => 'Formulaire introuvable'], 404);
        }

        return response()->json([
            'status' => $form->status ?? 'none',
            'note'   => $form->note ?? null,
        ]);
    }

    public function download($id)
    {
        // Fetch the document record based on provided ID
        $document = UserDocuments::findOrFail($id);

        // Assuming that $document->file_path contains a path like 'documents/2/service_1/filename.docx'
        // Create the complete path to the file
        $path = storage_path('app/public/' . $document->file_path);

        // Check if file exists at the given path
        if (! file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Return the file as a download response
        return response()->download($path, $document->original_name, [
            'Content-Type' => $document->mime_type,
        ]);
    }
}
