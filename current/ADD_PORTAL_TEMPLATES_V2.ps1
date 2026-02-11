# ==========================================
# ADD_PORTAL_TEMPLATES_V2.ps1
# Adds portal templates: modern, minimal, bold
# Safe: backups + idempotent + no brittle regex quoting
# Base: C:\sites\emc_abastos\current
# ==========================================
$ErrorActionPreference = "Stop"

$BASE = "C:\sites\emc_abastos\current"
if (!(Test-Path $BASE)) { throw "Base path not found: $BASE" }

$ts = Get-Date -Format "yyyyMMdd-HHmmss"
$BK = Join-Path $BASE "storage\backups\portal-templates-$ts"
New-Item -ItemType Directory -Force -Path $BK | Out-Null

Write-Host "==> BASE: $BASE"
Write-Host "==> BACKUP: $BK"

function Backup-File([string]$path) {
  if (!(Test-Path $path)) { throw "File not found: $path" }
  $leaf = Split-Path $path -Leaf
  Copy-Item $path (Join-Path $BK ($leaf + ".bak")) -Force
  Write-Host "  backup: $leaf"
}

function ReadUtf8([string]$path) {
  return Get-Content $path -Raw -Encoding UTF8
}

function WriteUtf8([string]$path, [string]$content) {
  Set-Content -Path $path -Value $content -Encoding UTF8
}

function UpsertBlock([string]$path, [string]$start, [string]$end, [string]$block) {
  $c = ReadUtf8 $path
  $s = $c.IndexOf($start)
  $e = $c.IndexOf($end)
  if ($s -ge 0 -and $e -ge 0 -and $e -gt $s) {
    $before = $c.Substring(0, $s)
    $after  = $c.Substring($e + $end.Length)
    $new = $before + $start + "`r`n" + $block + "`r`n" + $end + $after
    WriteUtf8 $path $new
    Write-Host "  updated block in: $(Split-Path $path -Leaf)"
  } else {
    $new = $c.TrimEnd() + "`r`n`r`n" + $start + "`r`n" + $block + "`r`n" + $end + "`r`n"
    WriteUtf8 $path $new
    Write-Host "  appended block in: $(Split-Path $path -Leaf)"
  }
}

function InsertAfterFirst([string]$path, [string]$needle, [string]$insert) {
  $c = ReadUtf8 $path
  if ($c.Contains($insert)) {
    Write-Host "  already inserted in: $(Split-Path $path -Leaf)"
    return
  }
  $idx = $c.IndexOf($needle)
  if ($idx -lt 0) { throw "Anchor not found in $(Split-Path $path -Leaf): $needle" }
  $pos = $idx + $needle.Length
  $new = $c.Substring(0, $pos) + "`r`n" + $insert + $c.Substring($pos)
  WriteUtf8 $path $new
  Write-Host "  inserted after anchor in: $(Split-Path $path -Leaf)"
}

function EnsureDir([string]$path) {
  New-Item -ItemType Directory -Force -Path $path | Out-Null
}

# --------------------------
# Targets
# --------------------------
$PortalAdminCtrl = Join-Path $BASE "app\Http\Controllers\Admin\PortalConfigController.php"
$PortalAdminView = Join-Path $BASE "resources\views\admin\portal\config.blade.php"
$PortalReactApp  = Join-Path $BASE "portal\src\App.jsx"

Backup-File $PortalAdminCtrl
Backup-File $PortalAdminView
Backup-File $PortalReactApp

# --------------------------
# 1) Backend: enforce allowed templates (safe marker block)
# --------------------------
$backendBlock = @'
/**
 * AUTO PATCH V2: portal templates whitelist (DO NOT EDIT MANUALLY)
 * Adds: modern, minimal, bold
 */
$allowedTemplates = ['default','market_v2','modern','minimal','bold'];

// Detect the incoming key used by the form / API
$templateKey = null;
if (request()->has('active_template')) { $templateKey = 'active_template'; }
elseif (request()->has('portal_template')) { $templateKey = 'portal_template'; }
elseif (request()->has('template')) { $templateKey = 'template'; }

if ($templateKey) {
    $tpl = (string) request()->input($templateKey);
    if (!in_array($tpl, $allowedTemplates, true)) {
        $tpl = 'default';
    }
    request()->merge([$templateKey => $tpl]);
}
'@

UpsertBlock `
  -path $PortalAdminCtrl `
  -start "// === AUTO: PORTAL_TEMPLATE_WHITELIST_START ===" `
  -end   "// === AUTO: PORTAL_TEMPLATE_WHITELIST_END ===" `
  -block $backendBlock

