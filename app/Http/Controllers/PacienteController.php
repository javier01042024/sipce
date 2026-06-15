<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    /**
     * Muestra el listado de todos los pacientes.
     * Permite filtrar por búsqueda y prioridad.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Construye la consulta base de pacientes
        $pacientes = Paciente::query()
            // Filtro de búsqueda: busca en nombre completo o número de expediente
            ->when($request->filled('search'), function($query) use ($request) {
                return $query->where('nombre_completo', 'like', '%' . $request->search . '%')
                            ->orWhere('numero_expediente', 'like', '%' . $request->search . '%');
            })
            // Filtro por nivel de prioridad
            ->when($request->filled('prioridad'), function($query) use ($request) {
                return $query->where('prioridad', $request->prioridad);
            })
            // Ordena por fecha de creación descendente
            ->orderBy('created_at', 'desc')
            // Pagina los resultados: 15 pacientes por página
            ->paginate(15);
        
        // Retorna la vista con los pacientes paginados
        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Muestra el formulario para crear un nuevo paciente.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pacientes.create');
    }

    /**
     * Almacena un nuevo paciente en la base de datos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'numero_expediente' => 'required|string|unique:pacientes,numero_expediente',
            'nacionalidad' => 'required|in:V,E', // V: Venezolano, E: Extranjero
            'cedula_paciente' => 'required|string|unique:pacientes,cedula_paciente',
            'nombre_completo' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', // Solo letras y espacios
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:pacientes,email',
            'direccion' => 'nullable|string',
            'motivo_consulta' => 'nullable|string',
            'diagnostico_preliminar' => 'nullable|string',
            'prioridad' => 'required|in:Alta,Media,Baja'
        ], [
            // Mensajes de validación personalizados
            'numero_expediente.unique' => 'El número de expediente ya está registrado',
            'cedula_paciente.unique' => 'La cédula ya está registrada',
            'nombre_completo.regex' => 'El nombre solo puede contener letras y espacios',
            'email.unique' => 'El correo electrónico ya está registrado'
        ]);

        // Combinar nacionalidad + cédula para formar la cédula completa (ej: V12345678)
        $cedulaCompleta = $request->nacionalidad . $request->cedula_paciente;

        // Crear el paciente en la base de datos
        Paciente::create([
            'user_id' => null, // Inicialmente sin usuario asociado
            'numero_expediente' => $validated['numero_expediente'],
            'cedula_paciente' => $cedulaCompleta, // Guardar cédula completa
            'nombre_completo' => $validated['nombre_completo'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
            'direccion' => $validated['direccion'],
            'motivo_consulta' => $validated['motivo_consulta'],
            'diagnostico_preliminar' => $validated['diagnostico_preliminar'],
            'prioridad' => $validated['prioridad']
        ]);

        // Redirigir al listado con mensaje de éxito
        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    /**
     * Muestra los detalles de un paciente específico.
     * Incluye sus citas relacionadas.
     * 
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\View\View
     */
    public function show(Paciente $paciente)
    {
        // Carga la relación de citas del paciente
        $paciente->load('citas');
        
        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Muestra el formulario para editar un paciente.
     * 
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\View\View
     */
    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Actualiza los datos de un paciente existente.
     * Si el paciente tiene un usuario asociado, también actualiza sus datos.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Paciente $paciente)
    {
        // Validar los datos del formulario (ignora el registro actual en reglas unique)
        $validated = $request->validate([
            'numero_expediente' => 'required|string|unique:pacientes,numero_expediente,' . $paciente->id,
            'cedula_paciente' => 'required|string|unique:pacientes,cedula_paciente,' . $paciente->id,
            'nombre_completo' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:pacientes,email,' . $paciente->id,
            'direccion' => 'nullable|string',
            'motivo_consulta' => 'nullable|string',
            'diagnostico_preliminar' => 'nullable|string',
            'prioridad' => 'required|in:Alta,Media,Baja'
        ], [
            'numero_expediente.unique' => 'El número de expediente ya está registrado por otro paciente',
            'cedula_paciente.unique' => 'La cédula ya está registrada por otro paciente',
            'nombre_completo.regex' => 'El nombre solo puede contener letras y espacios',
            'email.unique' => 'El correo electrónico ya está registrado por otro paciente'
        ]);

        // Iniciar transacción para asegurar integridad de datos
        DB::beginTransaction();
        
        try {
            // Actualizar datos del paciente
            $paciente->update([
                'numero_expediente' => $validated['numero_expediente'],
                'cedula_paciente' => $validated['cedula_paciente'],
                'nombre_completo' => $validated['nombre_completo'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'direccion' => $validated['direccion'],
                'motivo_consulta' => $validated['motivo_consulta'],
                'diagnostico_preliminar' => $validated['diagnostico_preliminar'],
                'prioridad' => $validated['prioridad']
            ]);
            
            // Si el paciente tiene un usuario asociado, actualizar sus datos también
            if ($paciente->user) {
                $paciente->user->update([
                    'name' => $validated['nombre_completo'],
                    'email' => $validated['email']
                ]);
            }
            
            // Confirmar la transacción
            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente actualizado correctamente.');
                
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput(); // Mantener los datos ingresados en el formulario
        }
    }

    /**
     * Elimina un paciente de la base de datos.
     * Si tiene usuario asociado, también lo elimina.
     * 
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Paciente $paciente)
    {
        // Iniciar transacción para eliminar paciente y usuario relacionados
        DB::beginTransaction();
        
        try {
            // Si el paciente tiene un usuario asociado, eliminarlo primero
            if ($paciente->user) {
                $paciente->user->delete();
            }
            
            // Eliminar el paciente
            $paciente->delete();
            
            // Confirmar la transacción
            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente eliminado correctamente.');
                
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los pacientes que no tienen usuario asignado.
     * Responde en formato JSON para peticiones AJAX.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sinUsuario()
    {
        try {
            // Obtener pacientes sin usuario asociado
            $pacientes = Paciente::whereNull('user_id')
                ->select('id', 'numero_expediente', 'nombre_completo', 'cedula_paciente')
                ->orderBy('nombre_completo')
                ->get();
            
            // Retornar respuesta JSON exitosa
            return response()->json([
                'success' => true,
                'pacientes' => $pacientes
            ]);
        } catch (\Exception $e) {
            // Retornar respuesta JSON de error
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pacientes: ' . $e->getMessage()
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }

    /**
     * Habilita el acceso al sistema para un paciente creando un usuario.
     * Asigna el rol de "paciente" al nuevo usuario.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function habilitarAcceso(Request $request, Paciente $paciente)
    {
        // Verificar si el paciente ya tiene acceso
        if ($paciente->user_id) {
            return redirect()->back()
                ->with('error', 'Este paciente ya tiene acceso al sistema.');
        }

        // Validar la contraseña ingresada
        $request->validate([
            'password' => 'required|min:6|confirmed' // Debe coincidir con la confirmación
        ], [
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);

        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Crear el usuario del sistema
            $user = User::create([
                'name' => $paciente->nombre_completo,
                'email' => $paciente->email,
                'password' => bcrypt($request->password), // Encriptar contraseña
                'email_verified_at' => now() // Marcar email como verificado
            ]);
            
            // Asignar rol de paciente al usuario
            $user->assignRole('paciente');
            
            // Vincular el usuario al paciente
            $paciente->user_id = $user->id;
            $paciente->save();
            
            // Confirmar la transacción
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Acceso habilitado. El paciente puede iniciar sesión con email: ' . $paciente->email);
                
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al habilitar acceso: ' . $e->getMessage());
        }
    }

    /**
     * Deshabilita el acceso al sistema para un paciente eliminando su usuario.
     * 
     * @param \App\Models\Paciente $paciente
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deshabilitarAcceso(Paciente $paciente)
    {
        // Verificar si el paciente realmente tiene acceso
        if (!$paciente->user_id) {
            return redirect()->back()
                ->with('error', 'Este paciente no tiene acceso al sistema.');
        }

        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Obtener el usuario asociado
            $user = $paciente->user;
            
            // Desvincular el usuario del paciente
            $paciente->user_id = null;
            $paciente->save();
            
            // Eliminar el usuario del sistema
            $user->delete();
            
            // Confirmar la transacción
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Acceso deshabilitado correctamente.');
                
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al deshabilitar acceso: ' . $e->getMessage());
        }
    }
}