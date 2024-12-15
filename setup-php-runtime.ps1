Set-PSDebug -Strict

$__PROJECT__ = (Get-Location).Path

cmd /c dir

cmd /c $__PROJECT__\sapi\quickstart\windows\native-build\windows-init.bat





exit


$env:PATH = "$env:PATH;%ProgramFiles%\7-Zip"

# 执行需要使用临时 PATH 的命令
# 例如
# cmd /c echo %PATH%

# 在会话结束时移除临时添加的路径
$env:PATH = $env:PATH.Replace(";%ProgramFiles%\7-Zip", "")


$TMP_DOWNLOAD_RUNTIME_DIR = "$__PROJECT__\var\runtime\"
New-Item -ItemType Directory -Path $TMP_DOWNLOAD_RUNTIME_DIR -Force | Out-Null

# Set-Location -Path  "$__PROJECT__\var\runtime\"



$APP_RUNTIME_DOWNLOAD_URL = "https://windows.php.net/downloads/releases/php-8.4.1-nts-Win32-vs17-x64.zip"
$COMPOSER_DOWNLOAD_URL = "https://getcomposer.org/download/latest-stable/composer.phar"
$CACERT_DOWNLOAD_URL = "https://curl.se/ca/cacert.pem"


$env:http_proxy = "http://127.0.0.1:8016"
$env:https_proxy = "http://127.0.0.1:8016"

Write-Host "HTTP Proxy: $env:http_proxy"
Write-Host "HTTPS Proxy: $env:https_proxy"


# $OutputEncoding = [Console]::OutputEncoding = [System.Text.Encoding]::UTF8


$client = new-object System.Net.WebClient
$client.DownloadFile("https://www.7-zip.org/a/7z2409-x64.exe", "7z2409-x64.exe")



$newPath = "C:\Program Files\7-Zip"
# 检查PATH中是否已存在该路径
if (-not ($env:PATH.Contains($newPath)))
{
    # $env:PATH = $env:PATH + ";" + $newPath
    # Write-Host "--------------------"
}
$originalPath = $env:PATH
$newPath = "C:\Program Files\7-Zip"
$env:PATH = "$newPath;$env:PATH"
Write-Output $env:PATH
# $env:PATH = $originalPath


cd $TMP_DOWNLOAD_RUNTIME_DIR

# (Test-Path -Path .\php-8.4.1-nts-Win32-vs17-x64) -and (Remove-Item -Path .\php-8.4.1-nts-Win32-vs17-x64 -Recurse)
# (Test-Path -Path .\php-8.4.1-nts-Win32-vs17-x64.zip) -and ( 7z x o php-8.4.1-nts-Win32-vs17-x64 .\php-8.4.1-nts-Win32-vs17-x64.zip)

cd $__PROJECT__



cd $TMP_DOWNLOAD_RUNTIME_DIR
Invoke-WebRequest -Uri $APP_RUNTIME_DOWNLOAD_URL  -OutFile .\php-8.4.1-nts-Win32-vs17-x64.zip
Invoke-WebRequest -Uri $COMPOSER_DOWNLOAD_URL  -OutFile .\composer.phar
Invoke-WebRequest -Uri $CACERT_DOWNLOAD_URL  -OutFile .\cacert.pem

cd "$__PROJECT__"

set-PSDebug -Off
