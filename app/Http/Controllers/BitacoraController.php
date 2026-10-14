<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Muestra el listado de la bitácora con filtros y estadísticas.
     * Permite filtrar por usuario, acción, tabla y rango de fechas.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Obtener parámetros de filtro desde la URL
        $usuarioId = $request->get('usuario'); // ID del usuario para filtrar
        $accion = $request->get('accion');       // Tipo de acción (INSERT, UPDATE, DELETE, LOGIN)
        $tabla = $request->get('tabla');         // Tabla afectada (pacientes, citas, diarios, auth)
        $desde = $request->get('desde');         // Fecha de inicio del filtro
        $hasta = $request->get('hasta');         // Fecha de fin del filtro
        
        // Construir consulta aplicando los scopes del modelo Bitacora
        $registros = Bitacora::with('usuario')       // Cargar relación con usuario
            ->porUsuario($usuarioId)                  // Scope: filtrar por usuario
            ->porAccion($accion)                      // Scope: filtrar por acción
            ->porTabla($tabla)                        // Scope: filtrar por tabla
            ->porFecha($desde, $hasta)                // Scope: filtrar por rango de fechas
            ->orderBy('fecha_hora', 'desc')           // Ordenar del más reciente al más antiguo
            ->paginate(20);                           // Paginar: 20 registros por página
        
        // ============================================
        // ESTADÍSTICAS PARA LAS TARJETAS DEL PANEL
        // ============================================
        
        // Total de registros en la bitácora
        $totalRegistros = Bitacora::count();
        
        // Registros creados en el día de hoy
        $registrosHoy = Bitacora::whereDate('fecha_hora', today())->count();
        
        // Conteo de eliminaciones y errores (acciones críticas)
        $errores = Bitacora::where('accion', 'DELETE')
            ->orWhere('accion', 'ERROR')
            ->count();
        
        // Conteo de inicios de sesión
        $iniciosSesion = Bitacora::where('accion', 'LOGIN')->count();
        
        // Obtener lista de usuarios para el filtro desplegable
        $usuarios = User::orderBy('name')->get();
        
        // Definir opciones disponibles para los filtros
        $acciones = ['INSERT', 'UPDATE', 'DELETE', 'LOGIN']; // Tipos de acciones registradas
        $tablas = ['pacientes', 'citas', 'diarios', 'auth']; // Tablas monitoreadas
        
        // Retornar vista con todas las variables necesarias
        return view('configuracion.bitacora.index', compact(
            'registros',
            'totalRegistros',
            'registrosHoy',
            'errores',
            'iniciosSesion',
            'usuarios',
            'acciones',
            'tablas'
        ));
    }
    
    /**
     * GET /api/bitacora
     * Listado paginado para la app móvil con los mismos filtros y
     * estadísticas que la vista web. Requiere permiso bitacora.index.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $user = $request->user();
        $permisos = $user->roles
            ->pluck('permissions')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!in_array('bitacora.index', $permisos)) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado.',
            ], 403);
        }

        $registros = Bitacora::with('usuario:id,name,email')
            ->porUsuario($request->get('usuario'))
            ->porAccion($request->get('accion'))
            ->porTabla($request->get('tabla'))
            ->porFecha($request->get('desde'), $request->get('hasta'))
            ->orderBy('fecha_hora', 'desc')
            ->paginate(min((int) $request->get('per_page', 20), 100));

        return response()->json([
            'success' => true,
            'data' => $registros->items(),
            'pagination' => [
                'total' => $registros->total(),
                'per_page' => $registros->perPage(),
                'current_page' => $registros->currentPage(),
                'last_page' => $registros->lastPage(),
            ],
            'stats' => Bitacora::getEstadisticas(),
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
            'acciones' => ['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'ERROR'],
            'tablas' => ['pacientes', 'paciente_adultos', 'paciente_adolescentes', 'paciente_ninos', 'citas', 'sesiones', 'diarios', 'diagnosticos', 'acompanantes', 'notificaciones', 'plan_tratamiento', 'plan_objetivos', 'estados', 'users', 'auth'],
        ]);
    }
}
