@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="profile-card">
        <h1>{{ $post->titulo }}</h1>
        <p class="meta-item">Publicado por @ {{ $post->author->username }}</p>
        <hr>
        <p>{{ $post->descripcion }}</p>
        
        @if($post->images->count() > 0)
            <div class="post-images" style="display: flex; gap: 10px; flex-wrap: wrap;">
                @foreach($post->images as $img)
                    <img src="{{ asset('storage/' . $img->url) }}" style="width: 200px; border-radius: 8px;">
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection