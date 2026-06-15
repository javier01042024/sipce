<?php
// app/Http/Controllers/Admin/RoleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Muestra el listado de todos los roles con su cantidad de usuarios.
     * También retorna los permisos disponibles en el sistema.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener roles con el conteo de usuarios asignados
        $roles = Role::withCount('users')->get();
        
        // Obtener lista de permisos disponibles agrupados por módulo
        $availablePermissions = $this->getAvailablePermissions();
        
        // Retornar respuesta JSON con roles y permisos
        return response()->json([
            'success' => true,
            'roles' => $roles,
            'availablePermissions' => $availablePermissions
        ]);
    }

    /**
     * Muestra los detalles de un rol específico.
     * Incluye el conteo de usuarios y los permisos disponibles.
     * 
     * @param int $id ID del rol
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Buscar el rol con conteo de usuarios o lanzar error 404
        $role = Role::withCount('users')->findOrFail($id);
        
        // Retornar respuesta JSON con los detalles del rol
        return response()->json([
            'success' => true,
            'role' => $role,
            'availablePermissions' => $this->getAvailablePermissions()
        ]);
    }

    /**
     * Crea un nuevo rol en el sistema.
     * Valida que el nombre y slug sean únicos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',         // Nombre único del rol
            'slug' => 'required|string|max:50|unique:roles,slug|alpha_dash', // Slug único, solo letras, números y guiones
            'description' => 'nullable|string|max:255',                    // Descripción opcional
            'permissions' => 'nullable|array',                             // Lista de permisos (opcional)
            'permissions.*' => 'string',                                   // Cada permiso debe ser un string
        ]);

        // Crear el rol con los datos validados
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,            // Si no hay descripción, guardar null
            'permissions' => $validated['permissions'] ?? [],              // Si no hay permisos, guardar arreglo vacío
        ]);

        // Retornar respuesta exitosa con el rol creado
        return response()->json([
            'success' => true,
            'message' => "Rol '{$role->name}' creado exitosamente",
            'role' => $role->loadCount('users') // Cargar conteo de usuarios
        ]);
    }

    /**
     * Actualiza un rol existente.
     * Protege el rol de administrador contra modificaciones.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id ID del rol a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Buscar el rol o lanzar error 404
        $role = Role::findOrFail($id);
        
        // Proteger el rol de administrador: no se puede modificar
        if ($role->slug === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar el rol de administrador'
            ], 403); // Código HTTP 403: Acceso prohibido
        }

        // Validar los datos del formulario (ignorando el registro actual en reglas unique)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        // Actualizar el rol con los datos validados
        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        // Retornar respuesta exitosa con el rol actualizado
        return response()->json([
            'success' => true,
            'message' => "Rol '{$role->name}' actualizado exitosamente",
            'role' => $role->fresh()->loadCount('users') // Obtener datos frescos con conteo de usuarios
        ]);
    }

    /**
     * Elimina un rol del sistema.
     * Protege el rol de administrador y verifica que no tenga usuarios asignados.
     * 
     * @param int $id ID del rol a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Buscar el rol o lanzar error 404
        $role = Role::findOrFail($id);
        
        // Proteger el rol de administrador: no se puede eliminar
        if ($role->slug === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el rol de administrador'
            ], 403); // Código HTTP 403: Acceso prohibido
        }

        // Verificar que el rol no tenga usuarios asignados
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el rol porque tiene {$role->users()->count()} usuario(s) asignado(s)"
            ], 403);
        }

        // Eliminar el rol
        $role->delete();

        // Retornar respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => "Rol '{$role->name}' eliminado exitosamente"
        ]);
    }

    /**
     * Obtiene la lista de permisos disponibles en el sistema.
     * Los permisos están agrupados por módulos y coinciden con las rutas reales.
     * Cada permiso tiene un nombre descriptivo y un slug que corresponde a la ruta.
     * 
     * @return array Arreglo de módulos con sus respectivos permisos
     */
    private function getAvailablePermissions(): array
    {
        return [
            // Módulo: Dashboard (Panel principal)
            [
                'group' => 'Dashboard',
                'icon' => 'fa-tachometer-alt', // Icono de Font Awesome
                'permissions' => [
                    ['name' => 'Ver dashboard', 'slug' => 'dashboard'],
                ]
            ],
            // Módulo: Perfil de usuario
            [
                'group' => 'Perfil',
                'icon' => 'fa-user-circle',
                'permissions' => [
                    ['name' => 'Editar perfil', 'slug' => 'profile.edit'],
                    ['name' => 'Actualizar perfil', 'slug' => 'profile.update'],
                    ['name' => 'Eliminar cuenta', 'slug' => 'profile.destroy'],
                ]
            ],
            // Módulo: Gestión de pacientes
            [
                'group' => 'Pacientes',
                'icon' => 'fa-hospital-user', // Icono de paciente de hospital
                'permissions' => [
                    ['name' => 'Ver lista de pacientes', 'slug' => 'pacientes.index'],
                    ['name' => 'Crear paciente', 'slug' => 'pacientes.create'],
                    ['name' => 'Ver detalle de paciente', 'slug' => 'pacientes.show'],
                    ['name' => 'Editar paciente', 'slug' => 'pacientes.edit'],
                    ['name' => 'Eliminar paciente', 'slug' => 'pacientes.destroy'],
                ]
            ],
            // Módulo: Gestión de citas médicas
            [
                'group' => 'Citas',
                'icon' => 'fa-calendar-check', // Icono de calendario con verificación
                'permissions' => [
                    ['name' => 'Ver lista de citas', 'slug' => 'citas.index'],
                    ['name' => 'Crear cita', 'slug' => 'citas.create'],
                    ['name' => 'Ver detalle de cita', 'slug' => 'citas.show'],
                    ['name' => 'Editar cita', 'slug' => 'citas.edit'],
                    ['name' => 'Eliminar cita', 'slug' => 'citas.destroy'],
                ]
            ],
            // Módulo: Registros diarios (diario médico)
            [
                'group' => 'Diarios',
                'icon' => 'fa-book-medical', // Icono de libro médico
                'permissions' => [
                    ['name' => 'Ver lista de diarios', 'slug' => 'diarios.index'],
                    ['name' => 'Crear diario', 'slug' => 'diarios.create'],
                    ['name' => 'Ver detalle de diario', 'slug' => 'diarios.show'],
                    ['name' => 'Editar diario', 'slug' => 'diarios.edit'],
                    ['name' => 'Eliminar diario', 'slug' => 'diarios.destroy'],
                ]
            ],
            // Módulo: Administración de usuarios del sistema
            [
                'group' => 'Usuarios',
                'icon' => 'fa-users', // Icono de grupo de usuarios
                'permissions' => [
                    ['name' => 'Ver usuarios', 'slug' => 'usuarios.index'],
                    ['name' => 'Crear usuario', 'slug' => 'usuarios.create'],
                    ['name' => 'Ver detalle de usuario', 'slug' => 'usuarios.show'],
                    ['name' => 'Editar usuario', 'slug' => 'usuarios.edit'],
                    ['name' => 'Eliminar usuario', 'slug' => 'usuarios.destroy'],
                    ['name' => 'Activar/Desactivar usuario', 'slug' => 'usuarios.toggle-status'], // Permiso especial
                ]
            ],
            // Módulo: Gestión de roles y permisos
            [
                'group' => 'Roles',
                'icon' => 'fa-user-tag', // Icono de etiqueta de usuario
                'permissions' => [
                    ['name' => 'Ver roles', 'slug' => 'roles.index'],
                    ['name' => 'Crear rol', 'slug' => 'roles.create'],
                    ['name' => 'Ver detalle de rol', 'slug' => 'roles.show'],
                    ['name' => 'Editar rol', 'slug' => 'roles.edit'],
                    ['name' => 'Eliminar rol', 'slug' => 'roles.destroy'],
                ]
            ],
            // Módulo: Configuración del sistema
            [
                'group' => 'Configuración',
                'icon' => 'fa-cog', // Icono de engranaje
                'permissions' => [
                    ['name' => 'Ver respaldos', 'slug' => 'respaldos.index'],  // Acceso a gestión de respaldos
                    ['name' => 'Ver bitácora', 'slug' => 'bitacora.index'],    // Acceso a bitácora del sistema
                ]
            ],
        ];
    }
}