<?php

namespace App\Http\Controllers;

use App\Models\Diario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiarioController extends Controller
{
    /**
     * Muestra el listado de entradas del diario.
     * El usuario administrador (ID 1) puede ver todas las entradas,
     * los demás usuarios solo ven sus propias entradas.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Si es el administrador (ID 1), obtiene todas las entradas con sus usuarios
        // Si es un usuario normal, solo obtiene sus propias entradas
        $diarios = Auth::user()->id == 1 
            ? Diario::with('user')->orderBy('fecha', 'desc')->get()
            : Diario::where('user_id', Auth::id())->orderBy('fecha', 'desc')->get();
        
        // Retorna la vista con las entradas del diario
        return view('diarios.index', compact('diarios'));
    }

    /**
     * Muestra el formulario para crear una nueva entrada del diario.
     * El administrador puede seleccionar cualquier usuario,
     * los usuarios normales solo pueden crear entradas para sí mismos.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Si es administrador, obtiene todos los usuarios para el selector
        // Si es usuario normal, solo se muestra a sí mismo en una colección
        $users = Auth::user()->id == 1 
            ? User::orderBy('name')->get()
            : collect([Auth::user()]);
        
        // Retorna la vista del formulario con los usuarios disponibles
        return view('diarios.create', compact('users'));
    }

    /**
     * Almacena una nueva entrada del diario en la base de datos.
     * Verifica que el usuario tenga permiso para crear la entrada.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'user_id' => 'required|exists:users,id', // El usuario debe existir
            'fecha' => 'required|date',
            'contenido' => 'required|string',
        ]);

        // Verificar permisos: solo el administrador puede crear entradas para otros usuarios
        // Si no es administrador y trata de crear una entrada para otro usuario, denegar acceso
        if (Auth::user()->id != 1 && $request->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }

        // Crear la entrada del diario con todos los datos del formulario
        Diario::create($request->all());
        
        // Redirigir al listado con mensaje de éxito
        return redirect()->route('diarios.index')->with('success', 'Guardado');
    }

    /**
     * Muestra los detalles de una entrada específica del diario.
     * Verifica que el usuario tenga permiso para ver la entrada.
     * 
     * @param \App\Models\Diario $diario
     * @return \Illuminate\View\View
     */
    public function show(Diario $diario)
    {
        // Verificar permisos: solo el administrador o el dueño de la entrada pueden verla
        if (Auth::user()->id != 1 && $diario->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }
        
        // Retornar la vista con los detalles de la entrada
        return view('diarios.show', compact('diario'));
    }

    /**
     * Muestra el formulario para editar una entrada del diario.
     * Solo el administrador o el dueño de la entrada pueden editarla.
     * 
     * @param \App\Models\Diario $diario
     * @return \Illuminate\View\View
     */
    public function edit(Diario $diario)
    {
        // Verificar permisos: solo el administrador o el dueño pueden editar
        if (Auth::user()->id != 1 && $diario->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }
        
        // Si es administrador, obtiene todos los usuarios para el selector
        // Si es usuario normal, solo se muestra a sí mismo
        $users = Auth::user()->id == 1 
            ? User::orderBy('name')->get()
            : collect([Auth::user()]);
        
        // Retornar la vista de edición con la entrada y los usuarios disponibles
        return view('diarios.edit', compact('diario', 'users'));
    }

    /**
     * Actualiza una entrada existente del diario.
     * Verifica permisos tanto de edición como de asignación de usuario.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Diario $diario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Diario $diario)
    {
        // Verificar permisos: solo el administrador o el dueño pueden actualizar
        if (Auth::user()->id != 1 && $diario->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }

        // Validar los datos actualizados del formulario
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'contenido' => 'required|string',
        ]);

        // Verificar que un usuario normal no intente cambiar el dueño de la entrada
        if (Auth::user()->id != 1 && $request->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }

        // Actualizar la entrada con los nuevos datos
        $diario->update($request->all());
        
        // Redirigir al listado con mensaje de éxito
        return redirect()->route('diarios.index')->with('success', 'Actualizado');
    }

    /**
     * Elimina una entrada del diario.
     * Solo el administrador o el dueño de la entrada pueden eliminarla.
     * 
     * @param \App\Models\Diario $diario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Diario $diario)
    {
        // Verificar permisos: solo el administrador o el dueño pueden eliminar
        if (Auth::user()->id != 1 && $diario->user_id != Auth::id()) {
            abort(403); // Error 403: Acceso prohibido
        }
        
        // Eliminar la entrada del diario
        $diario->delete();
        
        // Redirigir al listado con mensaje de éxito
        return redirect()->route('diarios.index')->with('success', 'Eliminado');
    }
}