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
    public function index()
    {
        // ============================================
        // ESTADÍSTICAS PRINCIPALES
        // ============================================
        
        // ✅ REACTIVADO - Pacientes
        $totalPacientes = Paciente::count();
        $pacientesNuevosMes = Paciente::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // ✅ Citas
        $citasProgramadas = Cita::pendientes()->count();
        $citasEstaSemana = Cita::whereBetween('fecha', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()    
        ])->count();
        
        // ✅ Diario
        $registrosDiarios = Diario::count();
        $registrosHoy = Diario::whereDate('created_at', Carbon::today())->count();
        
        // ✅ REACTIVADO - Prioridad
        $altaPrioridad = Paciente::where('prioridad', 'alta')
            ->orWhere('prioridad', 'urgencia')
            ->count();
            
        $altaPrioridadAyer = Paciente::where(function($q) {
                $q->where('prioridad', 'alta')
                  ->orWhere('prioridad', 'urgencia');
            })
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
            
        $diferenciaPrioridad = $altaPrioridad - $altaPrioridadAyer;

        // ============================================
        // DATOS PARA GRÁFICAS
        // ============================================
        
        // ✅ REACTIVADO
        $evolucionPacientes = $this->getEvolucionPacientes();
        
        // ✅ REACTIVADO
        $distribucionPrioridad = [
            'alta' => Paciente::where('prioridad', 'alta')->orWhere('prioridad', 'urgencia')->count(),
            'media' => Paciente::where('prioridad', 'media')->count(),
            'baja' => Paciente::where('prioridad', 'baja')->count(),
        ];
        
        // ✅ REACTIVADO
        $distribucionEdad = $this->getDistribucionEdad();

        // ============================================
        // ACTIVIDAD RECIENTE
        // ============================================
        $actividadReciente = $this->getActividadReciente();

        // ============================================
        // PRÓXIMAS CITAS
        // ============================================
        
        // ✅ REACTIVADO - Ahora con relación paciente
        $citasHoy = Cita::with('paciente.detalle')
            ->pendientes()
            ->hoy()        
            ->orderBy('fecha')
            ->get();
        
        $citasFuturas = Cita::with('paciente.detalle')
            ->pendientes()
            ->futuras()    
            ->orderBy('fecha')
            ->take(5)      
            ->get();

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
     * Obtener evolución de pacientes por mes (últimos 6 meses)
     */
    private function getEvolucionPacientes()
    {
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $labels[] = $fecha->translatedFormat('M');
            
            $data[] = Paciente::whereMonth('created_at', $fecha->month)
                ->whereYear('created_at', $fecha->year)
                ->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Obtener distribución de pacientes por rango de edad
     */
    private function getDistribucionEdad()
    {
        $pacientes = Paciente::with('detalle')->get();
        
        $rangos = [
            '18-30' => 0,
            '31-50' => 0,
            '51-70' => 0,
            '70+' => 0,
        ];
        
        foreach ($pacientes as $paciente) {
            if ($paciente->detalle && $paciente->detalle->fecha_nacimiento) {
                $edad = Carbon::parse($paciente->detalle->fecha_nacimiento)->age;
                
                if ($edad >= 18 && $edad <= 30) {
                    $rangos['18-30']++;
                } elseif ($edad >= 31 && $edad <= 50) {
                    $rangos['31-50']++;
                } elseif ($edad >= 51 && $edad <= 70) {
                    $rangos['51-70']++;
                } elseif ($edad > 70) {
                    $rangos['70+']++;
                }
            }
        }
        
        return $rangos;
    }

    /**
     * Obtener actividad reciente del sistema
     */
    private function getActividadReciente()
    {
        $actividades = collect();
        
        // ✅ REACTIVADO - Nuevos pacientes
        $ultimosPacientes = Paciente::with('detalle')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($paciente) {
                $nombre = $paciente->detalle 
                    ? $paciente->detalle->nombre . ' ' . $paciente->detalle->apellido 
                    : 'Paciente ' . $paciente->numero_expediente;
                    
                return [
                    'icono' => 'fas fa-user-plus',
                    'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'titulo' => "Nuevo paciente: {$nombre}",
                    'tiempo' => $paciente->created_at->diffForHumans(),
                    'fecha' => $paciente->created_at
                ];
            });
        
        // ✅ Citas nuevas
        $ultimasCitas = Cita::with('paciente.detalle')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($cita) {
                $nombre = $cita->paciente && $cita->paciente->detalle 
                    ? $cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido 
                    : 'Paciente';
                    
                return [
                    'icono' => 'fas fa-calendar-plus',
                    'color' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', 
                    'titulo' => "Cita programada: {$nombre}",
                    'tiempo' => $cita->created_at->diffForHumans(), 
                    'fecha' => $cita->created_at
                ];
            });
        
        // ✅ Registros del diario
        $ultimosDiarios = Diario::with('user')
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($diario) {
                return [
                    'icono' => 'fas fa-pen',
                    'color' => 'linear-gradient(135deg, #f39c12 0%, #f1c40f 100%)', 
                    'titulo' => "Registro actualizado: " . ($diario->user->name ?? 'Usuario'),
                    'tiempo' => $diario->created_at->diffForHumans(),
                    'fecha' => $diario->created_at
                ];
            });
        
        // ✅ Citas completadas
        $citasCompletadas = Cita::with('paciente.detalle')
            ->where('estado', 'atendida')
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($cita) {
                $nombre = $cita->paciente && $cita->paciente->detalle 
                    ? $cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido 
                    : 'Paciente';
                    
                return [
                    'icono' => 'fas fa-check-circle',
                    'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 
                    'titulo' => "Cita completada: {$nombre}",
                    'tiempo' => $cita->updated_at->diffForHumans(),
                    'fecha' => $cita->updated_at
                ];
            });
        
        return $actividades->concat($ultimosPacientes)
            ->concat($ultimasCitas)
            ->concat($ultimosDiarios)
            ->concat($citasCompletadas)
            ->sortByDesc('fecha')
            ->take(8)
            ->values();
    }
}