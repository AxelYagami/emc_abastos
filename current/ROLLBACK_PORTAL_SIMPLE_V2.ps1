# ==========================================
# ROLLBACK_PORTAL_SIMPLE_V2.ps1
# Restores known files from .bak if they exist
# NO complex logic, NO broken syntax
# ==========================================

$BASE = "C:\sites\emc_abastos\current"

$files = @(
  "app\Http\Controllers\Admin\PortalConfigController.php",
  "resources\views\admin\portal\config.blade.php",
  "portal\src\App.jsx",
  "app\Models\Empresa.php"
)

foreach ($rel in $files) {
  $file = Join-Path $BASE $rel
  $bak  = "$file.bak"

  if (Test-Path $bak) {
    Copy-Item $bak $file -Force
    Write-Host "✔ Restored: $rel"
  } else {
    Write-Host "ℹ No backup found for: $rel (skip)"
  }
}

# Remove added portal templates to avoid broken imports
$tplDirs = @(
  "$BASE\portal\src\templates\modern",
  "$BASE\portal\src\templates\minimal",
  "$BASE\portal\src\templates\bold"
)

foreach ($d in $tplDirs) {
  if (Test-Path $d) {
    Remove-Item $d -Recurse -Force
    Write-Host "🧹 Removed: $d"
  }
}

# Clear Laravel caches
cd $BASE
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

Write-Host ""
Write-Host "✅ SIMPLE ROLLBACK COMPLETED."
Write-Host "➡️ Restart Laravel and hard-refresh the browser."
