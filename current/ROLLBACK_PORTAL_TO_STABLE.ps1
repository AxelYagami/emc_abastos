# ==========================================
# ROLLBACK_PORTAL_TO_STABLE.ps1
# Restores portal + admin portal config to last known stable backups
# Safe: snapshot current state first
# Base: C:\sites\emc_abastos\current
# ==========================================
$ErrorActionPreference = "Stop"

$BASE = "C:\sites\emc_abastos\current"
if (!(Test-Path $BASE)) { throw "Base path not found: $BASE" }

function Latest-Dir($pattern) {
  $root = Join-Path $BASE "storage\backups"
  if (!(Test-Path $root)) { return $null }
  $dirs = Get-ChildItem $root -Directory -Filter $pattern -ErrorAction SilentlyContinue | Sort-Object LastWriteTime -Descending
  if ($dirs.Count -gt 0) { return $dirs[0].FullName }
  return $null
}

function Snapshot-Now($paths) {
  $ts = Get-Date -Format "yyyyMMdd-HHmmss"
  $snap = Join-Path $BASE "storage\backups\SNAPSHOT_before_rollback_$ts"
  New-Item -ItemType Directory -Force -Path $snap | Out-Null
  foreach ($p in $paths) {
    $full = Join-Path $BASE $p
    if (Test-Path $full) {
      $leaf = Split-Path $full -Leaf
      Copy-Item $full (Join-Path $snap "$leaf.current") -Force
    }
  }
  Write-Host "✅ Snapshot actual guardado en: $snap"
}

function Restore-From($backupDir, $fileName, $targetRel) {
  if (!$backupDir) { return $false }
  $src = Join-Path $backupDir $fileName
  $dst = Join-Path $BASE $targetRel
  if (Test-Path $src -and Test-Path $dst) {
    Copy-Item $src $dst -Force
    Write-Host "✅ Restore: $targetRel <= $(Split-Path $backupDir -Leaf)\$fileName"
    return $true
  }
  return $false
}

Write-Host "==> BASE: $BASE"

$targets = @(
  "app\Http\Controllers\Admin\PortalConfigController.php",
  "resources\views\admin\portal\config.blade.php",
  "portal\src\App.jsx",
  "app\Models\Empresa.php"
)

Snapshot-Now $targets

# Find latest backups created by our scripts
$bkPortalTpl = Latest-Dir "portal-templates-*"
$bkPortalVal = Latest-Dir "portal-validation-*"
$bkEmpresa   = Latest-Dir "empresa-description-fix-*"
$bkEmpresa2  = Latest-Dir "empresa-description-fix-*"
$bkAnyPortal = $bkPortalTpl
if (!$bkAnyPortal) { $bkAnyPortal = $bkPortalVal }

Write-Host "==> Latest portal templates backup: $bkPortalTpl"
Write-Host "==> Latest portal validation backup: $bkPortalVal"
Write-Host "==> Latest empresa fix backup: $bkEmpresa"

# Restore PortalConfigController.php (prefer validation backup if exists, else portal templates backup)
$restoredCtrl = $false
if ($bkPortalVal) {
  $restoredCtrl = Restore-From $bkPortalVal "PortalConfigController.php.bak" "app\Http\Controllers\Admin\PortalConfigController.php"
}
if (!$restoredCtrl -and $bkPortalTpl) {
  $restoredCtrl = Restore-From $bkPortalTpl "PortalConfigController.php.bak" "app\Http\Controllers\Admin\PortalConfigController.php"
}

# Restore admin portal config blade (from portal templates backup)
$restoredView = $false
if ($bkPortalTpl) {
  $restoredView = Restore-From $bkPortalTpl "config.blade.php.bak" "resources\views\admin\portal\config.blade.php"
}

# Restore React App.jsx (from portal templates backup)
$restoredApp = $false
if ($bkPortalTpl) {
  $restoredApp = Restore-From $bkPortalTpl "App.jsx.bak" "portal\src\App.jsx"
}

# Restore Empresa.php (if we backed it up anywhere)
$restoredEmpresa = $false
# Our earlier scripts stored Empresa.php.bak inside empresa-description-fix-* OR general snapshot in other backups
if ($bkEmpresa) {
  $maybe = Join-Path $bkEmpresa "Empresa.php.bak"
  if (Test-Path $maybe) {
    Copy-Item $maybe (Join-Path $BASE "app\Models\Empresa.php") -Force
    Write-Host "✅ Restore: app\Models\Empresa.php <= $(Split-Path $bkEmpresa -Leaf)\Empresa.php.bak"
    $restoredEmpresa = $true
  }
}

# Remove added template directories to prevent broken imports/build
$tplDirs = @(
  Join-Path $BASE "portal\src\templates\modern",
  Join-Path $BASE "portal\src\templates\minimal",
  Join-Path $BASE "portal\src\templates\bold"
)
foreach ($d in $tplDirs) {
  if (Test-Path $d) {
    Remove-Item $d -Recurse -Force
    Write-Host "🧹 Removed: $d"
  }
}

# Clear Laravel caches
Push-Location $BASE
try {
  php artisan config:clear | Out-Host
  php artisan cache:clear  | Out-Host
  php artisan route:clear  | Out-Host
  php artisan view:clear   | Out-Host
} catch {
  Write-Host "⚠️ Cache clear warning: $($_.Exception.Message)"
} finally {
  Pop-Location
}

# Rebuild portal if deploy script exists
$deploy = Join-Path $BASE "scripts\windows\DEPLOY_V4_PORTAL.ps1"
if (Test-Path $deploy) {
  Write-Host "==> Rebuilding portal using: $deploy"
  powershell -ExecutionPolicy Bypass -File $deploy
} else {
  Write-Host "==> Deploy script not found. If portal is broken, run build manually:"
  Write-Host "   cd $BASE\portal ; npm ci ; npm run build"
}

Write-Host ""
Write-Host "✅ ROLLBACK COMPLETED."
Write-Host "👉 Ahora reinicia tu server Laravel (cierra y vuelve a correr artisan serve) y recarga duro el portal (Ctrl+F5)."
