<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('reporter_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['reports' => $reports], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reported_user_id' => 'nullable|integer|exists:users,id',
            'tipo_entidad' => ['required', Rule::in(['perfil', 'post', 'post_comentario', 'perfil_comentario', 'mensaje_chat'])],
            'entidad_id' => 'required|integer',
            'motivo' => 'required|string',
            'estado' => ['nullable', Rule::in(['pendiente', 'en_revision', 'aceptado', 'rechazado'])],
        ]);

        $data['reporter_id'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'pendiente';

        $report = Report::create($data);
        return response()->json(['report' => $report], 201);
    }

    public function show($id)
    {
        $report = Report::where('reporter_id', Auth::id())->findOrFail($id);
        return response()->json(['report' => $report], 200);
    }

    public function update(Request $request, $id)
    {
        $report = Report::where('reporter_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'motivo' => 'nullable|string',
            'estado' => ['nullable', Rule::in(['pendiente', 'en_revision', 'aceptado', 'rechazado'])],
        ]);

        $report->update($data);
        return response()->json(['report' => $report], 200);
    }

    public function destroy($id)
    {
        $report = Report::where('reporter_id', Auth::id())->findOrFail($id);
        $report->delete();
        return response()->json(['message' => __('Report deleted successfully.')], 200);
    }
}
