# ============================================================
# empaquetar.ps1 - Construye la version portable de escritorio
# de SIPCE para Windows.
#
# Uso:
#   powershell -ExecutionPolicy Bypass -File desktop\empaquetar.ps1
#
# NOTA: la construccion se hace en la carpeta temporal (FUERA del
# arbol del proyecto) para evitar recursiones, y al final el
# resultado se copia a desktop\output\ del proyecto.
#
# Salida:
#   desktop\output\SIPCE\             (carpeta portable lista)
#   desktop\output\SIPCE-Windows.zip  (zip para distribuir)
# ============================================================

$ErrorActionPreference = "Stop"

$Root      = Split-Path -Parent $PSScriptRoot          # C:\Users\User\sipce
$PhpPath   = "C:\xampp\php"
$WorkDir   = "C:\Users\User\AppData\Local\Temp\opencode\sipce-desktop"
$Dest      = Join-Path $WorkDir "SIPCE"
$ProjOut   = Join-Path $PSScriptRoot "output"
$Csc       = "C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe"

# Carpetas de la aplicacion a copiar (lista blanca => sin recursiones)
$AppDirs   = @("app","bootstrap","config","database","public","resources",
               "routes","storage","tests","vendor")
$AppFiles  = @("artisan","composer.json","composer.lock","package.json",
               "phpunit.xml",".env.example")

Write-Host "== Empaquetando SIPCE Desktop ==" -ForegroundColor Cyan

# ---- 0) Limpiar zona de trabajo temporal ----
if (Test-Path $WorkDir) { Remove-Item -Recurse -Force $WorkDir }
New-Item -ItemType Directory -Path $Dest -Force | Out-Null

# ---- 1) Copiar runtime PHP portable ----
Write-Host "[1/5] Copiando runtime PHP..." -ForegroundColor Yellow
if (-not (Test-Path "$PhpPath\php.exe")) { throw "No se encontro PHP en $PhpPath" }
New-Item -ItemType Directory -Path "$Dest\php" -Force | Out-Null
& robocopy $PhpPath "$Dest\php" /E /NFL /NDL /NJH /NJS /NP | Out-Null
if ($LASTEXITCODE -ge 8) { $LASTEXITCODE = 0 }   # robocopy usa codigos 1-7 = OK

# php.ini propio con rutas relativas y extensiones necesarias
$phpIni = @"
extension_dir = "ext"
extension=pdo_pgsql
extension=pgsql
extension=openssl
extension=mbstring
extension=curl
extension=fileinfo
extension=gd
extension=pdo_mysql
extension=pdo_sqlite
extension=sqlite3
extension=zip
extension=intl
extension=exif
zend_extension=opcache
date.timezone = America/Caracas
memory_limit = 512M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
"@
@("php.ini","php.ini-production","php.ini-development") | ForEach-Object {
    if (Test-Path "$Dest\php\$_") { Remove-Item "$Dest\php\$_" -Force }
}
Set-Content -Path "$Dest\php\php.ini" -Value $phpIni -Encoding ASCII

# ---- 2) Copiar aplicacion Laravel (lista blanca) ----
Write-Host "[2/5] Copiando aplicacion Laravel..." -ForegroundColor Yellow
$appDir = Join-Path $Dest "app"
New-Item -ItemType Directory -Path $appDir -Force | Out-Null
foreach ($d in $AppDirs) {
    $src = Join-Path $Root $d
    if (Test-Path $src) {
        Copy-Item $src -Destination $appDir -Recurse -Force
    } else {
        Write-Host "   (aviso: no existe la carpeta $d)" -ForegroundColor Gray
    }
}
foreach ($f in $AppFiles) {
    $src = Join-Path $Root $f
    if (Test-Path $src) { Copy-Item $src -Destination $appDir -Force }
}
# .env desktop (SQLite local + sesiones en archivo + sincronizacion contra el servidor)
if (-not (Test-Path "$Root\.env")) { throw "No existe el .env del proyecto" }
# la carpeta database del repo puede contener database.sqlite (desarrollo):
# NO debe entrar en el paquete, el launcher crea la BD en el primer inicio.
Get-ChildItem -Path "$appDir\database" -Recurse -Filter '*.sqlite' -ErrorAction SilentlyContinue | Remove-Item -Force
$repoEnv = Get-Content "$Root\.env" -Raw
$appKey = [regex]::Match($repoEnv, '(?m)^APP_KEY=(.+)$').Groups[1].Value.Trim()
if (-not $appKey) { throw "El .env del proyecto no tiene APP_KEY" }
@"
APP_NAME=SIPCE Desktop
APP_ENV=local
APP_KEY=$appKey
APP_DEBUG=false
APP_URL=http://127.0.0.1:8899

LOG_CHANNEL=stack
LOG_LEVEL=warning

