# ==========================================
# FIX_EMPRESA_DESCRIPTION_COLUMN_MISSING_V2.ps1
# Fix: DB has NO empresas.description column, but code tries to set it.
# Solution: Add mutator in Empresa model to ignore "description" assignments.
# Safe: Backup + idempotent + no DB changes
# Base: C:\sites\emc_abastos\current
# ==========================================
$ErrorActionPreference = "Stop"

$BASE = "C:\sites\emc_abastos\current"
$EmpresaModel = Join-Path $BASE "app\Models\Empresa.php"

if (!(Test-Path $EmpresaModel)) { throw "No encuentro: $EmpresaModel" }

$ts = Get-Date -Format "yyyyMMdd-HHmmss"
$BK = Join-Path $BASE "storage\backups\empresa-description-fix-$ts"
New-Item -ItemType Directory -Force -Path $BK | Out-Null

Copy-Item $EmpresaModel (Join-Path $BK "Empresa.php.bak") -Force
Write-Host "✅ Backup: $BK\Empresa.php.bak"

$content = Get-Content $EmpresaModel -Raw -Encoding UTF8

# If mutator already exists, exit clean
if ($content -match "function\s+setDescriptionAttribute\s*\(") {
  Write-Host "ℹ Ya existe setDescriptionAttribute() en Empresa.php. No hago cambios."
} else {
  $mutator = @'

    /**
     * AUTO FIX: The database table "empresas" does NOT have a "description" column.
     * Some code paths try to set it (e.g., syncing/seed/admin forms).
     * We ignore the assignment to prevent SQLSTATE[42703] undefined column.
     */
    public function setDescriptionAttribute($value): void
    {
        // Intentionally ignore; do NOT set $this->attributes['description'].
    }

'@

  # Insert mutator before the last closing brace of the class
  $lastBrace = $content.LastIndexOf("}")
  if ($lastBrace -lt 0) { throw "No pude localizar el cierre de clase en Empresa.php" }

  $newContent = $content.Substring(0, $lastBrace) + $mutator + "`r`n" + $content.Substring($lastBrace)
  Set-Content -Path $EmpresaModel -Value $newContent -Encoding UTF8
  Write-Host "✅ Insertado setDescriptionAttribute() en app\Models\Empresa.php"
}

# Clear Laravel caches (safe)
Push-Location $BASE
try {
  php artisan config:clear | Out-Host
  php artisan cache:clear  | Out-Host
  php artisan route:clear  | Out-Host
  php artisan view:clear   | Out-Host
} finally {
  Pop-Location
}

Write-Host ""
Write-Host "✅ LISTO: cualquier intento de guardar empresas.description será ignorado."
Write-Host "➡️ Prueba: guarda de nuevo en /admin/portal o /admin/empresas."
