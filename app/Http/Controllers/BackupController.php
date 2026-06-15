<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Muestra el panel de gestión de respaldos.
     * Lista todos los respaldos disponibles con sus estadísticas.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Crear carpeta de respaldos si no existe
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0777, true); // Permisos completos
        }
        
        // Obtener lista de archivos de respaldo
        $backups = [];
        $files = File::files($backupPath);
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // Determinar el tipo de respaldo según el prefijo del nombre
            $tipo = 'manual'; // Por defecto es manual
            if (str_contains($filename, 'auto_')) {
                $tipo = 'automatico'; // Respaldo automático diario
            } elseif (str_contains($filename, 'semanal_')) {
                $tipo = 'semanal'; // Respaldo semanal programado
            }
            
            // Crear objeto con información del archivo de respaldo
            $backups[] = (object)[
                'nombre' => $filename,
                'tipo' => $tipo,
                'tamaño' => $this->formatSize($file->getSize()), // Tamaño legible para humanos
                'tamaño_bytes' => $file->getSize(),              // Tamaño en bytes para cálculos
                'fecha' => date('d/m/Y H:i:s', $file->getMTime()), // Fecha de modificación
                'ruta' => $file->getPathname()                    // Ruta completa del archivo
            ];
        }
        
        // Ordenar por tamaño de archivo descendente (del más grande al más pequeño)
        usort($backups, function($a, $b) {
            return $b->tamaño_bytes - $a->tamaño_bytes;
        });
        
        // ============================================
        // ESTADÍSTICAS DE RESPALDOS
        // ============================================
        
        // Total de archivos de respaldo
        $totalBackups = count($backups);
        
        // Sumar el tamaño total de todos los respaldos
        $tamañoTotal = array_sum(array_column($backups, 'tamaño_bytes'));
        $tamañoTotalFormateado = $this->formatSize($tamañoTotal);
        
        // Contar respaldos automáticos
        $automaticos = count(array_filter($backups, function($b) { 
            return $b->tipo == 'automatico'; 
        }));
        
        // Obtener la fecha del último respaldo (el primero del arreglo)
        $ultimoRespaldo = !empty($backups) ? $backups[0]->fecha : 'No hay respaldos';
        
        // Retornar vista con todas las variables
        return view('configuracion.respaldos.index', compact(
            'backups',
            'totalBackups',
            'tamañoTotal',
            'tamañoTotalFormateado',
            'automaticos',
            'ultimoRespaldo'
        ));
    }
    
    /**
     * Crea un nuevo respaldo de la base de datos usando PHP puro.
     * No depende de mysqldump, funciona directamente con consultas SQL.
     * Genera un archivo SQL y lo comprime en formato ZIP.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        // Obtener tipo de respaldo (manual por defecto)
        $tipo = $request->get('tipo', 'manual');
        
        try {
            // Asegurar que la carpeta de respaldos existe
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0777, true);
            }
            
            // Generar nombre de archivo con timestamp
            $timestamp = date('Y-m-d_His');
            $filename = "backup_{$tipo}_{$timestamp}.sql";
            $filePath = storage_path("app/backups/{$filename}");
            
            // Obtener el nombre de la base de datos desde la configuración
            $dbName = env('DB_DATABASE', 'sipce1');
            
            // Obtener todas las tablas de la base de datos
            $tables = DB::select('SHOW TABLES');
            
            // Obtener la clave correcta para el nombre de la tabla
            // (varía según el driver de base de datos)
            $firstTable = json_decode(json_encode($tables[0]), true);
            $tableKey = array_keys($firstTable)[0];
            
            // Iniciar construcción del archivo SQL con cabecera informativa
            $sql = "-- ====================================================\n";
            $sql .= "-- RESPALDO DE BASE DE DATOS\n";
            $sql .= "-- Base de datos: {$dbName}\n";
            $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- ====================================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n"; // Desactivar verificación de claves foráneas
            
            // Procesar cada tabla
            foreach ($tables as $table) {
                $tableArray = json_decode(json_encode($table), true);
                $tableName = $tableArray[$tableKey];
                
                // Saltar tablas de sistema para no incluir respaldos antiguos
                if ($tableName == 'backups' || $tableName == 'bitacora_backup') {
                    continue;
                }
                
                // Obtener la estructura CREATE TABLE
                $createResult = DB::select("SHOW CREATE TABLE {$tableName}");
                $createArray = json_decode(json_encode($createResult[0]), true);
                
                // Extraer la sentencia CREATE TABLE del resultado
                $createTableSQL = '';
                foreach ($createArray as $key => $value) {
                    if (strpos($key, 'Create Table') !== false || strpos($key, 'Create') !== false) {
                        $createTableSQL = $value;
                        break;
                    }
                }
                
                // Si no se encontró la estructura, crear una vacía
                if (empty($createTableSQL)) {
                    $createTableSQL = "CREATE TABLE `{$tableName}` ()";
                }
                
                // Agregar separador y DROP TABLE antes del CREATE
                $sql .= "-- ----------------------------------------------------\n";
                $sql .= "-- Tabla: {$tableName}\n";
                $sql .= "-- ----------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTableSQL . ";\n\n";
                
                // Obtener todos los registros de la tabla
                $rows = DB::table($tableName)->get();
                if (count($rows) > 0) {
                    $sql .= "-- ----------------------------------------------------\n";
                    $sql .= "-- Datos de tabla: {$tableName}\n";
                    $sql .= "-- ----------------------------------------------------\n";
                    
                    // Obtener nombres de las columnas
                    $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
                    $columnNames = [];
                    foreach ($columns as $column) {
                        $columnArray = json_decode(json_encode($column), true);
                        $columnNames[] = $columnArray['Field'];
                    }
                    
                    // Construir sentencia INSERT
                    $sql .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columnNames) . "`) VALUES\n";
                    
                    // Procesar cada fila de datos
                    $values = [];
                    foreach ($rows as $row) {
                        $rowArray = json_decode(json_encode($row), true);
                        $escapedValues = [];
                        
                        // Escapar cada valor según su tipo
                        foreach ($columnNames as $col) {
                            $value = $rowArray[$col] ?? null;
                            if ($value === null) {
                                $escapedValues[] = 'NULL'; // Valores nulos
                            } elseif (is_numeric($value)) {
                                $escapedValues[] = $value; // Valores numéricos sin comillas
                            } else {
                                $escapedValues[] = "'" . addslashes($value) . "'"; // Strings con comillas y escapado
                            }
                        }
                        $values[] = "(" . implode(', ', $escapedValues) . ")";
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
            
            // Finalizar el archivo SQL
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n"; // Reactivar verificación de claves foráneas
            $sql .= "\n-- ====================================================\n";
            $sql .= "-- FIN DEL RESPALDO\n";
            $sql .= "-- ====================================================\n";
            
            // Guardar el archivo SQL en disco
            File::put($filePath, $sql);
            
            // Verificar que el archivo se creó correctamente
            if (!File::exists($filePath) || File::size($filePath) == 0) {
                throw new \Exception('No se pudo crear el archivo de respaldo');
            }
            
            // Comprimir el archivo SQL en formato ZIP para ahorrar espacio
            $zipPath = str_replace('.sql', '.zip', $filePath);
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($filePath, $filename); // Agregar archivo SQL al ZIP
                $zip->close();
                // Eliminar archivo SQL original después de comprimir
                File::delete($filePath);
            }
            
            // Retornar respuesta exitosa en formato JSON
            return response()->json([
                'success' => true,
                'message' => 'Respaldo creado exitosamente',
                'filename' => basename($zipPath),
                'size' => $this->formatSize(File::size($zipPath))
            ]);
            
        } catch (\Exception $e) {
            // Retornar error en formato JSON
            return response()->json([
                'success' => false,
                'message' => 'Error al crear respaldo: ' . $e->getMessage()
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }
    
    /**
     * Descarga un archivo de respaldo específico.
     * 
     * @param string $filename Nombre del archivo a descargar
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($filename)
    {
        $filePath = storage_path("app/backups/{$filename}");
        
        // Verificar que el archivo existe
        if (!File::exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }
        
        // Forzar la descarga del archivo
        return response()->download($filePath, $filename);
    }
    
    /**
     * Restaura una base de datos desde un archivo de respaldo.
     * Soporta archivos SQL y ZIP. Usa PHP puro sin depender de mysqldump.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $filename Nombre del archivo de respaldo
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, $filename)
    {
        $filePath = storage_path("app/backups/{$filename}");
        
        // Verificar que el archivo de respaldo existe
        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);
        }
        
        try {
            // Extraer contenido SQL según el tipo de archivo
            if (pathinfo($filePath, PATHINFO_EXTENSION) == 'zip') {
                // Si es ZIP, extraer el archivo SQL temporalmente
                $zip = new ZipArchive();
                $zip->open($filePath);
                $sqlPath = storage_path('app/backups/temp_' . time() . '.sql');
                $zip->extractTo(storage_path('app/backups/'), basename($sqlPath));
                $zip->close();
                $sqlContent = File::get($sqlPath);
                // Eliminar archivo temporal después de leerlo
                File::delete($sqlPath);
            } else {
                // Si es SQL, leer directamente
                $sqlContent = File::get($filePath);
            }
            
            // Iniciar transacción para asegurar integridad
            DB::beginTransaction();
            
            // Separar las consultas por punto y coma + nueva línea
            $queries = explode(";\n", $sqlContent);
            
            // Ejecutar cada consulta individualmente
            foreach ($queries as $query) {
                $query = trim($query);
                // Ignorar líneas vacías y comentarios SQL
                if (!empty($query) && !str_starts_with($query, '--')) {
                    try {
                        DB::statement($query);
                    } catch (\Exception $e) {
                        // Ignorar errores de elementos que ya existen
                        // (tablas duplicadas o registros duplicados)
                        if (!str_contains($e->getMessage(), 'already exists') && 
                            !str_contains($e->getMessage(), 'Duplicate entry')) {
                            throw $e; // Relanzar otros tipos de errores
                        }
                    }
                }
            }
            
            // Confirmar la transacción
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Respaldo restaurado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Elimina un archivo de respaldo del servidor.
     * 
     * @param string $filename Nombre del archivo a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($filename)
    {
        $filePath = storage_path("app/backups/{$filename}");
        
        // Verificar que el archivo existe
        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);
        }
        
        try {
            // Eliminar el archivo del disco
            File::delete($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Respaldo eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Guarda la configuración de respaldos automáticos.
     * Almacena las preferencias en un archivo JSON.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveConfig(Request $request)
    {
        // Construir arreglo de configuración
        $config = [
            'auto_diario' => $request->get('auto_diario', false),     // Respaldo automático diario
            'auto_semanal' => $request->get('auto_semanal', false),   // Respaldo automático semanal
            'notificar_email' => $request->get('notificar_email', false), // Notificación por correo
            'comprimir' => $request->get('comprimir', true)           // Comprimir respaldos en ZIP
        ];
        
        // Guardar configuración en archivo JSON con formato legible
        $configPath = storage_path('app/backups/config.json');
        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));
        
        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada'
        ]);
    }
    
    /**
     * Obtiene la configuración actual de respaldos.
     * Si no existe el archivo de configuración, retorna valores predeterminados.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig()
    {
        $configPath = storage_path('app/backups/config.json');
        
        // Cargar configuración existente o usar valores por defecto
        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true);
        } else {
            // Configuración predeterminada
            $config = [
                'auto_diario' => true,
                'auto_semanal' => true,
                'notificar_email' => false,
                'comprimir' => true
            ];
        }
        
        return response()->json($config);
    }
    
    /**
     * Formatea el tamaño de un archivo en una unidad legible.
     * Convierte bytes a B, KB, MB o GB según corresponda.
     * 
     * @param int $bytes Tamaño en bytes
     * @return string Tamaño formateado con unidad
     */
    private function formatSize($bytes)
    {
        // 1 GB = 1,073,741,824 bytes
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } 
        // 1 MB = 1,048,576 bytes
        elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } 
        // 1 KB = 1,024 bytes
        elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } 
        // Menos de 1 KB, mostrar en bytes
        else {
            return $bytes . ' B';
        }
    }
}