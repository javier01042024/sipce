<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Diario;
use App\Models\PatientActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PacientePortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        $proximaCita = null;
        $ultimasCitas = collect();
        $ultimosDiarios = collect();

        if ($paciente) {
            $proximaCita = Cita::where('paciente_id', $paciente->id)
                ->where('fecha', '>=', now()->toDateString())
                ->where('estado', 'pendiente')
                ->orderBy('fecha')
                ->first();

            $ultimasCitas = Cita::where('paciente_id', $paciente->id)
                ->orderBy('fecha', 'desc')
                ->limit(5)
                ->get();

            $ultimosDiarios = Diario::where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->limit(3)
                ->get();
        }

        $this->registrarActividad('dashboard', 'Accedió al panel principal');

        return view('paciente.dashboard', compact('paciente', 'proximaCita', 'ultimasCitas', 'ultimosDiarios'));
    }

    public function misCitas()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        $citas = collect();
        if ($paciente) {
            $citas = Cita::where('paciente_id', $paciente->id)
                ->orderBy('fecha', 'desc')
                ->get();
        }

        $this->registrarActividad('citas', 'Consultó sus citas');

        return view('paciente.mis-citas', compact('citas', 'paciente'));
    }

    public function miDiario()
    {
        $user = Auth::user();
        $diarios = Diario::where('user_id', $user->id)
            ->orderBy('fecha', 'desc')
            ->get();

        $this->registrarActividad('diario', 'Consultó su diario');

        return view('paciente.mi-diario', compact('diarios'));
    }

    public function storeDiario(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'contenido' => 'required|string|min:3',
        ]);

        $user = Auth::user();

        if (Diario::where('user_id', $user->id)->where('fecha', $request->fecha)->exists()) {
            return redirect()->route('paciente.mi-diario')
                ->with('error', 'Ya tienes una entrada para esta fecha. Puedes editarla.');
        }

        Diario::create([
            'user_id' => $user->id,
            'fecha' => $request->fecha,
            'contenido' => $request->contenido,
        ]);

        $this->registrarActividad('diario', 'Creó una nueva entrada del diario');

        return redirect()->route('paciente.mi-diario')->with('success', 'Entrada del diario guardada');
    }

    public function showDiario(Diario $diario)
    {
        if ($diario->user_id !== Auth::id()) {
            abort(403);
        }

        return view('paciente.show-diario', compact('diario'));
    }

    public function updateDiario(Request $request, Diario $diario)
    {
        if ($diario->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'contenido' => 'required|string|min:3',
        ]);

        $diario->update(['contenido' => $request->contenido]);

        $this->registrarActividad('diario', 'Editó una entrada del diario');

        return redirect()->route('paciente.mi-diario')->with('success', 'Entrada actualizada');
    }

    public function destroyDiario(Diario $diario)
    {
        if ($diario->user_id !== Auth::id()) {
            abort(403);
        }

        $diario->delete();

        $this->registrarActividad('diario', 'Eliminó una entrada del diario');

        return redirect()->route('paciente.mi-diario')->with('success', 'Entrada eliminada');
    }

    public function perfil()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        $this->registrarActividad('perfil', 'Consultó su perfil');

        return view('paciente.perfil', compact('user', 'paciente'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        $user->update(['password' => $request->password]);

        $this->registrarActividad('password', 'Cambió su contraseña');

        return redirect()->route('paciente.perfil')->with('success', 'Contraseña actualizada correctamente');
    }

    public function actividad()
    {
        $user = Auth::user();
        $actividad = PatientActivityLog::where('user_id', $user->id)
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        return view('paciente.actividad', compact('actividad'));
    }

    private function registrarActividad(string $accion, string $descripcion = null): void
    {
        PatientActivityLog::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'descripcion' => $descripcion,
            'ip' => request()->ip(),
            'fecha_hora' => now(),
        ]);
    }
}