# --------------------------
# 2) Admin UI: add Modern/Minimal/Bold cards (safe insert after "Template del Portal")
# --------------------------
$viewInsert = @'
{{-- === AUTO: PORTAL_TEMPLATES_EXTRA_START === --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition flex gap-3 items-start">
        <input type="radio" name="active_template" value="modern" class="mt-1"
            {{ (old('active_template', $config->active_template ?? 'default') === 'modern') ? 'checked' : '' }}>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <div class="font-semibold">Modern</div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Nuevo</span>
            </div>
            <div class="text-sm text-slate-600">Sidebar + Dark hero, look moderno.</div>
        </div>
    </label>

    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition flex gap-3 items-start">
        <input type="radio" name="active_template" value="minimal" class="mt-1"
            {{ (old('active_template', $config->active_template ?? 'default') === 'minimal') ? 'checked' : '' }}>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <div class="font-semibold">Minimal</div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Nuevo</span>
            </div>
            <div class="text-sm text-slate-600">Ultra limpio, elegante.</div>
        </div>
    </label>

    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition flex gap-3 items-start">
        <input type="radio" name="active_template" value="bold" class="mt-1"
            {{ (old('active_template', $config->active_template ?? 'default') === 'bold') ? 'checked' : '' }}>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <div class="font-semibold">Bold</div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Nuevo</span>
            </div>
            <div class="text-sm text-slate-600">Vibrante, tipo app mobile.</div>
        </div>
    </label>
</div>
{{-- === AUTO: PORTAL_TEMPLATES_EXTRA_END === --}}
'@

# Insert after first occurrence of "Template del Portal"
InsertAfterFirst -path $PortalAdminView -needle "Template del Portal" -insert $viewInsert

# --------------------------
# 3) React: add wrapper templates + safe router block
# --------------------------
$tplModernDir  = Join-Path $BASE "portal\src\templates\modern"
$tplMinimalDir = Join-Path $BASE "portal\src\templates\minimal"
$tplBoldDir    = Join-Path $BASE "portal\src\templates\bold"

EnsureDir $tplModernDir
EnsureDir $tplMinimalDir
EnsureDir $tplBoldDir

$modernHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";
export default function ModernHome(props){ return <MarketHome {...props} />; }
'@
$minimalHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";
export default function MinimalHome(props){ return <MarketHome {...props} />; }
'@
$boldHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";
export default function BoldHome(props){ return <MarketHome {...props} />; }
'@

WriteUtf8 (Join-Path $tplModernDir  "ModernHome.jsx")  $modernHome
WriteUtf8 (Join-Path $tplMinimalDir "MinimalHome.jsx") $minimalHome
WriteUtf8 (Join-Path $tplBoldDir    "BoldHome.jsx")    $boldHome
Write-Host "  created wrapper templates: modern/minimal/bold"

# Add imports safely: append near top if not present
$app = ReadUtf8 $PortalReactApp
if ($app -notmatch "ModernHome") {
  # Find end of import section (first blank line after imports)
  $lines = Get-Content $PortalReactApp -Encoding UTF8
  $insertAt = 0
  for ($i=0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -match "^\s*$" -and $i -gt 0) { $insertAt = $i; break }
  }
  $imports = @(
    'import ModernHome from "./templates/modern/ModernHome";'
    'import MinimalHome from "./templates/minimal/MinimalHome";'
    'import BoldHome from "./templates/bold/BoldHome";'
    ''
  )
  $newLines = New-Object System.Collections.Generic.List[string]
  for ($i=0; $i -lt $lines.Count; $i++) {
    $newLines.Add($lines[$i])
    if ($i -eq $insertAt) {
      foreach($l in $imports){ $newLines.Add($l) }
    }
  }
  WriteUtf8 $PortalReactApp ($newLines -join "`r`n")
  Write-Host "  added React imports in App.jsx"
}

# Router block via markers (no guessy replacements)
$routerBlock = @'
/**
 * AUTO PATCH V2: portal template router
 */
function resolvePortalTemplate(config){
  const t = (config?.active_template || config?.portal_template || config?.template || "default").toString();
  if (t === "market_v2" || t === "default" || t === "modern" || t === "minimal" || t === "bold") return t;
  return "default";
}

// NOTE: wire this into your render where you currently pick a template.
// Example usage (you can keep your existing logic and just swap component):
// const tpl = resolvePortalTemplate(config);
// const Home = tpl==="market_v2"?MarketHome : tpl==="modern"?ModernHome : tpl==="minimal"?MinimalHome : tpl==="bold"?BoldHome : DefaultHome;
'@

UpsertBlock `
  -path $PortalReactApp `
  -start "/* === AUTO: PORTAL_TEMPLATE_ROUTER_START === */" `
  -end   "/* === AUTO: PORTAL_TEMPLATE_ROUTER_END === */" `
  -block $routerBlock

# --------------------------
# 4) Laravel caches clear
# --------------------------
Push-Location $BASE
try {
  php artisan config:clear | Out-Host
  php artisan cache:clear  | Out-Host
  php artisan route:clear  | Out-Host
  php artisan view:clear   | Out-Host
} finally {
  Pop-Location
}

# --------------------------
# 5) Deploy portal build (if script exists)
# --------------------------
$DeployPortal = Join-Path $BASE "scripts\windows\DEPLOY_V4_PORTAL.ps1"
if (Test-Path $DeployPortal) {
  Write-Host "==> Running portal deploy: $DeployPortal"
  powershell -ExecutionPolicy Bypass -File $DeployPortal
} else {
  Write-Host "==> Deploy script not found. If needed:"
  Write-Host "   cd $BASE\portal ; npm ci ; npm run build"
}

Write-Host ""
Write-Host "✅ DONE V2: Added Modern/Minimal/Bold to /admin/portal + created portal template wrappers."
Write-Host "✅ Backup: $BK"
Write-Host "Next: Open /admin/portal, select template, Save, then load /portal."