# Sin DB_DATABASE a proposito: Laravel usa database_path('database.sqlite')
# que en el paquete es <app>\database\database.sqlite (donde el launcher la crea).
# DB_URL vacio evita que la conexion sqlite herede la URL de pgsql/Neon.
DB_CONNECTION=sqlite
DB_URL=

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log

SYNC_OUTBOX=true
SYNC_API_URL=https://sipce.onrender.com
SYNC_EMAIL=admin@example.com
SYNC_PASSWORD=password123
"@ | Set-Content -Path "$appDir\.env" -Encoding ASCII
# Solo el directorio database: el launcher crea la BD con sipce:init-local
# en el primer arranque (si existiera un archivo vacío, se saltaría la inicialización).
New-Item -ItemType Directory -Force -Path "$appDir\database" | Out-Null
# router del servidor PHP para el launcher (emula mod_rewrite)
Copy-Item "$PSScriptRoot\launcher\server.php" "$appDir\public\server.php" -Force
# estructura writable requerida por artisan
New-Item -ItemType Directory -Force -Path "$appDir\bootstrap\cache" | Out-Null
New-Item -ItemType Directory -Force -Path "$appDir\storage\framework\cache\data" | Out-Null
New-Item -ItemType Directory -Force -Path "$appDir\storage\framework\sessions" | Out-Null
New-Item -ItemType Directory -Force -Path "$appDir\storage\framework\views" | Out-Null
New-Item -ItemType Directory -Force -Path "$appDir\storage\logs" | Out-Null

# ---- 3) Compilar launcher SIPCE.exe ----
Write-Host "[3/5] Compilando launcher SIPCE.exe..." -ForegroundColor Yellow
if (-not (Test-Path $Csc)) { throw "No se encontro csc.exe (necesario para compilar el launcher)" }
& $Csc /nologo /target:winexe /out:"$Dest\SIPCE.exe" `
    /reference:System.Windows.Forms.dll `
    /reference:System.Drawing.dll `
    "$PSScriptRoot\launcher\SIPCE.cs" | Out-Null
if (-not (Test-Path "$Dest\SIPCE.exe")) { throw "Fallo al compilar el launcher" }

# ---- 4) Manual ----
Write-Host "[4/5] Generando manual..." -ForegroundColor Yellow
$readme = @"
============================================
 SIPCE Desktop (Windows) - App portable
============================================

COMO USARLA:
  1. Descomprime la carpeta en el lugar que quieras
     (ej: C:\SIPCE o un USB).
  2. Haz doble clic en  SIPCE.exe
  3. Se inicia automaticamente el servidor local y
     se abre el navegador en la aplicacion SIPCE.
  4. Para cerrar: clic derecho en el icono de la
     bandeja (junto al reloj) -> Salir.

DATOS:
  - La aplicacion funciona SIN internet: usa una base de datos
    local (SQLite) dentro de app\database, creada automaticamente
    en el primer arranque.
  - Cuando hay conexion, sincroniza con la version web (nube) en
    segundo plano: los cambios offline se suben al volver la senal
    y se descargan los de otras maquinas.
  - Estado de sincronizacion: icono del reloj en la barra superior.
    Clic en el icono fuerza una sincronizacion inmediata.

ACCESOS:
  - Usuario administrador: admin@example.com / password123

REQUISITOS:
  - Windows 10/11 (x64).
  - Internet solo para sincronizar con la nube (opcional).

SOLUCION DE PROBLEMAS:
  - Si el navegador no se abre solo, ve a:
      http://127.0.0.1:8899/login
  - Si el puerto 8899 esta ocupado, cierra la app que
    lo use y vuelve a abrir SIPCE.exe.
  - Revisa el log en: app\storage\logs\desktop.log
"@
Set-Content -Path "$Dest\LEEME.txt" -Value $readme -Encoding UTF8

# ---- 5) ZIP + copia final al proyecto ----
Write-Host "[5/5] Generando ZIP y copia final..." -ForegroundColor Yellow
$zip = Join-Path $WorkDir "SIPCE-Windows.zip"
if (Test-Path $zip) { Remove-Item $zip -Force }
Compress-Archive -Path "$Dest\*" -DestinationPath $zip -CompressionLevel Optimal

# copiar el resultado al proyecto (desde fuera del arbol fuente => sin recursion)
New-Item -ItemType Directory -Path $ProjOut -Force | Out-Null
if (Test-Path "$ProjOut\SIPCE") { Remove-Item -Recurse -Force "$ProjOut\SIPCE" }
Copy-Item $Dest -Destination $ProjOut -Recurse -Force
Copy-Item $zip -Destination $ProjOut -Force

$sizeMb = [math]::Round(((Get-ChildItem "$ProjOut\SIPCE" -Recurse -File | Measure-Object Length -Sum).Sum / 1MB), 1)
Write-Host ""
Write-Host "Listo!" -ForegroundColor Green
Write-Host "  Carpeta portable : $ProjOut\SIPCE  ($sizeMb MB)"
Write-Host "  ZIP distribucion : $ProjOut\SIPCE-Windows.zip"