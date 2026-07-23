<#
.SYNOPSIS
    Despliega el sitio SOLUCTEM al servidor de produccion via SFTP.
    La primera ejecucion instala automaticamente el modulo Posh-SSH.

.PARAMETER File
    Archivo especifico a subir (ruta absoluta o relativa a la raiz del proyecto).

.PARAMETER Folder
    Carpeta especifica a subir (ruta absoluta o relativa a la raiz del proyecto).

.EXAMPLE
    .\deploy.ps1
    .\deploy.ps1 -File admin/form/edit.php
    .\deploy.ps1 -Folder admin/form
#>

param(
    [string]$File   = "",
    [string]$Folder = ""
)

# -- Configuracion -------------------------------------------------------------
$SERVER      = "soluctem.com.co"
$SSH_USER    = "soluctem"
$SSH_PASS    = "32uv[lSCt8SU;0"
$REMOTE_ROOT = "/home/soluctem/sistema.soluctem.com.co"
$LOCAL_ROOT  = $PSScriptRoot.TrimEnd('\')

# Nombres y extensiones que se excluyen en subida completa
$SKIP_NAMES = @('.git', '_notes', 'deploy.ps1', 'deploy.log', 'CLAUDE.md', 'CLAUDE.md.bak')
$SKIP_EXT   = @('.md')

# -- Modulo Posh-SSH -----------------------------------------------------------
if (-not (Get-Module -ListAvailable -Name Posh-SSH)) {
    Write-Host ""
    Write-Host "  Instalando modulo Posh-SSH (solo la primera vez)..." -ForegroundColor Yellow
    try {
        Install-Module -Name Posh-SSH -Scope CurrentUser -Force -AllowClobber -Repository PSGallery -ErrorAction Stop
        Write-Host "  Posh-SSH instalado correctamente." -ForegroundColor Green
    } catch {
        Write-Host "  ERROR al instalar Posh-SSH: $_" -ForegroundColor Red
        Write-Host "  Ejecuta manualmente: Install-Module -Name Posh-SSH -Scope CurrentUser -Force" -ForegroundColor Yellow
        exit 1
    }
}
Import-Module Posh-SSH -ErrorAction Stop

# -- Conexion SFTP -------------------------------------------------------------
$secPass = ConvertTo-SecureString $SSH_PASS -AsPlainText -Force
$cred    = New-Object System.Management.Automation.PSCredential($SSH_USER, $secPass)

Write-Host ""
Write-Host "  Conectando a $SERVER..." -ForegroundColor Yellow
try {
    $session = New-SFTPSession -ComputerName $SERVER -Credential $cred -AcceptKey -ErrorAction Stop
} catch {
    Write-Host "  ERROR al conectar: $_" -ForegroundColor Red
    exit 1
}
$sid = $session.SessionId

# -- Funciones -----------------------------------------------------------------
function Ensure-RemoteDir([string]$path) {
    $parts   = $path.TrimStart('/').Split('/', [System.StringSplitOptions]::RemoveEmptyEntries)
    $current = ""
    foreach ($part in $parts) {
        $current += "/$part"
        if (-not (Test-SFTPPath -SessionIndex $sid -Path $current)) {
            try { New-SFTPDirectory -SessionIndex $sid -Path $current | Out-Null } catch {}
        }
    }
}

function Upload-OneFile([string]$localFile, [string]$remoteDir) {
    Ensure-RemoteDir $remoteDir
    $name = Split-Path $localFile -Leaf
    Write-Host "    + $remoteDir/$name" -ForegroundColor Gray
    Set-SFTPFile -SessionIndex $sid -LocalFile $localFile -RemotePath $remoteDir -Overwrite
}

function Should-Skip([string]$name, [string]$ext) {
    if ($SKIP_NAMES -contains $name) { return $true }
    if ($ext -ne "" -and $SKIP_EXT -contains $ext.ToLower()) { return $true }
    return $false
}

function Upload-Dir([string]$localDir, [string]$remoteDir) {
    Ensure-RemoteDir $remoteDir

    Get-ChildItem -Path $localDir -File | ForEach-Object {
        if (-not (Should-Skip $_.Name $_.Extension)) {
            Upload-OneFile $_.FullName $remoteDir
        }
    }

    Get-ChildItem -Path $localDir -Directory | ForEach-Object {
        if (-not (Should-Skip $_.Name "")) {
            Upload-Dir $_.FullName "$remoteDir/$($_.Name)"
        }
    }
}

function Get-RelPath([string]$base, [string]$target) {
    $base   = $base.TrimEnd('\') + '\'
    $target = [System.IO.Path]::GetFullPath($target)
    if ($target.StartsWith($base, [System.StringComparison]::OrdinalIgnoreCase)) {
        return $target.Substring($base.Length).Replace('\', '/')
    }
    return $target.Replace('\', '/')
}

# -- Ejecutar ------------------------------------------------------------------
Write-Host ""
Write-Host "  ====================================" -ForegroundColor Cyan
Write-Host "  SOLUCTEM Deploy SFTP" -ForegroundColor Cyan
Write-Host "  ====================================" -ForegroundColor Cyan
Write-Host ""

$ok = $false
try {
    if ($File -ne "") {
        # --- Archivo especifico ---
        $absFile = if ([System.IO.Path]::IsPathRooted($File)) { $File } else { Join-Path $LOCAL_ROOT $File }
        $absFile = [System.IO.Path]::GetFullPath($absFile)

        if (-not (Test-Path $absFile -PathType Leaf)) {
            throw "Archivo no encontrado: $absFile"
        }

        $relPath   = Get-RelPath $LOCAL_ROOT $absFile
        $remoteDir = $REMOTE_ROOT
        $relDir    = ($relPath -replace '/[^/]+$', '')
        if ($relDir -ne $relPath) { $remoteDir = "$REMOTE_ROOT/$relDir" }

        Write-Host "  Modo    : archivo" -ForegroundColor White
        Write-Host "  Archivo : $relPath" -ForegroundColor Gray
        Write-Host ""
        Upload-OneFile $absFile $remoteDir

    } elseif ($Folder -ne "") {
        # --- Carpeta especifica ---
        $absFolder = if ([System.IO.Path]::IsPathRooted($Folder)) { $Folder } else { Join-Path $LOCAL_ROOT $Folder }
        $absFolder = [System.IO.Path]::GetFullPath($absFolder).TrimEnd('\')

        if (-not (Test-Path $absFolder -PathType Container)) {
            throw "Carpeta no encontrada: $absFolder"
        }

        $relPath      = Get-RelPath $LOCAL_ROOT $absFolder
        $remoteFolder = "$REMOTE_ROOT/$relPath"

        Write-Host "  Modo    : carpeta" -ForegroundColor White
        Write-Host "  Carpeta : $relPath" -ForegroundColor Gray
        Write-Host ""
        Upload-Dir $absFolder $remoteFolder

    } else {
        # --- Sitio completo ---
        Write-Host "  Modo    : sitio completo" -ForegroundColor White
        Write-Host "  Origen  : $LOCAL_ROOT" -ForegroundColor Gray
        Write-Host ""
        Upload-Dir $LOCAL_ROOT $REMOTE_ROOT
    }

    $ok = $true

} catch {
    Write-Host ""
    Write-Host "  ERROR: $_" -ForegroundColor Red
}

# -- Cerrar sesion -------------------------------------------------------------
Remove-SFTPSession -SessionIndex $sid | Out-Null

Write-Host ""
if ($ok) {
    Write-Host "  OK - Despliegue completado exitosamente." -ForegroundColor Green
} else {
    Write-Host "  FALLO - El despliegue termino con errores." -ForegroundColor Red
}
Write-Host ""
exit $(if ($ok) { 0 } else { 1 })
