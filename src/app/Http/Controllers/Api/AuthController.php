<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerfilEmprendedor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'rol' => ['nullable', Rule::in(['usuario', 'comprador', 'emprendedor'])],
        ]);

        $rol = ($datos['rol'] ?? 'usuario') === 'emprendedor' ? 'emprendedor' : 'comprador';

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'rol' => $rol,
            'estado' => 'activo',
            'email_verified_at' => now(),
        ]);

        if ($rol === 'emprendedor') {
            PerfilEmprendedor::firstOrCreate([
                'usuario_id' => $usuario->id,
            ], [
                'nombre_emprendimiento' => $usuario->name,
                'pais' => 'Bolivia',
                'estado_aprobacion' => 'aprobado',
                'acepta_donaciones' => true,
            ]);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'token' => $token,
            'user' => $usuario,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $credenciales['email'])->first();

        if (! $usuario || ! Hash::check($credenciales['password'], $usuario->password)) {
            return response()->json([
                'message' => 'Las credenciales no son validas.',
            ], 401);
        }

        if ($usuario->estado !== 'activo') {
            return response()->json([
                'message' => 'La cuenta se encuentra inactiva.',
            ], 403);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Sesion iniciada correctamente.',
            'token' => $token,
            'user' => $usuario,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->currentAccessToken()) {
            $usuario->currentAccessToken()->delete();
        } else {
            $usuario->tokens()->delete();
        }

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }
}
