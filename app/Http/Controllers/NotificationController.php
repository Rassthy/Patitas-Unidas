<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications], 200);
    }

    public function show($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['notification' => $notification], 200);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'leida' => 'required|boolean',
        ]);

        $notification->update($data);
        return response()->json(['notification' => $notification], 200);
    }
}
