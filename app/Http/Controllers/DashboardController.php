<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Diario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control principal con estadísticas,
     * gráficas y actividad reciente del sistema.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ============================================
        // ESTADÍSTICAS PRINCIPALES
        // ============================================
        
        // Total de pacientes registrados en el sistema
        $totalPacientes = Paciente::count();
        
        // Pacientes nuevos en el mes actual
        $pacientesNuevosMes = Paciente::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Citas programadas pendientes por atender
        $citasProgramadas = Cita::pendientes()->count();
        
        // Citas programadas para esta semana
        $citasEstaSemana = Cita::whereBetween('fecha', [
            Carbon::now()->startOfWeek(), // Inicio de la semana (lunes)
            Carbon::now()->endOfWeek()    // Fin de la semana (domingo)
        ])->count();
        
        // Total de registros en el diario
        $registrosDiarios = Diario::count();
        
        // Registros creados en el día de hoy
        $registrosHoy = Diario::whereDate('created_at', Carbon::today())->count();
        
        // Pacientes clasificados con prioridad alta
        $altaPrioridad = Paciente::where('prioridad', 'alta')->count();
        
        // Comparativa con el día anterior para calcular tendencia
        $altaPrioridadAyer = Paciente::where('prioridad', 'alta')
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
        $diferenciaPrioridad = $altaPrioridad - $altaPrioridadAyer;

        // ============================================
        // DATOS PARA GRÁFICAS
        // ============================================
        
        // Evolución de pacientes en los últimos 6 meses
        $evolucionPacientes = $this->getEvolucionPacientes();
        
        // Distribución de pacientes por nivel de prioridad
        $distribucionPrioridad = [
            'alta' => Paciente::where('prioridad', 'alta')->count(),
            'media' => Paciente::where('prioridad', 'media')->count(),
            'baja' => Paciente::where('prioridad', 'baja')->count(),
        ];
        
        // Distribución de pacientes por rangos de edad
        $distribucionEdad = $this->getDistribucionEdad();

        // ============================================
        // ACTIVIDAD RECIENTE
        // ============================================
        $actividadReciente = $this->getActividadReciente();

        // ============================================
        // PRÓXIMAS CITAS
        // ============================================
        
        // Citas programadas para el día de hoy
        $citasHoy = Cita::with('paciente')
            ->pendientes() // Solo citas en estado pendiente
            ->hoy()        // Solo las de hoy
            ->orderBy('fecha')
            ->get();
        
        // Próximas 5 citas futuras (después de hoy)
        $citasFuturas = Cita::with('paciente')
            ->pendientes()
            ->futuras()    // Solo citas futuras
            ->orderBy('fecha')
            ->take(5)      // Limitar a 5 resultados
            ->get();

        // Retornar la vista del dashboard con todas las variables
        return view('dashboard', compact(
            'totalPacientes',
            'pacientesNuevosMes',
            'citasProgramadas',
            'citasEstaSemana',
            'registrosDiarios',
            'registrosHoy',
            'altaPrioridad',
            'diferenciaPrioridad',
            'evolucionPacientes',
            'distribucionPrioridad',
            'distribucionEdad',
            'actividadReciente',
            'citasHoy',
            'citasFuturas'
        ));
    }

    /**
     * Obtiene la evolución de pacientes nuevos por mes.
     * Calcula los datos de los últimos 6 meses para la gráfica.
     * 
     * @return array Arreglo con etiquetas (labels) y datos (data)
     */
    private function getEvolucionPacientes()
    {
        $meses = []; // Etiquetas de los meses
        $datos = []; // Cantidad de pacientes por mes
        
        // Recorrer los últimos 6 meses (del más antiguo al actual)
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $fecha->translatedFormat('M'); // Nombre del mes traducido (Ene, Feb...)
            $datos[] = Paciente::whereMonth('created_at', $fecha->month)
                ->whereYear('created_at', $fecha->year)
                ->count();
        }
        
        return [
            'labels' => $meses, // Etiquetas para el eje X
            'data' => $datos    // Valores para el eje Y
        ];
    }

    /**
     * Calcula la distribución de pacientes por rangos de edad.
     * Agrupa a los pacientes en 4 categorías según su edad.
     * 
     * @return array Cantidad de pacientes por cada rango de edad
     */
    private function getDistribucionEdad()
    {
        // Obtener solo pacientes con fecha de nacimiento registrada
        $pacientes = Paciente::whereNotNull('fecha_nacimiento')->get();
        
        // Inicializar contadores por rango
        $rangos = [
            '18-30' => 0,  // Jóvenes adultos
            '31-50' => 0,  // Adultos
            '51-70' => 0,  // Adultos mayores
            '70+' => 0     // Tercera edad
        ];
        
        // Clasificar cada paciente según su edad
        foreach ($pacientes as $paciente) {
            $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
            
            if ($edad <= 30) {
                $rangos['18-30']++;
            } elseif ($edad <= 50) {
                $rangos['31-50']++;
            } elseif ($edad <= 70) {
                $rangos['51-70']++;
            } else {
                $rangos['70+']++;
            }
        }
        
        return $rangos;
    }

    /**
     * Obtiene la actividad reciente del sistema.
     * Combina diferentes tipos de eventos: nuevos pacientes, citas,
     * registros diarios y citas completadas.
     * 
     * @return \Illuminate\Support\Collection Colección de actividades ordenadas por fecha
     */
    private function getActividadReciente()
    {
        $actividades = collect();
        
        // Obtener los últimos 3 pacientes creados
        $ultimosPacientes = Paciente::latest()
            ->take(3)
            ->get()
            ->map(function ($paciente) {
                return [
                    'icono' => 'fas fa-user-plus', // Icono de Font Awesome
                    'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', // Color púrpura
                    'titulo' => "Nuevo paciente: {$paciente->nombre_completo}",
                    'tiempo' => $paciente->created_at->diffForHumans(), // Ej: "hace 2 horas"
                    'fecha' => $paciente->created_at
                ];
            });
        
        // Obtener las últimas 3 citas creadas
        $ultimasCitas = Cita::with('paciente')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($cita) {
                return [
                    'icono' => 'fas fa-calendar-plus',
                    'color' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', // Color verde
                    'titulo' => "Cita programada: " . ($cita->paciente->nombre_completo ?? 'Sin paciente'),
                    'tiempo' => $cita->created_at->diffForHumans(),
                    'fecha' => $cita->created_at
                ];
            });
        
        // Obtener los últimos 2 registros del diario
        $ultimosDiarios = Diario::with('user')
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($diario) {
                return [
                    'icono' => 'fas fa-pen',
                    'color' => 'linear-gradient(135deg, #f39c12 0%, #f1c40f 100%)', // Color naranja
                    'titulo' => "Registro actualizado: " . ($diario->user->name ?? 'Usuario'),
                    'tiempo' => $diario->created_at->diffForHumans(),
                    'fecha' => $diario->created_at
                ];
            });
        
        // Obtener las últimas 2 citas completadas (atendidas)
        $citasCompletadas = Cita::with('paciente')
            ->where('estado', 'atendida')
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($cita) {
                return [
                    'icono' => 'fas fa-check-circle',
                    'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', // Color azul
                    'titulo' => "Cita completada: " . ($cita->paciente->nombre_completo ?? 'Sin paciente'),
                    'tiempo' => $cita->updated_at->diffForHumans(),
                    'fecha' => $cita->updated_at
                ];
            });
        
        // Combinar todas las actividades, ordenar por fecha descendente
        // y limitar a los 8 eventos más recientes
        return $actividades->concat($ultimosPacientes)
            ->concat($ultimasCitas)
            ->concat($ultimosDiarios)
            ->concat($citasCompletadas)
            ->sortByDesc('fecha') // Ordenar del más reciente al más antiguo
            ->take(8)             // Solo mostrar 8 actividades
            ->values();           // Reiniciar los índices de la colección
    }
}