<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetImage;
use App\Models\PetVaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $pets = Pet::where('user_id', Auth::id())
            ->with(['images', 'vaccines'])
            ->get();
        return response()->json(['pets' => $pets], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'especie'     => 'nullable|string|max:50',
            'raza'        => 'nullable|string|max:50',
            'edad'        => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'vaccines'    => 'nullable|array',
            'vaccines.*'  => 'nullable|string|max:100',
        ]);

        $petData = array_filter([
            'user_id'     => Auth::id(),
            'nombre'      => $data['nombre'],
            'especie'     => $data['especie'] ?? null,
            'raza'        => $data['raza'] ?? null,
            'edad'        => $data['edad'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
        ], fn($v) => $v !== null);

        $petData['user_id'] = Auth::id();

        $pet = Pet::create($petData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store("pets/{$pet->id}/images", 'public');
                if ($index === 0) {
                    $pet->update(['foto' => $path]);
                }
                PetImage::create([
                    'pet_id' => $pet->id,
                    'url'    => $path,
                    'orden'  => $index,
                ]);
            }
        }

        if (!empty($data['vaccines'])) {
            foreach ($data['vaccines'] as $vaccineName) {
                if (!empty(trim($vaccineName))) {
                    PetVaccine::create([
                        'pet_id'               => $pet->id,
                        'nombre_vacuna'        => trim($vaccineName),
                        'fecha_administracion' => now()->toDateString(),
                    ]);
                }
            }
        }

        return response()->json(['pet' => $pet->load(['images', 'vaccines'])], 201);
    }

    public function show($id)
    {
        $pet = Pet::with(['images', 'vaccines', 'reminders', 'user'])->findOrFail($id);
        $isOwner = Auth::check() && Auth::id() === $pet->user_id;

        $petData = [
            'id'          => $pet->id,
            'nombre'      => $pet->nombre,
            'especie'     => $pet->especie,
            'raza'        => $pet->raza,
            'edad'        => $pet->edad,
            'descripcion' => $pet->descripcion,
            'foto_url'    => $pet->foto_url,
            'images'      => $pet->images->map(fn($img) => [
                'id'  => $img->id,
                'url' => asset('storage/' . $img->url),
            ]),
            'is_owner'    => $isOwner,
            'user_id'     => $pet->user_id,
        ];

        if ($isOwner) {
            $petData['vaccines']  = $pet->vaccines->map(fn($v) => [
                'id'                    => $v->id,
                'nombre_vacuna'         => $v->nombre_vacuna,
                'fecha_administracion'  => $v->fecha_administracion?->format('d/m/Y'),
                'proxima_dosis'         => $v->proxima_dosis?->format('d/m/Y'),
            ]);
            $petData['reminders'] = $pet->reminders->map(fn($r) => [
                'id'           => $r->id,
                'titulo'       => $r->titulo,
                'mensaje'      => $r->mensaje,
                'fecha_alarma' => $r->fecha_alarma?->format('d/m/Y H:i'),
            ]);
        }

        return response()->json(['pet' => $petData], 200);
    }

    public function update(Request $request, $id)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'nombre'          => 'sometimes|required|string|max:100',
            'especie'         => 'nullable|string|max:50',
            'raza'            => 'nullable|string|max:50',
            'edad'            => 'nullable|integer|min:0',
            'descripcion'     => 'nullable|string',
            'new_images'      => 'nullable|array|max:5',
            'new_images.*'    => 'mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'remove_images'   => 'nullable|array',
            'remove_images.*' => 'integer',
            'vaccines'        => 'nullable|array',
            'vaccines.*'      => 'nullable|string|max:100',
        ]);

        $pet->update(array_intersect_key($data, array_flip(['nombre','especie','raza','edad','descripcion'])));

        if (!empty($data['remove_images'])) {
            $imagesToRemove = PetImage::where('pet_id', $pet->id)
                ->whereIn('id', $data['remove_images'])
                ->get();
            foreach ($imagesToRemove as $img) {
                Storage::disk('public')->delete($img->url);
                $img->delete();
            }
        }

        if ($request->hasFile('new_images')) {
            $maxOrden = PetImage::where('pet_id', $pet->id)->max('orden') ?? -1;
            foreach ($request->file('new_images') as $index => $image) {
                $path = $image->store("pets/{$pet->id}/images", 'public');
                PetImage::create([
                    'pet_id' => $pet->id,
                    'url'    => $path,
                    'orden'  => $maxOrden + $index + 1,
                ]);
            }
        }

        $firstImage = PetImage::where('pet_id', $pet->id)->orderBy('orden')->first();
        if ($firstImage) {
            $pet->update(['foto' => $firstImage->url]);
        } elseif (!empty($data['remove_images'])) {
            $pet->update(['foto' => null]);
        }

        if (isset($data['vaccines'])) {
            $pet->vaccines()->delete();
            foreach ($data['vaccines'] as $vaccineName) {
                if (!empty(trim($vaccineName))) {
                    PetVaccine::create([
                        'pet_id'               => $pet->id,
                        'nombre_vacuna'        => trim($vaccineName),
                        'fecha_administracion' => now()->toDateString(),
                    ]);
                }
            }
        }

        return response()->json(['pet' => $pet->load(['images', 'vaccines'])], 200);
    }

    public function destroy($id)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($id);
        foreach ($pet->images as $image) {
            Storage::disk('public')->delete($image->url);
        }
        $pet->delete();
        return response()->json(['message' => 'Mascota eliminada correctamente.'], 200);
    }
}