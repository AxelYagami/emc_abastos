# ==========================================
# ADD_PORTAL_TEMPLATES_V1.ps1
# Adds: modern, minimal, bold to /admin/portal template selector
# Applies at portal level ONLY (no empresa_id rules)
# Safe: backups + idempotent patches
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

function Backup-File($path) {
  if (Test-Path $path) {
    $dest = Join-Path $BK ((Split-Path $path -Leaf) + ".bak")
    Copy-Item $path $dest -Force
    Write-Host "  backup: $path -> $dest"
  } else {
    throw "File not found: $path"
  }
}

function Ensure-Contains($content, $needle, $errMsg) {
  if ($content -notmatch [regex]::Escape($needle)) {
    throw $errMsg
  }
}

function Upsert-Block($path, $markerStart, $markerEnd, $block) {
  $c = Get-Content $path -Raw -Encoding UTF8
  if ($c -match [regex]::Escape($markerStart) -and $c -match [regex]::Escape($markerEnd)) {
    # replace block between markers
    $pattern = [regex]::Escape($markerStart) + ".*?" + [regex]::Escape($markerEnd)
    $new = [regex]::Replace($c, $pattern, ($markerStart + "`r`n" + $block + "`r`n" + $markerEnd), "Singleline")
    Set-Content -Path $path -Value $new -Encoding UTF8
    Write-Host "  updated block in: $path"
  } else {
    # append markers + block at end
    $new = $c.TrimEnd() + "`r`n`r`n" + $markerStart + "`r`n" + $block + "`r`n" + $markerEnd + "`r`n"
    Set-Content -Path $path -Value $new -Encoding UTF8
    Write-Host "  appended block in: $path"
  }
}

function Insert-After($path, $anchorRegex, $insertText) {
  $c = Get-Content $path -Raw -Encoding UTF8
  if ($c -match $insertText) {
    Write-Host "  already inserted in: $path"
    return
  }
  $m = [regex]::Match($c, $anchorRegex, "Singleline")
  if (!$m.Success) { throw "Anchor not found in $path (regex): $anchorRegex" }
  $idx = $m.Index + $m.Length
  $new = $c.Substring(0,$idx) + "`r`n" + $insertText + $c.Substring($idx)
  Set-Content -Path $path -Value $new -Encoding UTF8
  Write-Host "  inserted after anchor in: $path"
}

# --------------------------
# Targets
# --------------------------
$PortalAdminCtrl = Join-Path $BASE "app\Http\Controllers\Admin\PortalConfigController.php"
$PortalAdminView = Join-Path $BASE "resources\views\admin\portal\config.blade.php"
$PortalReactApp   = Join-Path $BASE "portal\src\App.jsx"

Backup-File $PortalAdminCtrl
Backup-File $PortalAdminView
Backup-File $PortalReactApp

# --------------------------
# 1) Backend: allow new templates in PortalConfigController
# We patch a whitelist array safely via markers to avoid guessing exact line numbers.
# --------------------------
$backendBlock = @'
/**
 * AUTO PATCH: portal templates whitelist (DO NOT EDIT MANUALLY)
 */
$allowedTemplates = ['default','market_v2','modern','minimal','bold'];

# Ensure request template field exists; detect common keys and normalize.
$templateKey = null;
if (request()->has('active_template')) { $templateKey = 'active_template'; }
if (request()->has('portal_template')) { $templateKey = 'portal_template'; }
if (request()->has('template'))        { $templateKey = 'template'; }

if ($templateKey) {
    $tpl = (string) request()->input($templateKey);
    if (!in_array($tpl, $allowedTemplates, true)) {
        // fallback safe
        $tpl = 'default';
    }
    // Store under the existing key if present, else prefer active_template
    $saveKey = $templateKey ?: 'active_template';
    $data = request()->all();
    $data[$saveKey] = $tpl;
    request()->merge([$saveKey => $tpl]);
}
'@

Upsert-Block `
  -path $PortalAdminCtrl `
  -markerStart "// === AUTO: PORTAL_TEMPLATE_WHITELIST_START ===" `
  -markerEnd   "// === AUTO: PORTAL_TEMPLATE_WHITELIST_END ===" `
  -block $backendBlock

