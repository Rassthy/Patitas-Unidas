<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Listar chats del usuario autenticado
    public function index()
    {
        $chats = Chat::whereHas('participants', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['participants.user', 'lastMessage.sender'])
            ->get()
            ->map(function($chat) {
                $other = $chat->participants
                    ->where('user_id', '!=', Auth::id())
                    ->first();
                return [
                    'id'          => $chat->id,
                    'is_group'    => $chat->is_group,
                    'nombre'      => $chat->is_group ? $chat->nombre_grupo : ($other?->user?->username ?? 'Usuario'),
                    'foto'        => $other?->user?->foto_perfil ? '/storage/' . $other->user->foto_perfil : null,
                    'last_msg'    => $chat->lastMessage?->contenido ?? '',
                    'last_time'   => $chat->lastMessage?->created_at?->format('H:i') ?? '',
                    'unread'      => Message::where('chat_id', $chat->id)
                                        ->where('sender_id', '!=', Auth::id())
                                        ->where('leido', false)->count(),
                ];
            });

        return response()->json(['chats' => $chats], 200);
    }

    // Crear o encontrar chat privado entre dos usuarios
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'      => 'required_without:is_group|integer|exists:users,id',
            'is_group'     => 'nullable|boolean',
            'nombre_grupo' => 'nullable|string|max:100',
        ]);

        $isGroup = $data['is_group'] ?? false;

        if (!$isGroup) {
            $targetId = $data['user_id'];

            // Buscar chat privado existente entre los dos usuarios
            $existing = Chat::where('is_group', false)
                ->whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
                ->whereHas('participants', fn($q) => $q->where('user_id', $targetId))
                ->first();

            if ($existing) {
                return response()->json(['chat' => ['id' => $existing->id]], 200);
            }

            $chat = Chat::create(['is_group' => false]);
            ChatParticipant::create(['chat_id' => $chat->id, 'user_id' => Auth::id(), 'estado' => 'aceptado']);
            ChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $targetId, 'estado' => 'aceptado']);
        } else {
            $chat = Chat::create(['is_group' => true, 'nombre_grupo' => $data['nombre_grupo']]);
            ChatParticipant::create(['chat_id' => $chat->id, 'user_id' => Auth::id(), 'estado' => 'activo', 'is_admin' => true]);
        }

        return response()->json(['chat' => ['id' => $chat->id]], 201);
    }

    // Cargar mensajes de un chat
    public function show($id)
    {
        $chat = Chat::whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['messages.sender', 'participants.user'])
            ->findOrFail($id);

        // Marcar como leídos
        Message::where('chat_id', $id)
            ->where('sender_id', '!=', Auth::id())
            ->where('leido', false)
            ->update(['leido' => true, 'fecha_lectura' => now()]);

        $other = $chat->participants->where('user_id', '!=', Auth::id())->first();

        return response()->json([
            'chat' => [
                'id'            => $chat->id,
                'nombre'        => $chat->is_group ? $chat->nombre_grupo : ($other?->user?->username ?? 'Usuario'),
                'foto'          => $other?->user?->foto_perfil ? '/storage/' . $other->user->foto_perfil : null,
                'other_user_id' => $other?->user_id,
                'other_username' => $other?->user?->username,
                'messages'      => $chat->messages->map(fn($m) => [
                    'id'        => $m->id,
                    'mine'      => $m->sender_id === Auth::id(),
                    'tipo'      => $m->tipo_contenido,
                    'texto'     => $m->contenido,
                    'sender'    => $m->sender?->username,
                    'time'      => $m->created_at->format('H:i'),
                ]),
            ]
        ], 200);
    }

    // Enviar mensaje
    public function sendMessage(Request $request, $id)
    {
        $chat = Chat::whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
            ->with('participants')
            ->findOrFail($id);

        $request->validate([
            'contenido' => 'nullable|string|max:2000',
            'archivo'   => 'nullable|file|max:51200|mimes:jpeg,png,jpg,gif,mp4,mov,pdf,doc,docx,zip,rar',
        ]);

        $tipoContenido = 'texto';
        $contenido = $request->input('contenido', '');

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $mime = $file->getMimeType();
            $path = $file->store('chats', 'public');
            $contenido = $path;

            if (str_starts_with($mime, 'image/')) $tipoContenido = 'imagen';
            elseif (str_starts_with($mime, 'video/')) $tipoContenido = 'video';
            elseif ($mime === 'application/pdf') $tipoContenido = 'pdf';
            else $tipoContenido = 'archivo';
        }

        if (!$contenido) {
            return response()->json(['error' => 'Mensaje vacío'], 422);
        }

        $message = Message::create([
            'chat_id'        => $id,
            'sender_id'      => Auth::id(),
            'tipo_contenido' => $tipoContenido,
            'contenido'      => $contenido,
            'leido'          => false,
        ]);

        $chat->participants
        ->where('user_id', '!=', Auth::id())
        ->each(function($participant) use ($id) {
            $this->createNotification(
                $participant->user_id,
                'mensaje',
                'Nuevo mensaje',
                Auth::user()->username . ' te ha enviado un mensaje',
                '/chats/' . $id
            );
        });

        return response()->json([
            'message' => [
                'id'     => $message->id,
                'mine'   => true,
                'tipo'   => $tipoContenido,
                'texto'  => $message->contenido,
                'sender' => Auth::user()->username,
                'time'   => $message->created_at->format('H:i'),
            ]
        ], 201);
    }

    private function createNotification($userId, $tipo, $titulo, $mensaje, $enlaceUrl = null)
    {
        if ($userId === Auth::id()) return;

        \App\Models\Notification::create([
            'user_id'    => $userId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'enlace_url' => $enlaceUrl,
            'leida'      => false,
        ]);
    }
}