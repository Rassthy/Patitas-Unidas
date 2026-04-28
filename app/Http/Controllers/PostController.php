<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return response()->json(['posts' => $posts], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer|exists:forum_categories,id',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'animal_nombre' => 'nullable|string|max:100',
            'animal_especie' => 'nullable|string|max:50',
            'animal_raza' => 'nullable|string|max:50',
            'provincia' => 'required|string|max:50',
            'ciudad' => 'required|string|max:100',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'estado' => ['required', Rule::in(['activa', 'en_revision', 'cerrada'])],
        ]);

        $data['author_id'] = Auth::id();
        $post = Post::create($data);

        return response()->json(['post' => $post], 201);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json(['post' => $post], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('author_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'category_id' => 'sometimes|required|integer|exists:forum_categories,id',
            'titulo' => 'sometimes|required|string|max:200',
            'descripcion' => 'sometimes|required|string',
            'animal_nombre' => 'nullable|string|max:100',
            'animal_especie' => 'nullable|string|max:50',
            'animal_raza' => 'nullable|string|max:50',
            'provincia' => 'sometimes|required|string|max:50',
            'ciudad' => 'sometimes|required|string|max:100',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'estado' => ['sometimes', Rule::in(['activa', 'en_revision', 'cerrada'])],
        ]);

        $post->update($data);
        return response()->json(['post' => $post], 200);
    }

    public function destroy($id)
    {
        $post = Post::where('author_id', Auth::id())->findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }
}