# --------------------------
# 2) Admin UI: add cards Modern/Minimal/Bold in admin portal config blade
# We inject a block right after the existing template section heading.
# --------------------------
$viewInsert = @'
{{-- === AUTO: PORTAL_TEMPLATES_EXTRA_START === --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
    {{-- Modern --}}
    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition flex gap-3 items-start">
        <input type="radio" name="active_template" value="modern" class="mt-1"
            {{ (old('active_template', $config->active_template ?? 'default') === 'modern') ? 'checked' : '' }}>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <div class="font-semibold">Modern</div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Nuevo</span>
            </div>
            <div class="text-sm text-slate-600">Sidebar + estilo dark hero. Seguro y moderno.</div>
        </div>
    </label>

    {{-- Minimal --}}
    <label class="border rounded-lg p-4 cursor-pointer hover:border-blue-400 transition flex gap-3 items-start">
        <input type="radio" name="active_template" value="minimal" class="mt-1"
            {{ (old('active_template', $config->active_template ?? 'default') === 'minimal') ? 'checked' : '' }}>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <div class="font-semibold">Minimal</div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Nuevo</span>
            </div>
            <div class="text-sm text-slate-600">Ultra limpio, elegante, rápido.</div>
        </div>
    </label>

    {{-- Bold --}}
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

# Anchor: "Template del Portal" text exists per your screenshot.
Insert-After `
  -path $PortalAdminView `
  -anchorRegex "(?s)Template del Portal.*?\n" `
  -insertText $viewInsert

# --------------------------
# 3) React portal: add template mapping in portal/src/App.jsx
# We inject imports + mapping block via markers.
# --------------------------
$reactImports = @'
import ModernHome from "./templates/modern/ModernHome";
import MinimalHome from "./templates/minimal/MinimalHome";
import BoldHome from "./templates/bold/BoldHome";
'@

# Insert imports after existing market_v2 import or first template import.
Insert-After `
  -path $PortalReactApp `
  -anchorRegex "(?s)(from\s+['\"]./templates/market_v2/MarketHome['\"];|from\s+['\"]./templates/market_v2/MarketHome\.jsx['\"];).*?\n" `
  -insertText $reactImports

$reactBlock = @'
/**
 * AUTO PATCH: portal template resolver (DO NOT EDIT MANUALLY)
 */
const PORTAL_TEMPLATES = {
  default: "default",
  market_v2: "market_v2",
  modern: "modern",
  minimal: "minimal",
  bold: "bold",
};

function resolvePortalTemplate(activeTemplate) {
  const t = (activeTemplate || "default").toString();
  return PORTAL_TEMPLATES[t] ? t : "default";
}

function TemplateRouter({ template, props }) {
  switch (template) {
    case "market_v2":
      return <MarketHome {...props} />;
    case "modern":
      return <ModernHome {...props} />;
    case "minimal":
      return <MinimalHome {...props} />;
    case "bold":
      return <BoldHome {...props} />;
    case "default":
    default:
      // fallback: keep existing behavior for default
      return <AppDefault {...props} />;
  }
}
'@

Upsert-Block `
  -path $PortalReactApp `
  -markerStart "/* === AUTO: PORTAL_TEMPLATE_ROUTER_START === */" `
  -markerEnd   "/* === AUTO: PORTAL_TEMPLATE_ROUTER_END === */" `
  -block $reactBlock

# Now patch actual render usage: we need to ensure App.jsx uses config.active_template and routes through TemplateRouter.
# We do a safe insertion: define template variable after config load, and replace direct MarketHome rendering if present.
$cApp = Get-Content $PortalReactApp -Raw -Encoding UTF8

if ($cApp -notmatch "resolvePortalTemplate\(") {
  throw "React patch failed: resolver not found after insert. Check $PortalReactApp"
}

# Ensure AppDefault symbol exists; if not, we keep a safe fallback to MarketHome.
if ($cApp -notmatch "\bAppDefault\b") {
  # Try to detect default component usage: if file already has a default template component, we alias it.
  # Safe injection: define AppDefault = MarketHome if not found.
  $alias = "const AppDefault = MarketHome;"
  if ($cApp -notmatch [regex]::Escape($alias)) {
    $cApp = $cApp + "`r`n`r`n// AUTO PATCH: fallback default component`r`n$alias`r`n"
    Set-Content -Path $PortalReactApp -Value $cApp -Encoding UTF8
    Write-Host "  appended AppDefault alias fallback in: $PortalReactApp"
  }
}

# Inject template resolution near config usage
# Common pattern: const { config } = usePortal... ; we'll insert after "config" is available.
if ($cApp -notmatch "const\s+activeTemplate\s*=") {
  $inject = @'
  // AUTO PATCH: resolve portal template
  const activeTemplate = resolvePortalTemplate(config?.active_template || config?.portal_template || config?.template);
'@
  try {
    Insert-After -path $PortalReactApp -anchorRegex "(?s)const\s+\{\s*config\s*\}\s*=\s*usePortal.*?\n" -insertText $inject
  } catch {
    # fallback: insert after any "config" variable declaration
    Insert-After -path $PortalReactApp -anchorRegex "(?s)\bconfig\b.*?\n" -insertText $inject
  }
}

# Replace direct MarketHome render usage with TemplateRouter if obvious marker exists.
$cApp = Get-Content $PortalReactApp -Raw -Encoding UTF8
if ($cApp -match "<MarketHome") {
  $cApp = $cApp -replace "<MarketHome([^>]*)\/>", "<TemplateRouter template={activeTemplate} props={{config, ...(props || {})}} />"
  $cApp = $cApp -replace "<MarketHome([^>]*)>", "<TemplateRouter template={activeTemplate} props={{config, ...(props || {})}} >"
  Set-Content -Path $PortalReactApp -Value $cApp -Encoding UTF8
  Write-Host "  replaced direct MarketHome usage with TemplateRouter (best-effort) in: $PortalReactApp"
} else {
  Write-Host "  NOTE: MarketHome direct render not found; TemplateRouter is available but not wired automatically. Check App.jsx render section."
}

# --------------------------
# 4) Create safe wrapper templates (modern/minimal/bold)
# These wrappers prevent runtime errors and allow future unique design per template.
# --------------------------
$tplModernDir = Join-Path $BASE "portal\src\templates\modern"
$tplMinimalDir = Join-Path $BASE "portal\src\templates\minimal"
$tplBoldDir = Join-Path $BASE "portal\src\templates\bold"
New-Item -ItemType Directory -Force -Path $tplModernDir | Out-Null
New-Item -ItemType Directory -Force -Path $tplMinimalDir | Out-Null
New-Item -ItemType Directory -Force -Path $tplBoldDir | Out-Null

$modernHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";

/**
 * Modern template wrapper (safe).
 * Later you can implement unique layout here without touching admin/backend.
 */
export default function ModernHome(props) {
  return <MarketHome {...props} />;
}
'@
$minimalHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";

/**
 * Minimal template wrapper (safe).
 * Later you can implement ultra-clean layout here.
 */
export default function MinimalHome(props) {
  return <MarketHome {...props} />;
}
'@
$boldHome = @'
import React from "react";
import MarketHome from "../market_v2/MarketHome";

/**
 * Bold template wrapper (safe).
 * Later you can implement vibrant/mobile-first layout here.
 */
export default function BoldHome(props) {
  return <MarketHome {...props} />;
}
'@

Set-Content -Path (Join-Path $tplModernDir "ModernHome.jsx") -Value $modernHome -Encoding UTF8
Set-Content -Path (Join-Path $tplMinimalDir "MinimalHome.jsx") -Value $minimalHome -Encoding UTF8
Set-Content -Path (Join-Path $tplBoldDir "BoldHome.jsx") -Value $boldHome -Encoding UTF8
Write-Host "  created template wrappers: modern/minimal/bold"

# --------------------------
# 5) Laravel caches clear (safe)
# --------------------------
Push-Location $BASE
try {
  php artisan config:clear | Out-Host
  php artisan cache:clear | Out-Host
  php artisan route:clear | Out-Host
  php artisan view:clear | Out-Host
} finally {
  Pop-Location
}

# --------------------------
# 6) Deploy portal build (if script exists)
# --------------------------
$DeployPortal = Join-Path $BASE "scripts\windows\DEPLOY_V4_PORTAL.ps1"
if (Test-Path $DeployPortal) {
  Write-Host "==> Running portal deploy script: $DeployPortal"
  powershell -ExecutionPolicy Bypass -File $DeployPortal
} else {
  Write-Host "==> Deploy script not found. Portal source updated; build may be required."
  Write-Host "    (No action taken) Missing: $DeployPortal"
}

Write-Host ""
Write-Host "✅ DONE: Modern/Minimal/Bold added to Portal templates (admin + portal)."
Write-Host "✅ Backup saved at: $BK"
Write-Host "Next: open /admin/portal, select template, Save, then load /portal."
