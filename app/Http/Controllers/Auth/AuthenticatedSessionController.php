<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\BitacoraHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        BitacoraHelper::login();

        $this->setActiveSyncUser($request->user());

        $user = $request->user();
        if ($user->hasRole('paciente')) {
            return redirect()->intended(route('paciente.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->clearActiveSyncUser();

        return redirect('/');
    }

    /**
     * El proceso CLI de sincronizacion (proceso separado del servidor web)
     * lee este marcador para atribuir la actividad de sync al doctor que
     * tiene la sesion abierta en el escritorio en lugar de la cuenta de admin.
     */
    private function setActiveSyncUser(\Illuminate\Contracts\Auth\Authenticatable $user): void
    {
        try {
            $dir = storage_path('app/sync');
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            @file_put_contents($dir.'/active-user.txt', (string) $user->email);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('No se pudo escribir el marcador de usuario activo', ['error' => $e->getMessage()]);
        }
    }

    private function clearActiveSyncUser(): void
    {
        try {
            $file = storage_path('app/sync/active-user.txt');
            if (file_exists($file)) {
                @unlink($file);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('No se pudo limpiar el marcador de usuario activo', ['error' => $e->getMessage()]);
        }
    }
}
