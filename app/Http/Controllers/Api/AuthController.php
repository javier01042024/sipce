<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Autentica al usuario y devuelve un token Sanctum + sus roles y permisos.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        $deviceName = $request->input('device_name', 'sipce-mobile');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->usuarioConPermisos($user),
        ]);
    }

    /**
     * POST /api/logout
     * Revoca el token actual del dispositivo.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/user
     * Devuelve el usuario autenticado con sus roles y permisos.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $this->usuarioConPermisos($request->user()),
        ]);
    }

    /**
     * Serializar el usuario con roles y permisos unificados.
     */
    private function usuarioConPermisos(User $user): array
    {
        $roles = $user->roles()
            ->get(['id', 'name', 'slug'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ])
            ->values()
            ->all();

        $permissions = $user->roles
            ->pluck('permissions')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }
}