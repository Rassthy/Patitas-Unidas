<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\CommentLike;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category', 'images'])->whereIn('estado', ['activa', 'activo']);

        // Filtros
        if ($request->has('category_id') && $request->category_id && $request->category_id != 0) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('provincia') && $request->provincia) {
            $query->where('provincia', $request->provincia);
        }
        if ($request->has('ciudad') && $request->ciudad) {
            $query->where('ciudad', $request->ciudad);
        }
        if ($request->has('animal_especie') && $request->animal_especie) {
            $query->where('animal_especie', $request->animal_especie);
        }
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                  ->orWhere('animal_nombre', 'like', '%' . $request->search . '%');
            });
        }

        match($request->sort ?? 'recientes') {
            'antiguas'  => $query->orderBy('created_at', 'asc'),
            'populares' => $query->withCount('likes')->orderBy('likes_count', 'desc'),
            default     => $query->orderBy('created_at', 'desc'),
        };

        $posts = $query->paginate(12);

        return response()->json(['posts' => $posts], 200);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = Auth::id();
        $post = Post::create($data);

        // Manejar imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts', 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'url' => $path,
                    'orden' => $index,
                ]);
            }
        }

        return response()->json(['post' => $post->load(['author', 'category', 'images'])], 201);
    }

    public function show($id)
    {
        if (request()->ajax()) {
            $post = Post::with(['author', 'category', 'images', 'comments.user'])->findOrFail($id);
            return response()->json(['post' => $post]);
        }

        return redirect('/?open_post=' . $id);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::where('author_id', Auth::id())->findOrFail($id);
        $data = $request->validated();
        $post->update($data);
        return response()->json(['post' => $post], 200);
    }

    public function destroy($id)
    {
        $post = Post::where('author_id', Auth::id())->findOrFail($id);

        // Eliminar imágenes
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->url);
            $image->delete();
        }

        $post->delete();
        return response()->json(['message' => __('Post deleted successfully.')], 200);
    }

    public function toggleLike($id)
    {
        $post = Post::findOrFail($id);
        $like = PostLike::where('post_id', $id)->where('user_id', Auth::id())->first();

        if ($like) {
            PostLike::where('post_id', $id)->where('user_id', Auth::id())->delete();
            return response()->json(['liked' => false, 'likes_count' => $post->likes()->count()], 200);
        } else {
            PostLike::create(['post_id' => $id, 'user_id' => Auth::id()]);

            // Notificar al autor del post
            $this->createNotification(
                $post->author_id,
                'like',
                __('Nuevo like'),
                __(':usuario le dio like a tu publicación: :titulo', ['usuario' => Auth::user()->username, 'titulo' => $post->titulo]),
                '/posts/' . $id
            );

            return response()->json(['liked' => true, 'likes_count' => $post->likes()->count()], 200);
        }
    }

    public function addComment(Request $request, $id)
    {
        $data = $request->validate([
            'comentario'       => 'required|string|max:500',
            'parent_comment_id' => 'nullable|integer|exists:post_comments,id',
        ]);

        $comment = PostComment::create([
            'post_id'           => $id,
            'author_id'         => Auth::id(),
            'parent_comment_id' => $data['parent_comment_id'] ?? null,
            'comentario'        => $data['comentario'],
        ]);

        $post = Post::findOrFail($id);

        if ($data['parent_comment_id'] ?? null) {
            // Es una respuesta — notificar al autor del comentario padre
            $parentComment = PostComment::findOrFail($data['parent_comment_id']);
            $this->createNotification(
                $parentComment->author_id,
                'comentario_post',
                __('Nueva respuesta'),
                __(':usuario respondió a tu comentario: ":comentario"', ['usuario' => Auth::user()->username, 'comentario' => substr($parentComment->comentario, 0, 50)]),
                '/posts/' . $id
            );
        } else {
            // Es un comentario — notificar al autor del post
            $this->createNotification(
                $post->author_id,
                'comentario_post',
                __('Nuevo comentario'),
                __(':usuario comentó en tu publicación: :titulo', ['usuario' => Auth::user()->username, 'titulo' => $post->titulo]),
                '/posts/' . $id
            );
        }

        return response()->json(['comment' => $comment->load('user')], 201);
    }

    public function destroyComment($id)
    {
    $comment = PostComment::where('id', $id)
        ->where('author_id', Auth::id())
        ->firstOrFail();
    $comment->delete();
    return response()->json(['message' => __('Comentario eliminado')], 200);
    }

    public function toggleCommentLike($id)
    {
        $comment = PostComment::findOrFail($id);
        $existing = CommentLike::where('comment_id', $id)->where('user_id', Auth::id())->first();

        if ($existing) {
            CommentLike::where('comment_id', $id)->where('user_id', Auth::id())->delete();
            return response()->json(['liked' => false, 'likes_count' => $comment->likes()->count()], 200);
        } else {
            CommentLike::create(['comment_id' => $id, 'user_id' => Auth::id()]);

            // Notificar al autor del comentario
            $this->createNotification(
                $comment->author_id,
                'like',
                __('Like en comentario'),
                __(':usuario le dio like a tu comentario: ":comentario"', ['usuario' => Auth::user()->username, 'comentario' => substr($comment->comentario, 0, 50)]),
                '/posts/' . $comment->post_id
            );

            return response()->json(['liked' => true, 'likes_count' => $comment->likes()->count()], 200);
        }
    }

    public function getComments($id)
    {
        $comments = PostComment::where('post_id', $id)
            ->whereNull('parent_comment_id')
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $userId = Auth::id();
        $comments->each(function($comment) use ($userId) {
            $comment->likes_count = $comment->likes()->count();
            $comment->liked_by_user = $userId ? $comment->isLikedBy($userId) : false;

            $comment->replies->each(function($reply) use ($userId) {
                $reply->likes_count   = $reply->likes()->count();
                $reply->liked_by_user = $userId ? $reply->isLikedBy($userId) : false;
            });
        });

        return response()->json(['comments' => $comments], 200);
    }

    private function createNotification($userId, $tipo, $titulo, $mensaje, $enlaceUrl = null)
    {
        // No notificar a uno mismo
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
