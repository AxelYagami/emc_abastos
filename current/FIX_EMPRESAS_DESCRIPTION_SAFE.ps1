# ==========================================
# FIX_EMPRESAS_DESCRIPTION_SAFE.ps1
# Elimina el campo "description" del flujo de guardado
# porque NO existe en la tabla empresas
# ==========================================

$ErrorActionPreference = "Stop"

$BASE = "C:\sites\emc_abastos\current"

$controller = "$BASE\app\Http\Controllers\Admin\EmpresasController.php"
$model      = "$BASE\app\Models\Empresa.php"

# ---- BACKUPS ----
foreach ($file in @($controller, $model)) {
    if (Test-Path $file) {
        Copy-Item $file "$file.bak" -Force
        Write-Host "Backup creado: $file.bak"
    } else {
        Write-Host "Archivo no encontrado (skip): $file"
    }
}

# ---- FIX CONTROLLER ----
if (Test-Path $controller) {
    $content = Get-Content $controller -Raw

    # Reemplaza request()->all() por except(description)
    if ($content -match "request\(\)->all\(\)") {
        $content = $content -replace `
            "request\(\)->all\(\)", `
            "collect(request()->all())->except(['description'])->toArray()"

        Set-Content $controller $content
        Write-Host "✔ description eliminado del request en EmpresasController"
    } else {
        Write-Host "ℹ request()->all() no encontrado en controller (skip)"
    }
}

# ---- FIX MODEL ----
if (Test-Path $model) {
    $content = Get-Content $model -Raw

    if ($content -match "description") {
        $content = $content -replace "'description',?", ""
        Set-Content $model $content
        Write-Host "✔ description eliminado de $fillable en Empresa"
    } else {
        Write-Host "ℹ description no encontrado en modelo (skip)"
    }
}

# ---- LIMPIAR CACHES ----
cd $BASE
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

Write-Host ""
Write-Host "✅ FIX COMPLETADO. El error SQL de description NO debe volver a salir."
