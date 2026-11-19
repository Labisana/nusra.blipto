$source = "C:\Herd\nusra.blipto"
$staging = "C:\Herd\nusra.blipto\deploy_staging"
$zipFile = "C:\Herd\nusra.blipto\deploy.zip"

Write-Host "Menyiapkan folder staging..."
if (Test-Path $staging) { Remove-Item -Recurse -Force $staging }
if (Test-Path $zipFile) { Remove-Item -Force $zipFile }

New-Item -ItemType Directory -Path $staging | Out-Null

$dirs = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "storage", "vendor")
foreach ($dir in $dirs) {
    Write-Host "Menyalin $dir..."
    Copy-Item -Path "$source\$dir" -Destination "$staging\$dir" -Recurse -Force
}

$files = @("artisan", "composer.json", "composer.lock", "package.json", ".env.example", ".env.production.example")
foreach ($f in $files) {
    if (Test-Path "$source\$f") {
        Write-Host "Menyalin $f..."
        Copy-Item -Path "$source\$f" -Destination "$staging\$f" -Force
    }
}

Write-Host "Membersihkan file-file database, cache, dan log..."
Get-ChildItem -Path "$staging\database" -Filter "*.sqlite*" -Recurse -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue
Get-ChildItem -Path "$staging\storage\logs" -Filter "*.log" -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue
Get-ChildItem -Path "$staging\storage\framework\cache\data" -Recurse -ErrorAction SilentlyContinue | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
Get-ChildItem -Path "$staging\storage\framework\sessions" -Recurse -Exclude ".gitignore" -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue
Get-ChildItem -Path "$staging\storage\framework\views" -Recurse -Exclude ".gitignore" -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue
Get-ChildItem -Path "$staging\bootstrap\cache" -Recurse -Exclude ".gitignore" -ErrorAction SilentlyContinue | Remove-Item -Force -ErrorAction SilentlyContinue

$emptyDirs = @(
    "$staging\storage\app\public",
    "$staging\storage\framework\cache\data",
    "$staging\storage\framework\sessions",
    "$staging\storage\framework\views",
    "$staging\storage\logs",
    "$staging\bootstrap\cache"
)
foreach ($ed in $emptyDirs) {
    if (-not (Test-Path $ed)) {
        New-Item -ItemType Directory -Path $ed -Force | Out-Null
    }
    if (-not (Test-Path "$ed\.gitignore")) {
        Set-Content -Path "$ed\.gitignore" -Value "*`n!.gitignore"
    }
}

Write-Host "Membuat file deploy.zip..."
Set-Location $staging
tar.exe -a -c -f $zipFile *
Set-Location $source

Write-Host "Menghapus folder staging sementara..."
Remove-Item -Recurse -Force $staging

Write-Host "Selesai! Informasi file deploy.zip:"
Get-Item $zipFile | Select-Object Name, @{Name="Size (MB)";Expression={[math]::Round($_.Length / 1MB, 2)}}, LastWriteTime
