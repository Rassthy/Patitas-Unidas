<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::orderBy('created_at', 'desc')->get();
        return response()->json(['chats' => $chats], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'is_group' => 'required|boolean',
            'nombre_grupo' => 'nullable|string|max:100',
        ]);

        $chat = Chat::create($data);
        return response()->json(['chat' => $chat], 201);
    }

    public function show($id)
    {
        $chat = Chat::findOrFail($id);
        return response()->json(['chat' => $chat], 200);
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);

        $data = $request->validate([
            'is_group' => 'nullable|boolean',
            'nombre_grupo' => 'nullable|string|max:100',
        ]);

        $chat->update($data);
        return response()->json(['chat' => $chat], 200);
    }

    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->delete();
        return response()->json(['message' => 'Chat deleted successfully.'], 200);
    }
}
