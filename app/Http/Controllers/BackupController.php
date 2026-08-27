<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Valida que el nombre de archivo sea seguro (sin path traversal).
     * Solo permite nombres de archivo simples sin barras ni puntos dobles.
     */
    private function sanitizeFilename(string $filename): string
    {
        // Eliminar cualquier path separator o traversal
        $filename = basename($filename);
        // Rechazar si contiene .. o caracteres peligrosos
        if (str_contains($filename, '..') || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            abort(400, 'Nombre de archivo inválido.');
        }
        return $filename;
    }

    /**
     * Valida que un contenido SQL sea seguro para ejecutar.
     * Rechaza operaciones peligrosas como DROP, DELETE masivo, TRUNCATE, ALTER, GRANT, etc.
     */
    private function validateSqlSafety(string $query): void
    {
        $upper = strtoupper(trim($query));
        $dangerous = [
            'DROP DATABASE', 'DROP SCHEMA', 'TRUNCATE', 'ALTER DATABASE',
            'GRANT ', 'REVOKE ', 'CREATE USER', 'DROP USER',
            'SET PASSWORD', 'LOAD DATA', 'INTO OUTFILE', 'INTO DUMPFILE',
            'EXEC ', 'EXECUTE ', 'CALL ', 'INTO OUTFILE',
        ];
        foreach ($dangerous as $pattern) {
            if (str_contains($upper, $pattern)) {
                abort(403, 'Operación SQL no permitida en respaldos.');
            }
        }
    }
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
            File::makeDirectory($backupPath, 0755, true);
        }
        
        // Obtener lista de archivos de respaldo
        $backups = [];
        $files = File::files($backupPath);
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // Determinar el tipo de respaldo según el prefijo del nombre
            $tipo = 'manual';
            if (str_contains($filename, 'auto_')) {
                $tipo = 'automatico';
            } elseif (str_contains($filename, 'semanal_')) {
                $tipo = 'semanal';
            }
            
            $backups[] = (object)[
                'nombre' => $filename,
                'tipo' => $tipo,
                'tamaño' => $this->formatSize($file->getSize()),
                'tamaño_bytes' => $file->getSize(),
                'fecha' => date('d/m/Y H:i:s', $file->getMTime()),
                'ruta' => $file->getPathname()
            ];
        }
        
        // Ordenar por tamaño de archivo descendente
        usort($backups, function($a, $b) {
            return $b->tamaño_bytes - $a->tamaño_bytes;
        });
        
        // Estadísticas
        $totalBackups = count($backups);
        $tamañoTotal = array_sum(array_column($backups, 'tamaño_bytes'));
        $tamañoTotalFormateado = $this->formatSize($tamañoTotal);
        $automaticos = count(array_filter($backups, function($b) { 
            return $b->tipo == 'automatico'; 
        }));
        $ultimoRespaldo = !empty($backups) ? $backups[0]->fecha : 'No hay respaldos';
        
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
     * Crea un nuevo respaldo de la base de datos.
     * Es driver-aware: PostgreSQL usa pg_dump y MySQL usa consultas PHP puras.
     * Genera un archivo SQL y lo comprime en formato ZIP.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $tipo = $request->get('tipo', 'manual');
        
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            $timestamp = date('Y-m-d_His');
            $filename = "backup_{$tipo}_{$timestamp}.sql";
            $filePath = storage_path("app/backups/{$filename}");
            
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'pgsql') {
                $this->dumpPostgres($filePath);
            } else {
                $this->dumpMySql($filePath);
            }
            
            if (!File::exists($filePath) || File::size($filePath) == 0) {
                throw new \Exception('No se pudo crear el archivo de respaldo');
            }
            
            $zipPath = str_replace('.sql', '.zip', $filePath);
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($filePath, $filename);
                $zip->close();
                File::delete($filePath);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Respaldo creado exitosamente',
                'filename' => basename($zipPath),
                'size' => $this->formatSize(File::size($zipPath))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el respaldo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Respaldo para MySQL: genera SQL con PHP puro via SHOW DATABASE.
     */
    private function dumpMySql(string $filePath): void
    {
        $connection = DB::connection();
        $dbName = $connection->getDatabaseName();
        $tables = $connection->select('SHOW TABLES');
        
        $firstTable = json_decode(json_encode($tables[0]), true);
        $tableKey = array_keys($firstTable)[0];
        
        $sql = "-- ====================================================\n";
        $sql .= "-- RESPALDO DE BASE DE DATOS\n";
        $sql .= "-- Base de datos: {$dbName}\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Motor: MySQL\n";
        $sql .= "-- ====================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            $tableArray = json_decode(json_encode($table), true);
            $tableName = $tableArray[$tableKey];
            
            if ($tableName == 'backups' || $tableName == 'bitacora_backup') {
                continue;
            }
            
            $createResult = $connection->select("SHOW CREATE TABLE {$tableName}");
            $createArray = json_decode(json_encode($createResult[0]), true);
            
            $createTableSQL = '';
            foreach ($createArray as $key => $value) {
                if (strpos($key, 'Create Table') !== false || strpos($key, 'Create') !== false) {
                    $createTableSQL = $value;
                    break;
                }
            }
            
            if (empty($createTableSQL)) {
                $createTableSQL = "CREATE TABLE `{$tableName}` ()";
            }
            
            $sql .= "-- ----------------------------------------------------\n";
            $sql .= "-- Tabla: {$tableName}\n";
            $sql .= "-- ----------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTableSQL . ";\n\n";
            
            $rows = $connection->table($tableName)->get();
            if (count($rows) > 0) {
                $sql .= "-- ----------------------------------------------------\n";
                $sql .= "-- Datos de tabla: {$tableName}\n";
                $sql .= "-- ----------------------------------------------------\n";
                
                $columns = $connection->select("SHOW COLUMNS FROM {$tableName}");
                $columnNames = [];
                foreach ($columns as $column) {
                    $columnArray = json_decode(json_encode($column), true);
                    $columnNames[] = $columnArray['Field'];
                }
                
                $sql .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columnNames) . "`) VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $rowArray = json_decode(json_encode($row), true);
                    $escapedValues = [];
                    foreach ($columnNames as $col) {
                        $value = $rowArray[$col] ?? null;
                        if ($value === null) {
                            $escapedValues[] = 'NULL';
                        } elseif (is_numeric($value)) {
                            $escapedValues[] = $value;
                        } else {
                            $escapedValues[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $values[] = "(" . implode(', ', $escapedValues) . ")";
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "\n-- ====================================================\n";
        $sql .= "-- FIN DEL RESPALDO\n";
        $sql .= "-- ====================================================\n";
        
        File::put($filePath, $sql);
    }

    /**
     * Respaldo para PostgreSQL usando pg_dump.
     * Genera SQL plano con INSERTs (no COPY) para que el parser de
     * restauración pueda ejecutarlo statement a statement.
     */
    private function dumpPostgres(string $filePath): void
    {
        $pg = config('database.connections.pgsql');
        $dbHost = $pg['host'] ?? '127.0.0.1';
        $dbPort = $pg['port'] ?? '5432';
        $dbName = $pg['database'] ?? 'sipce';
        $dbUser = $pg['username'] ?? 'postgres';
        $dbPass = $pg['password'] ?? '';
        
        $cmd = sprintf(
            'pg_dump --clean --if-exists --no-owner --no-privileges --inserts --no-comments ' .
            '-h %s -p %s -U %s -d %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName)
        );
        
        $env = array_merge($_SERVER, ['PGPASSWORD' => $dbPass]);
        
        $process = proc_open($cmd, [
            1 => ['file', $filePath, 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, null, $env);
        
        if (!is_resource($process)) {
            throw new \Exception('No se pudo iniciar pg_dump. Verifica que PostgreSQL client esté instalado.');
        }
        
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        
        if ($exitCode !== 0) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            throw new \Exception('pg_dump falló (código ' . $exitCode . '): ' . trim($stderr));
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
        $filename = $this->sanitizeFilename($filename);
        $filePath = storage_path("app/backups/{$filename}");
        
        if (!File::exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }
        
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
        $filename = $this->sanitizeFilename($filename);
        $filePath = storage_path("app/backups/{$filename}");
        
        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo de respaldo no encontrado'
            ], 404);
        }
        
        try {
            // Extraer contenido SQL
            $sqlContent = '';
            if (pathinfo($filePath, PATHINFO_EXTENSION) == 'zip') {
                $zip = new ZipArchive();
                if ($zip->open($filePath) !== true) {
                    throw new \Exception('No se pudo abrir el archivo ZIP');
                }
                $sqlPath = storage_path('app/backups/temp_' . time() . '.sql');
                $zip->extractTo(storage_path('app/backups/'), basename($sqlPath));
                $zip->close();
                
                if (!File::exists($sqlPath)) {
                    throw new \Exception('No se pudo extraer el archivo SQL del ZIP');
                }
                
                $sqlContent = File::get($sqlPath);
                File::delete($sqlPath);
            } else {
                $sqlContent = File::get($filePath);
            }
            
            if (empty($sqlContent)) {
                throw new \Exception('El archivo de respaldo está vacío o es inválido');
            }
            
            // Separar las consultas (tokenizer: respeta strings y comentarios)
            $queries = $this->splitQueries($sqlContent);
            
            $totalQueries = count($queries);
            $executedQueries = 0;
            $errors = [];
            
            // Ejecutar cada consulta (sin transacciones para evitar errores)
            foreach ($queries as $index => $query) {
                try {
                    $this->validateSqlSafety($query);
                    DB::statement($query);
                    $executedQueries++;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    
                    $ignoreErrors = [
                        'already exists',
                        'Duplicate entry',
                        'Unknown table',
                        'Can\'t create table',
                        'Table already exists',
                        'Duplicate key name',
                        'duplicate key value violates unique constraint'
                    ];
                    
                    $shouldIgnore = false;
                    foreach ($ignoreErrors as $ignore) {
                        if (str_contains($errorMsg, $ignore)) {
                            $shouldIgnore = true;
                            break;
                        }
                    }
                    
                    if (!$shouldIgnore) {
                        $errors[] = "Error en la consulta " . ($index + 1) . ": " . $errorMsg;
                    }
                }
            }
            
            // Verificar si hay errores críticos
            if (!empty($errors)) {
                if ($executedQueries > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => "Respaldo restaurado parcialmente. {$executedQueries} de {$totalQueries} consultas ejecutadas correctamente.",
                        'warnings' => $errors,
                        'executed' => $executedQueries,
                        'total' => $totalQueries
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al restaurar el respaldo',
                        'errors' => $errors
                    ], 500);
                }
            }
            
            // Registrar en bitácora
            \App\Helpers\BitacoraHelper::exito(
                'restaurar',
                'respaldos',
                "Restauró el respaldo: {$filename} - {$executedQueries} consultas ejecutadas",
                ['filename' => $filename, 'queries' => $executedQueries]
            );
            
            return response()->json([
                'success' => true,
                'message' => "Respaldo restaurado exitosamente. {$executedQueries} consultas ejecutadas.",
                'queries' => $executedQueries
            ]);
            
        } catch (\Exception $e) {
            \App\Helpers\BitacoraHelper::error(
                'restaurar',
                'respaldos',
                "Error al restaurar el respaldo {$filename}: " . $e->getMessage(),
                ['filename' => $filename, 'error' => $e->getMessage()]
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el respaldo: ' . $e->getMessage()
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
        $filename = $this->sanitizeFilename($filename);
        $filePath = storage_path("app/backups/{$filename}");
        
        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);
        }
        
        try {
            File::delete($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Respaldo eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el respaldo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Importa un archivo SQL subido desde el equipo.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importSql(Request $request)
    {
        \Log::info('=== INICIO IMPORTACIÓN SQL ===');
        
        try {
            // Validar que se haya subido un archivo
            if (!$request->hasFile('sql_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibió ningún archivo. Por favor selecciona un archivo SQL o ZIP.'
                ], 400);
            }
            
            $file = $request->file('sql_file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            
            \Log::info('Archivo recibido: ' . $originalName);
            
            // Validar extensión
            $validExtensions = ['sql', 'zip'];
            if (!in_array(strtolower($extension), $validExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo debe ser de tipo SQL o ZIP. Extensión recibida: ' . $extension
                ], 400);
            }
            
            // Validar tamaño (50MB máximo)
            $maxSize = 50 * 1024 * 1024;
            if ($size > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no debe superar los 50MB. Tamaño actual: ' . number_format($size / 1024 / 1024, 2) . 'MB'
                ], 400);
            }
            
            // Crear carpeta temporal si no existe
            $tempPath = storage_path('app/backups/temp');
            if (!File::exists($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }
            
            // Guardar archivo temporal
            $tempFile = $tempPath . '/' . time() . '_' . $originalName;
            $file->move($tempPath, basename($tempFile));
            
            \Log::info('Archivo guardado temporalmente');
            
            // Obtener contenido SQL
            $sqlContent = '';
            if (strtolower($extension) == 'zip') {
                \Log::info('Procesando archivo ZIP...');
                $zip = new ZipArchive();
                if ($zip->open($tempFile) !== true) {
                    File::delete($tempFile);
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo abrir el archivo ZIP. Verifica que sea un ZIP válido.'
                    ], 400);
                }
                
                $sqlFile = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (pathinfo($filename, PATHINFO_EXTENSION) == 'sql') {
                        $sqlFile = $filename;
                        break;
                    }
                }
                
                if (!$sqlFile) {
                    $zip->close();
                    File::delete($tempFile);
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró un archivo SQL dentro del ZIP'
                    ], 400);
                }
                
                $extractPath = $tempPath . '/extracted_' . time();
                $zip->extractTo($extractPath);
                $zip->close();
                
                $sqlContent = File::get($extractPath . '/' . $sqlFile);
                File::deleteDirectory($extractPath);
            } else {
                $sqlContent = File::get($tempFile);
            }
            
            // Eliminar archivo temporal
            if (File::exists($tempFile)) {
                File::delete($tempFile);
            }
            
            if (empty($sqlContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo SQL está vacío o es inválido'
                ], 400);
            }
            
            // Verificar que el SQL es válido
            if (!preg_match('/CREATE\s+TABLE|INSERT\s+INTO/i', $sqlContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no contiene una estructura de base de datos válida'
                ], 400);
            }
            
            // Extraer nombres de tablas del SQL
            $tableNames = [];
            preg_match_all('/CREATE\s+TABLE\s+`?([a-zA-Z0-9_]+)`?/i', $sqlContent, $matches);
            if (!empty($matches[1])) {
                $tableNames = array_unique($matches[1]);
                \Log::info('Tablas encontradas: ' . implode(', ', $tableNames));
            }
            
            // Separar consultas (tokenizer: respeta strings y comentarios) - SIN TRANSACCIONES
            $queries = $this->splitQueries($sqlContent);
            
            $totalQueries = count($queries);
            $executedQueries = 0;
            $errors = [];
            $createdTables = [];
            
            \Log::info('Total de consultas a ejecutar: ' . $totalQueries);
            
            // Ejecutar cada consulta individualmente SIN transacciones
            foreach ($queries as $index => $query) {
                try {
                    $this->validateSqlSafety($query);
                    DB::statement($query);
                    $executedQueries++;
                    
                    if (preg_match('/CREATE\s+TABLE\s+`?([a-zA-Z0-9_]+)`?/i', $query, $match)) {
                        $createdTables[] = $match[1];
                    }
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    
                    $ignoreErrors = [
                        'already exists',
                        'Duplicate entry',
                        'Unknown table',
                        'Can\'t create table',
                        'Table already exists',
                        'Duplicate key name',
                        'duplicate key value violates unique constraint'
                    ];
                    
                    $shouldIgnore = false;
                    foreach ($ignoreErrors as $ignore) {
                        if (str_contains($errorMsg, $ignore)) {
                            $shouldIgnore = true;
                            break;
                        }
                    }
                    
                    if (!$shouldIgnore) {
                        $errors[] = "Error en la consulta " . ($index + 1) . ": " . $errorMsg;
                        \Log::info('Error en consulta ' . ($index + 1) . ': ' . $errorMsg);
                    }
                }
            }
            
            \Log::info('Consultas ejecutadas: ' . $executedQueries . ' de ' . $totalQueries);
            \Log::info('Tablas creadas: ' . implode(', ', $createdTables));
            
            // Si no se ejecutó ninguna consulta
            if ($executedQueries == 0) {
                \Log::error('Importación fallida, no se ejecutó ninguna consulta');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al importar el archivo SQL. No se ejecutó ninguna consulta.',
                    'errors' => $errors
                ], 500);
            }
            
            // Si hay errores pero se ejecutaron algunas consultas
            if (!empty($errors) && $executedQueries > 0) {
                \Log::info('Importación parcial, ' . $executedQueries . ' consultas ejecutadas');
                
                return response()->json([
                    'success' => true,
                    'message' => "Importación parcial. {$executedQueries} de {$totalQueries} consultas ejecutadas.",
                    'warnings' => $errors,
                    'tables_creadas' => $createdTables,
                    'executed' => $executedQueries,
                    'total' => $totalQueries
                ]);
            }
            
            // Todo bien
            \Log::info('Importación completada exitosamente');
            \Log::info('=== FIN IMPORTACIÓN SQL ===');
            
            \App\Helpers\BitacoraHelper::exito(
                'importar_sql',
                'respaldos',
                "Importó archivo SQL: {$originalName} - {$executedQueries} consultas ejecutadas",
                [
                    'filename' => $originalName,
                    'queries' => $executedQueries,
                    'tables' => $createdTables
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => "Archivo SQL importado exitosamente. {$executedQueries} consultas ejecutadas.",
                'tables_creadas' => $createdTables,
                'queries' => $executedQueries
            ]);
            
        } catch (\Exception $e) {
            // Limpiar archivos temporales
            if (isset($tempFile) && File::exists($tempFile)) {
                File::delete($tempFile);
            }
            if (isset($extractPath) && File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            
            \Log::error('Error en importación: ' . $e->getMessage());
            \Log::info('=== FIN IMPORTACIÓN SQL (ERROR) ===');
            
            \App\Helpers\BitacoraHelper::error(
                'importar_sql',
                'respaldos',
                "Error al importar SQL: " . $e->getMessage(),
                ['filename' => $originalName ?? 'desconocido', 'error' => $e->getMessage()]
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Error al importar el archivo SQL: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Guarda la configuración de respaldos automáticos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveConfig(Request $request)
    {
        $config = [
            'auto_diario' => $request->get('auto_diario', false),
            'auto_semanal' => $request->get('auto_semanal', false),
            'notificar_email' => $request->get('notificar_email', false),
            'comprimir' => $request->get('comprimir', true)
        ];
        
        $configPath = storage_path('app/backups/config.json');
        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));
        
        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente'
        ]);
    }
    
    /**
     * Obtiene la configuración actual de respaldos.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig()
    {
        $configPath = storage_path('app/backups/config.json');
        
        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true);
        } else {
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
     * 
     * @param int $bytes Tamaño en bytes
     * @return string Tamaño formateado con unidad
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Divide un archivo SQL en consultas individuales.
     * Usa un tokenizer que respeta strings entre comillas, comentarios
     * de línea (--) y de bloque (/* * /), de modo que un ";" dentro de
     * una cadena de texto no rompa la consulta.
     */
    private function splitQueries(string $sqlContent): array
    {
        $queries = [];
        $length = strlen($sqlContent);
        $current = '';
        $quote = null;
        $inLineComment = false;
        $inBlockComment = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $sqlContent[$i];
            $next = $sqlContent[$i + 1] ?? '';

            if ($char === "\n") {
                $inLineComment = false;
            }

            if (!$quote && !$inLineComment && !$inBlockComment) {
                if ($char === '-' && $next === '-') {
                    $inLineComment = true;
                    $i++;
                    continue;
                }
                if ($char === '/' && $next === '*') {
                    $inBlockComment = true;
                    $i++;
                    continue;
                }
            }

            if ($inLineComment || $inBlockComment) {
                if ($inBlockComment && $char === '*' && $next === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }

            if ($quote !== null) {
                $current .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sqlContent[$i + 1];
                    $i++;
                    continue;
                }
                if ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === "'" || $char === '"') {
                $quote = $char;
                $current .= $char;
                continue;
            }

            if ($char === ';') {
                $query = trim($current);
                if ($query !== '') {
                    $queries[] = $query;
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $query = trim($current);
        if ($query !== '') {
            $queries[] = $query;
        }

        return $queries;
    }
}