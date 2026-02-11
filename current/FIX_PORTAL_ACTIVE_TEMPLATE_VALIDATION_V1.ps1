# ==========================================
# FIX_PORTAL_ACTIVE_TEMPLATE_VALIDATION_V1.ps1
# Fix: admin/portal validation rejects modern/minimal/bold
# Adds them to the "in:" rule for active_template (and similar keys if present)
# Safe: backup + idempotent
# ==========================================
$ErrorActionPreference = "Stop"

$BASE = "C:\sites\emc_abastos\current"
$file = Join-Path $BASE "app\Http\Controllers\Admin\PortalConfigController.php"

if (!(Test-Path $file)) { throw "No encuentro: $file" }

$ts = Get-Date -Format "yyyyMMdd-HHmmss"
$bkDir = Join-Path $BASE "storage\backups\portal-validation-$ts"
New-Item -ItemType Directory -Force -Path $bkDir | Out-Null
Copy-Item $file (Join-Path $bkDir "PortalConfigController.php.bak") -Force

Write-Host "✅ Backup: $bkDir\PortalConfigController.php.bak"

$c = Get-Content $file -Raw -Encoding UTF8

# Candidates keys that may exist in validation arrays
$keys = @("active_template","portal_template","template")

$changed = $false

foreach ($k in $keys) {
  # Replace common exact patterns (fast path)
  $patterns = @(
    "in:default,market_v2",
    "in:market_v2,default"
  )

  foreach ($p in $patterns) {
    $target = "in:default,market_v2,modern,minimal,bold"
    if ($c -match [regex]::Escape($p)) {
      $c = $c -replace [regex]::Escape($p), $target
      $changed = $true
    }
  }

  # Also handle cases like: "'active_template' => 'required|in:default,market_v2'"
  # by expanding the in-list if it exists but missing our new values.
  $rx = "(?s)('"+[regex]::Escape($k)+"'\s*=>\s*'[^']*?\bin:)([^'|]+)"
  $m = [regex]::Match($c, $rx)
  if ($m.Success) {
    $prefix = $m.Groups[1].Value
    $list   = $m.Groups[2].Value.Trim()

    # Normalize list and add missing values
    $items = $list.Split(",") | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne "" }
    $need = @("default","market_v2","modern","minimal","bold")
    foreach ($n in $need) { if ($items -notcontains $n) { $items += $n } }

    $newList = ($items | Select-Object -Unique) -join ","
    $c = [regex]::Replace($c, $rx, ("'"+$k+"' => '"+($m.Value -replace ("'"+$k+"' => '"),"") ))  # no-op safe
    # Replace just the list in the first match span
    $c = $c.Remove($m.Groups[2].Index, $m.Groups[2].Length).Insert($m.Groups[2].Index, $newList)
    $changed = $true
  }
}

if (!$changed) {
  Write-Host "⚠️ No encontré un in:default,market_v2 para parchear. Aun así dejo el backup y termino."
} else {
  Set-Content -Path $file -Value $c -Encoding UTF8
  Write-Host "✅ Validación actualizada para permitir: default, market_v2, modern, minimal, bold"
}

# Clear caches
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
Write-Host "✅ LISTO. Vuelve a /admin/portal y guarda Modern/Minimal/Bold."
