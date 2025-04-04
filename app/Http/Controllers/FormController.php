<?php
namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Notification;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    public function submitForm(Request $request, $serviceId)
    {
        $user = Auth::user();

        $form = Form::where('user_id', $user->id)
            ->where('service_id', $serviceId)
            ->first();

        if (! $form) {
            return response()->json(['status' => 'form_not_found']);
        }

        if ($form->status === "pending" || $form->status === "  ") {
            $form->status = 'review';
            $form->save();

            // Create a notification when the form is submitted
            Notification::create([
                'user_id'     => $user->id,
                'type'        => 'form_submission',
                'title'       => 'Votre formulaire a été soumis pour examen.',
                'serviceLink' => $serviceId,
            ]);

            return response()->json(['status' => 'submitted_for_review']);
        }

        if ($form->status === "review") {
            return response()->json(['status' => 'form_in_review']);
        }

        if ($form->status === "accepted") {
            return response()->json(['status' => 'form_accepted']);
        }

        return response()->json(['status' => 'unknown_error']);
    }

    public function getForms()
    {
        $forms = Form::with(['user', 'service', 'userDocuments.document'])->get();

        return response()->json($forms);
    }
    public function destroy($id)
{
    $form = Form::with(['user', 'service'])->find($id);

    if (! $form) {
        return response()->json(['message' => 'Formulaire introuvable'], 404);
    }

    $user = $form->user;
    $serviceName = $form->service->name ?? 'le service concerné';

    // Delete associated documents
    $userDocuments = UserDocuments::where('form_id', $form->id)->get();

    foreach ($userDocuments as $userDocument) {
        if (Storage::exists($userDocument->file_path)) {
            Storage::delete($userDocument->file_path);
        }
    }

    UserDocuments::where('form_id', $form->id)->delete();

    // Send notification before deletion
    if ($user) {
        Notification::create([
            'user_id'     => $user->id,
            'type'        => 'form_deleted',
            'title'       => "Votre formulaire pour <strong>{$serviceName}</strong> a été supprimé.",
            'serviceLink' => "/dashboard/forms", // or anywhere you redirect for forms list
            'isUnRead'    => true,
        ]);
    }

    $form->delete();

    return response()->json(['message' => 'Formulaire et documents associés supprimés avec succès']);
}


    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,review,rejected,accepted',
        ]);

        $form = Form::with(['user', 'service'])->find($id); // Eager load user and service

        if (! $form) {
            return response()->json(['message' => 'Formulaire introuvable'], 404);
        }

        if ($form->status === $request->status) {
            return response()->json(['message' => 'Le statut est déjà défini à cette valeur'], 400);
        }

        $form->status = $request->status;
        $form->save();

        $user        = $form->user;
        $serviceName = $form->service->name ?? 'le service concerné';

        $messages = [
            'accepted' => "Votre formulaire pour <strong>{$serviceName}</strong> a été accepté. Merci pour votre soumission.",
            'rejected' => "Votre formulaire pour <strong>{$serviceName}</strong> a été rejeté. Veuillez vérifier les informations fournies.",
            'pending'  => "Votre formulaire pour <strong>{$serviceName}</strong> est en attente. Veuillez le remplir dès que possible.",
            'review'   => "Votre formulaire pour <strong>{$serviceName}</strong> est en cours d'examen. Nous vous tiendrons informé sous peu.",
        ];

        $types = [
            'accepted' => 'form_accepted',
            'rejected' => 'form_rejection',
            'pending'  => 'form_submission',
            'review'   => 'form_submission',
        ];

        if ($user) {
            Notification::create([
                'user_id'     => $user->id,
                'type'        => $types[$form->status],
                'title'       => $messages[$form->status],
                'serviceLink' => "/dashboard/forms/{$form->id}", // adjust if needed
                'isUnRead'    => true,
            ]);
        }

        return response()->json([
            'message' => 'Statut du formulaire mis à jour avec succès',
            'form'    => $form,
        ]);
    }

}
