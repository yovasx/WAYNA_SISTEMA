<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categorias = Categoria::query()
            ->withCount('productos')
            ->when($request->boolean('solo_activas'), fn ($query) => $query->where('activa', true))
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $categorias,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $categoria = Categoria::create([
            'nombre' => $datos['nombre'],
            'slug' => Str::slug($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'activa' => $datos['activa'] ?? true,
        ]);

        return response()->json([
            'message' => 'Categoria creada correctamente.',
            'data' => $categoria,
        ], 201);
    }

    public function show(Categoria $categoria): JsonResponse
    {
        $categoria->loadCount('productos');

        return response()->json([
            'data' => $categoria,
        ]);
    }

    public function update(Request $request, Categoria $categoria): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('categorias', 'nombre')->ignore($categoria->id)],
            'descripcion' => ['nullable', 'string'],
            'activa' => ['nullable', 'boolean'],
        ]);

        $categoria->update([
            'nombre' => $datos['nombre'],
            'slug' => Str::slug($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'activa' => $datos['activa'] ?? $categoria->activa,
        ]);

        return response()->json([
            'message' => 'Categoria actualizada correctamente.',
            'data' => $categoria->fresh()->loadCount('productos'),
        ]);
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        if ($categoria->productos()->exists()) {
            return response()->json([
                'message' => 'No puedes eliminar una categoria que tiene productos asociados.',
            ], 409);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoria eliminada correctamente.',
        ]);
    }
}
