<?php

namespace App\Http\Controllers;

use App\Models\Pet;                         // El modelo que representa la tabla 'pets'
use Illuminate\Http\Request;               // Para recibir los datos del formulario
use Illuminate\Support\Facades\Auth;       // Para saber quién está logueado
use Illuminate\Support\Facades\Storage;    // Para manejar archivos (fotos)

// El controlador gestiona TODO lo relacionado con mascotas (listar, crear, editar, borrar)
class PetController extends Controller
{
    // INDEX — muestra la lista de mascotas del usuario logueado
    public function index()
    {
        // Coge las mascotas del usuario actual, ordenadas de la más nueva a la más antigua
        $pets = Auth::user()->pets()->orderByDesc('created_at')->get();
        // Manda los datos a la vista 'resources/views/pets/index.blade.php'
        return view('pets.index', compact('pets'));
    }

    // CREATE — muestra el formulario en blanco para crear una nueva mascota
    public function create()
    {
        return view('pets.create');
    }

    // STORE — recibe y guarda los datos del formulario de creación
    public function store(Request $request)
    {
        // Valida que los campos tengan el formato correcto antes de guardar nada
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'especie'     => ['nullable', 'string', 'max:50'],
            'raza'        => ['nullable', 'string', 'max:50'],
            'edad'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'foto'        => ['nullable', 'image', 'max:2048'], // Solo imágenes de máx. 2 MB
        ]);

        // Si el usuario ha subido una foto, la guardamos en storage/app/public/pets/
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pets', 'public');
        }

        // Asignamos el usuario actual como dueño de la mascota
        $data['user_id'] = Auth::id();

        // Creamos el registro en la base de datos con todos los datos de golpe
        Pet::create($data);

        // Redirigimos al listado con un mensaje de éxito
        return redirect()->route('mis-mascotas.index')->with('success', 'Mascota añadida correctamente.');
    }

    // SHOW — muestra el detalle de una mascota concreta con sus vacunas y recordatorios
    public function show(Pet $pet)
    {
        // Comprobamos que esta mascota es del usuario actual (seguridad)
        $this->authorize($pet);

        // Cargamos vacunas (de más nueva a más antigua) y recordatorios en la misma consulta
        $pet->load(['vaccines' => fn($q) => $q->orderBy('fecha_administracion', 'desc'), 'reminders']);
        return view('pets.show', compact('pet'));
    }

    // EDIT — muestra el formulario con los datos actuales para editar
    public function edit(Pet $pet)
    {
        $this->authorize($pet); // Solo el dueño puede editar
        return view('pets.edit', compact('pet'));
    }

    // UPDATE — recibe y guarda los cambios del formulario de edición
    public function update(Request $request, Pet $pet)
    {
        $this->authorize($pet);

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'especie'     => ['nullable', 'string', 'max:50'],
            'raza'        => ['nullable', 'string', 'max:50'],
            'edad'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'foto'        => ['nullable', 'image', 'max:2048'],
        ]);

        // Si el usuario sube una foto nueva, borramos la vieja para no acumular basura
        if ($request->hasFile('foto')) {
            if ($pet->foto) {
                Storage::disk('public')->delete($pet->foto); // Borra la foto antigua del disco
            }
            $data['foto'] = $request->file('foto')->store('pets', 'public'); // Guarda la nueva
        }

        // Actualizamos solo los campos que han cambiado
        $pet->update($data);

        return redirect()->route('mis-mascotas.show', $pet)->with('success', 'Mascota actualizada correctamente.');
    }

    // DESTROY — borra la mascota y su foto del disco
    public function destroy(Pet $pet)
    {
        $this->authorize($pet);

        // Si tenía foto guardada, la borramos también del disco (para no desperdiciar espacio)
        if ($pet->foto) {
            Storage::disk('public')->delete($pet->foto);
        }

        $pet->delete(); // Borra la mascota (y en cascada sus vacunas y recordatorios)

        return redirect()->route('mis-mascotas.index')->with('success', 'Mascota eliminada.');
    }

    // Método privado de seguridad: si la mascota no es del usuario logueado, devuelve error 403
    private function authorize(Pet $pet): void
    {
        abort_if($pet->user_id !== Auth::id(), 403); // abort_if = "para todo si esto es verdad"
    }
}
