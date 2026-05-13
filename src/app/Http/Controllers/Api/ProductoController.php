<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerfilEmprendedor;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $productos = Producto::query()
            ->with(['categoria:id,nombre', 'perfilEmprendedor.usuario:id,name,email,rol'])
            ->when($request->filled('categoria_id'), fn ($query) => $query->where('categoria_id', $request->integer('categoria_id')))
            ->when($request->filled('estado_disponibilidad'), fn ($query) => $query->where('estado_disponibilidad', $request->string('estado_disponibilidad')))
            ->when($request->filled('usuario_id'), fn ($query) => $query->whereHas('perfilEmprendedor', fn ($perfil) => $perfil->where('usuario_id', $request->integer('usuario_id'))))
            ->latest()
            ->paginate(10);

        return response()->json($productos);
    }

    public function store(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $datos = $this->validarProducto($request, null);
        $datos['perfil_emprendedor_id'] = $this->resolverPerfilEmprendedorId($request, null);
        $datos['slug'] = $this->generarSlug($datos['nombre']);

        $producto = Producto::create($datos)->load(['categoria:id,nombre', 'perfilEmprendedor.usuario:id,name,email,rol']);

        return response()->json([
            'message' => 'Producto creado correctamente.',
            'data' => $producto,
        ], 201);
    }

    public function show(Producto $producto): JsonResponse
    {
        return response()->json([
            'data' => $producto->load(['categoria:id,nombre', 'perfilEmprendedor.usuario:id,name,email,rol']),
        ]);
    }

    public function update(Request $request, Producto $producto): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->tieneRol('emprendedor') && $producto->perfilEmprendedor?->usuario_id !== $usuario->id) {
            return response()->json([
                'message' => 'No puedes modificar productos de otro emprendedor.',
            ], 403);
        }

        $datos = $this->validarProducto($request, $producto);
        $datos['perfil_emprendedor_id'] = $this->resolverPerfilEmprendedorId($request, $producto);
        $datos['slug'] = $this->generarSlug($datos['nombre'], $producto->id);

        $producto->update($datos);

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'data' => $producto->fresh()->load(['categoria:id,nombre', 'perfilEmprendedor.usuario:id,name,email,rol']),
        ]);
    }

    public function destroy(Request $request, Producto $producto): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->tieneRol('emprendedor') && $producto->perfilEmprendedor?->usuario_id !== $usuario->id) {
            return response()->json([
                'message' => 'No puedes eliminar productos de otro emprendedor.',
            ], 403);
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ]);
    }

    private function validarProducto(Request $request, ?Producto $producto): array
    {
        $usuario = $request->user();

        $reglas = [
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:160'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'estado_disponibilidad' => ['nullable', Rule::in(['disponible', 'ultimas_unidades', 'agotado'])],
            'destacado' => ['nullable', 'boolean'],
            'publicado_at' => ['nullable', 'date'],
        ];

        if ($usuario->tieneRol('admin')) {
            $reglas['perfil_emprendedor_id'] = ['nullable', 'integer', 'exists:perfiles_emprendedores,id'];
        }

        $datos = $request->validate($reglas);

        $datos['stock'] = $datos['stock'] ?? ($producto?->stock ?? 0);
        $datos['estado_disponibilidad'] = $datos['estado_disponibilidad'] ?? ($producto?->estado_disponibilidad ?? 'disponible');
        $datos['destacado'] = $datos['destacado'] ?? ($producto?->destacado ?? false);
        $datos['publicado_at'] = $datos['publicado_at'] ?? ($producto?->publicado_at ?? now());

        if (! array_key_exists('perfil_emprendedor_id', $datos) && $producto) {
            $datos['perfil_emprendedor_id'] = $producto->perfil_emprendedor_id;
        }

        return $datos;
    }

    private function resolverPerfilEmprendedorId(Request $request, ?Producto $producto): int
    {
        $usuario = $request->user();

        if ($usuario->tieneRol('emprendedor')) {
            $perfilId = $usuario->perfilEmprendedor?->id;

            abort_unless($perfilId, 422, 'No existe un perfil de emprendedor asociado al usuario autenticado.');

            return $perfilId;
        }

        if ($request->filled('perfil_emprendedor_id')) {
            return $request->integer('perfil_emprendedor_id');
        }

        abort_if(! $producto, 422, 'Debes indicar un perfil_emprendedor_id para crear el producto.');

        return $producto->perfil_emprendedor_id;
    }

    private function generarSlug(string $nombre, ?int $productoId = null): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $contador = 1;

        while (Producto::query()->where('slug', $slug)->when($productoId, fn ($query) => $query->where('id', '!=', $productoId))->exists()) {
            $slug = $base.'-'.$contador;
            $contador++;
        }

        return $slug;
    }
}
