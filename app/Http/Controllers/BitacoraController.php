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
     * Exporta los registros de la bitácora a un archivo CSV.
     * Aplica los mismos filtros que la vista de listado.
     * El archivo incluye BOM UTF-8 para correcta visualización de caracteres especiales.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportar(Request $request)
    {
        // Obtener parámetros de filtro desde la URL
        $usuarioId = $request->get('usuario');
        $accion = $request->get('accion');
        $tabla = $request->get('tabla');
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        
        // Obtener registros filtrados (sin paginación para exportar todos)
        $registros = Bitacora::with('usuario')
            ->porUsuario($usuarioId)
            ->porAccion($accion)
            ->porTabla($tabla)
            ->porFecha($desde, $hasta)
            ->orderBy('fecha_hora', 'desc')
            ->get();
        
        // Generar nombre del archivo con fecha y hora actual
        $filename = "bitacora_" . date('Y-m-d_His') . ".csv";
        
        // Usar stream para descargar directamente sin cargar todo en memoria
        return response()->stream(
            function() use ($registros) {
                // Abrir el flujo de salida para escritura
                $handle = fopen('php://output', 'w');
                
                // Agregar BOM (Byte Order Mark) UTF-8 al inicio del archivo
                // Esto asegura que programas como Excel reconozcan los caracteres especiales
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Escribir la fila de cabeceras del CSV
                fputcsv($handle, [
                    'ID', 
                    'Usuario', 
                    'Acción', 
                    'Tabla', 
                    'Registro ID', 
                    'Descripción', 
                    'IP', 
                    'Fecha/Hora'
                ]);
                
                // Escribir cada registro como una fila del CSV
                foreach ($registros as $registro) {
                    // Construir descripción combinando datos viejos y nuevos
                    $descripcion = $registro->datos_viejos ?? '';
                    if ($registro->datos_nuevos) {
                        // Si hay datos nuevos, concatenarlos con una flecha
                        $descripcion .= ' → ' . $registro->datos_nuevos;
                    }
                    
                    // Escribir fila con los datos del registro
                    fputcsv($handle, [
                        $registro->id,
                        $registro->usuario_nombre ?? 'Sistema', // Si no hay usuario, mostrar "Sistema"
                        $registro->accion,
                        $registro->tabla_afectada,
                        $registro->registro_id,
                        $descripcion,
                        $registro->ip ?? 'N/A',                  // Si no hay IP, mostrar "N/A"
                        $registro->fecha_hora 
                            ? $registro->fecha_hora->format('d/m/Y H:i:s') 
                            : 'N/A'                               // Formatear fecha o mostrar "N/A"
                    ]);
                }
                
                // Cerrar el archivo
                fclose($handle);
            },
            200, // Código HTTP 200: Éxito
            [
                // Cabeceras HTTP para la descarga del archivo
                'Content-Type' => 'text/csv; charset=UTF-8',           // Tipo de contenido CSV con UTF-8
                'Content-Disposition' => 'attachment; filename="' . $filename . '"', // Forzar descarga
            ]
        );
    }
}