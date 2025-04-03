<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUserNotifications()
    {
        // return auth()->user()->notifications()->latest()->get();
        $user = Auth::user();

        $data = Notification::where('user_id', $user->id)->latest()->get();

        return $data;
    }

    public function allRead()
    {
        $user = Auth::user();

        // Update the 'read' status of all unread notifications for the authenticated user
        $notifications = Notification::where('user_id', $user->id)
            ->where('isUnRead', 1)       // Assuming 'is_read' is the column to track read/unread status
            ->update(['isUnRead' => 0]); // Mark them as read

        if ($notifications) {
            return response()->json(['status' => 'success', 'message' => 'All notifications marked as read.']);
        }

        return response()->json(['status' => 'error', 'message' => 'No notifications to mark as read.']);
    }
    
    public function read($id)
    {
        $notification = Notification::where('id', $id)
            ->update(['isUnRead' => 0]);

        if ($notification) {
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read.']);
        }
        return response()->json(['status' => 'error', 'message' => 'No notifications to mark as read.']);

    }
}
