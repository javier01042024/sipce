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
    /**
     * Muestra el listado de todos los usuarios del sistema.
     * Incluye estadísticas generales para el panel de administración.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todos los usuarios con sus roles y relación con paciente
        // Ordenados del más reciente al más antiguo
        $users = User::with('roles', 'paciente')->latest()->get();
        
        // Obtener todos los roles disponibles para los filtros
        $roles = Role::all();
        
        // ============================================
        // ESTADÍSTICAS PARA EL PANEL
        // ============================================
        
        // Total de usuarios registrados en el sistema
        $totalUsers = User::count();
        
        // Usuarios con rol de administrador
        $adminUsers = User::whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->count();
        
        // Usuarios activos (con email verificado)
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        
        // Retornar vista con todas las variables necesarias
        return view('configuracion.usuarios.index', compact(
            'users', 'roles', 'totalUsers', 'adminUsers', 'activeUsers'
        ));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Crea el usuario, asigna un rol y opcionalmente lo vincula con un paciente.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', // Solo letras y espacios
            'email' => 'required|email|unique:users,email',                         // Email único en la tabla users
            'password' => 'required|min:8|confirmed',                               // Mínimo 8 caracteres, debe coincidir con confirmación
            'role' => 'required|exists:roles,id',                                   // El rol debe existir en la tabla roles
            'es_paciente' => 'sometimes|boolean',                                   // Campo opcional: indica si es paciente
            'paciente_id' => 'required_if:es_paciente,true|exists:pacientes,id'     // Requerido solo si es_paciente es true
        ], [
            // Mensajes de validación personalizados en español
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

        // Iniciar transacción para asegurar que todas las operaciones se completen
        DB::beginTransaction();
        
        try {
            // 1. Crear el usuario con los datos básicos
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Encriptar contraseña
                'email_verified_at' => now(), // Marcar email como verificado automáticamente
            ]);

            // 2. Asignar el rol seleccionado al usuario recién creado
            $user->assignRole(Role::find($validated['role']));
            
            // 3. Si el usuario es un paciente, vincularlo con su registro clínico
            if ($request->has('es_paciente') && $request->es_paciente && $request->filled('paciente_id')) {
                $paciente = Paciente::find($request->paciente_id);
                // Solo vincular si el paciente existe y no tiene otro usuario asignado
                if ($paciente && !$paciente->user_id) {
                    $paciente->user_id = $user->id;
                    $paciente->save();
                }
            }
            
            // Confirmar la transacción: todos los cambios se guardan
            DB::commit();

            // Respuesta exitosa en formato JSON
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user' => $user->load('roles', 'paciente') // Cargar relaciones para la respuesta
            ]);
            
        } catch (\Exception $e) {
            // Revertir todos los cambios si ocurrió algún error
            DB::rollBack();
            
            // Retornar mensaje de error
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    /**
     * Muestra los datos de un usuario específico.
     * Incluye sus roles y la relación con paciente.
     * 
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Buscar usuario por ID con sus relaciones o lanzar error 404
            $user = User::with('roles', 'paciente')->findOrFail($id);
            
            // Retornar datos del usuario en formato JSON
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Usuario no encontrado
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404); // Código HTTP 404: No encontrado
        }
    }

    /**
     * Actualiza los datos de un usuario existente.
     * Permite cambiar nombre, email, contraseña, rol y vinculación con paciente.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id ID del usuario a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Buscar el usuario a actualizar o lanzar error 404
        $user = User::findOrFail($id);
        
        // Validar los datos (email único excepto el del propio usuario)
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)], // Ignorar el email del usuario actual
            'password' => 'nullable|min:8|confirmed', // Contraseña opcional en actualización
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

        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // 1. Actualizar datos básicos del usuario (nombre y email)
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // 2. Si se proporcionó una nueva contraseña, actualizarla
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // 3. Sincronizar el rol del usuario (elimina roles anteriores y asigna el nuevo)
            $user->roles()->sync([$validated['role']]);
            
            // 4. Manejar la vinculación/desvinculación con el paciente
            $pacienteActual = $user->paciente; // Paciente actualmente vinculado
            
            // Si el usuario debe ser paciente (checkbox marcado)
            if ($request->has('es_paciente') && $request->es_paciente && $request->filled('paciente_id')) {
                // Si ya tenía otro paciente vinculado diferente, desvincularlo primero
                if ($pacienteActual && $pacienteActual->id != $request->paciente_id) {
                    $pacienteActual->user_id = null;
                    $pacienteActual->save();
                }
                
                // Vincular el nuevo paciente seleccionado
                $nuevoPaciente = Paciente::find($request->paciente_id);
                if ($nuevoPaciente && !$nuevoPaciente->user_id) {
                    $nuevoPaciente->user_id = $user->id;
                    $nuevoPaciente->save();
                }
            } else {
                // Si ya no es paciente, desvincular cualquier paciente asociado
                if ($pacienteActual) {
                    $pacienteActual->user_id = null;
                    $pacienteActual->save();
                }
            }
            
            // Confirmar la transacción
            DB::commit();

            // Retornar respuesta exitosa con datos actualizados
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $user->fresh()->load('roles', 'paciente') // Obtener datos frescos de la BD
            ]);
            
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un usuario del sistema.
     * Incluye validaciones de seguridad:
     * - No permite eliminarse a sí mismo
     * - No permite eliminar al último administrador
     * - Desvincula al paciente asociado antes de eliminar
     * 
     * @param int $id ID del usuario a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Buscar el usuario a eliminar
        $user = User::findOrFail($id);
        
        // Verificar que el usuario no se esté eliminando a sí mismo
        if (Auth::check() && $user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 403); // Código HTTP 403: Acceso prohibido
        }

        // Verificar que no sea el último administrador del sistema
        if ($user->hasRole('admin')) {
            $adminCount = User::whereHas('roles', function($q) {
                $q->where('slug', 'admin');
            })->count();
            
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar al último administrador del sistema'
                ], 403);
            }
        }

        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Si el usuario está vinculado a un paciente, desvincularlo antes de eliminar
            if ($user->paciente) {
                $user->paciente->user_id = null;
                $user->paciente->save();
            }
            
            // Eliminar el usuario de la base de datos
            $user->delete();
            
            // Confirmar la transacción
            DB::commit();

            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activa o desactiva un usuario cambiando el estado de verificación de email.
     * - Si tiene email_verified_at, lo desactiva (establece como null)
     * - Si no tiene email_verified_at, lo activa (establece fecha actual)
     * No permite desactivar al propio usuario.
     * 
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus($id)
    {
        // Buscar el usuario
        $user = User::findOrFail($id);
        
        // No permitir desactivar al propio usuario que está logueado
        if (Auth::check() && $user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propio usuario'
            ], 403);
        }
        
        try {
            // Verificar el estado actual y cambiarlo
            if ($user->email_verified_at) {
                // Si está verificado, desactivar usuario (eliminar fecha de verificación)
                $user->update(['email_verified_at' => null]);
                $message = 'Usuario desactivado exitosamente';
            } else {
                // Si no está verificado, activar usuario (establecer fecha actual)
                $user->update(['email_verified_at' => now()]);
                $message = 'Usuario activado exitosamente';
            }

            // Retornar respuesta con el nuevo estado
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => $user->fresh() // Datos actualizados del usuario
            ]);
            
        } catch (\Exception $e) {
            // Error al cambiar el estado
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtiene la lista de roles disponibles para los selectores.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles()
    {
        try {
            // Obtener todos los roles del sistema
            $roles = Role::all();
            
            // Retornar en formato JSON
            return response()->json([
                'success' => true,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            // Error al cargar los roles
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar roles'
            ], 500);
        }
    }
}