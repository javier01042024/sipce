<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
public function index()
{
    $users = User::with('roles', 'paciente')->latest()->get();
    $roles = Role::all();
    $totalUsers = User::count();
    $adminUsers = User::whereHas('roles', function ($q) {
        $q->where('slug', 'admin');
    })->count();
    $activeUsers = User::whereNotNull('email_verified_at')->count();

    $pacientesSinUsuario = Paciente::whereNull('user_id')->get();

    foreach ($pacientesSinUsuario as $p) {
        $nombreCompleto = 'Sin nombre';
        
        if ($p->paciente_detalle_type && $p->paciente_detalle_id) {
            // Usamos directamente la tabla según el tipo para evitar fallos de Eloquent
            $tabla = '';
            if ($p->paciente_detalle_type === 'App\Models\PacienteAdulto') {
                $tabla = 'paciente_adultos'; // O el nombre exacto de tu tabla de adultos
            } elseif ($p->paciente_detalle_type === 'App\Models\PacienteAdolescente') {
                $tabla = 'paciente_adolescentes';
            } elseif ($p->paciente_detalle_type === 'App\Models\PacienteNino') {
                $tabla = 'paciente_ninos';
            }

            if (!empty($tabla)) {
                $detalle = \Illuminate\Support\Facades\DB::table($tabla)->where('id', $p->paciente_detalle_id)->first();
                if ($detalle) {
                    $nombreCompleto = trim($detalle->nombre . ' ' . $detalle->apellido);
                }
            }
        }
        
        $p->nombre_completo = $nombreCompleto;
    }

    return view('configuracion.usuarios.index', compact(
        'users',
        'roles',
        'totalUsers',
        'adminUsers',
        'activeUsers',
        'pacientesSinUsuario'
    ));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'es_paciente' => 'sometimes|boolean',
            'paciente_id' => 'required_if:es_paciente,true|exists:pacientes,id'
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios',
            'name.required' => 'El nombre completo es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.required' => 'Debes seleccionar un rol para el usuario',
            'paciente_id.required_if' => 'Debes seleccionar un paciente para vincular'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            $user->assignRole(Role::find($validated['role']));

            if ($request->has('es_paciente') && $request->es_paciente && $request->filled('paciente_id')) {
                $paciente = Paciente::find($request->paciente_id);
                if ($paciente && !$paciente->user_id) {
                    $paciente->user_id = $user->id;
                    $paciente->save();

                    $detalle = app($paciente->paciente_detalle_type)->find($paciente->paciente_detalle_id);
                    if ($detalle) {
                        $user->update(['name' => $detalle->nombre . ' ' . $detalle->apellido]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user' => $user->fresh()->load('roles', 'paciente')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('roles', 'paciente.detalle')->findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'es_paciente' => 'sometimes|boolean',
            'paciente_id' => 'required_if:es_paciente,true|exists:pacientes,id'
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios',
            'name.required' => 'El nombre completo es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'Este correo ya está registrado por otro usuario',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.required' => 'Debes seleccionar un rol para el usuario',
            'paciente_id.required_if' => 'Debes seleccionar un paciente para vincular'
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $user->roles()->sync([$validated['role']]);

            $pacienteActual = $user->paciente;

            if ($request->has('es_paciente') && $request->es_paciente && $request->filled('paciente_id')) {
                if ($pacienteActual && $pacienteActual->id != $request->paciente_id) {
                    $pacienteActual->user_id = null;
                    $pacienteActual->save();
                }

                $nuevoPaciente = Paciente::find($request->paciente_id);
                if ($nuevoPaciente && !$nuevoPaciente->user_id) {
                    $nuevoPaciente->user_id = $user->id;
                    $nuevoPaciente->save();

                    $detalle = app($nuevoPaciente->paciente_detalle_type)->find($nuevoPaciente->paciente_detalle_id);
                    if ($detalle) {
                        $user->update(['name' => $detalle->nombre . ' ' . $detalle->apellido]);
                    }
                }
            } else {
                if ($pacienteActual) {
                    $pacienteActual->user_id = null;
                    $pacienteActual->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $user->fresh()->load('roles', 'paciente')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::check() && $user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 403);
        }

        if ($user->hasRole('admin')) {
            $adminCount = User::whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })->count();

            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar al último administrador del sistema'
                ], 403);
            }
        }

        DB::beginTransaction();

        try {
            if ($user->paciente) {
                $user->paciente->user_id = null;
                $user->paciente->save();
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (Auth::check() && $user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propio usuario'
            ], 403);
        }

        try {
            if ($user->email_verified_at) {
                $user->update(['email_verified_at' => null]);
                $message = 'Usuario desactivado exitosamente';
            } else {
                $user->update(['email_verified_at' => now()]);
                $message = 'Usuario activado exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => $user->fresh()->load('roles', 'paciente')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRoles()
    {
        try {
            $roles = Role::all();

            return response()->json([
                'success' => true,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar roles'
            ], 500);
        }
    }
}
