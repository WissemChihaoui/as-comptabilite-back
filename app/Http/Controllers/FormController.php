<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use App\Models\Notification;

class FormController extends Controller 
{
    public function submitForm(Request $request, $serviceId)
    {
        $user = Auth::user();
    
        $form = Form::where('user_id', $user->id)
                    ->where('service_id', $serviceId)
                    ->first();
        
        if (!$form) {
            return response()->json(['status' => 'form_not_found']);
        }
    
        if ($form->status === "pending" || $form->status === "  ") {
            $form->status = 'review';
            $form->save();

            // Create a notification when the form is submitted
            Notification::create([
                'user_id' => $user->id,
                'type' => 'form_submission',
                'title' => 'Votre formulaire a été soumis pour examen.',
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
}
