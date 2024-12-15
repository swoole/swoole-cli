Set-PSDebug -Strict
$__PROJECT__ = (Get-Location).Path
$__PROJECT__ = $PSScriptRoot

# 获得当前脚本所在目录
$__PROJECT__ = Split-Path -Parent $MyInvocation.MyCommand.Definition

cmd /c $__PROJECT__\sapi\quickstart\windows\native-build\windows-init.bat

cd $__PROJECT__



exit

.\bin\runtime\php\php.exe -c .\bin\runtime\php.ini .\bin\runtime\composer.phar config -g repos.packagist composer https://mirrors.tencent.com/composer/
.\bin\runtime\php\php.exe -c .\bin\runtime\php.ini .\bin\runtime\composer.phar update
.\bin\runtime\php\php.exe -c .\bin\runtime\php.ini .\bin\runtime\composer.pharr config -g repos.packagist composer https://packagist.org


.\sapi\quickstart\windows\native-build\build-static-php.ps1

exit


Write-Host $HOME


dir $HOME\AppData\Local\Microsoft\WindowsApps

cmd /c dir %USERPROFILE%\AppData\Local\Microsoft\WindowsApps
cmd /c echo %USERPROFILE%
cmd /c echo %HOMEDRIVE%
cmd /c echo %HOMEPATH%
cmd /c echo %ProgramFiles%
cmd /c echo %NUMBER_OF_PROCESSORS%

dir C:\Windows\System32\OpenSSH\
cmd /c echo %SYSTEMROOT%\System32\OpenSSH\
cmd /c dir %SYSTEMROOT%\System32\OpenSSH\

cmd /c where curl.exe

exit
$env:PATH = "$env:PATH;%ProgramFiles%\7-Zip"

# 执行需要使用临时 PATH 的命令
# 例如
# cmd /c echo %PATH%

# 在会话结束时移除临时添加的路径
$env:PATH = $env:PATH.Replace(";%ProgramFiles%\7-Zip", "")


New-Item -ItemType Directory -Path "$__PROJECT__\var\runtime\" -Force | Out-Null

# Set-Location -Path  "$__PROJECT__\var\runtime\"




# $env:http_proxy = "http://127.0.0.1:8016"
# $env:https_proxy = "http://127.0.0.1:8016"

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


cd $__PROJECT__\var\runtime\

# (Test-Path -Path .\php-8.4.1-nts-Win32-vs17-x64) -and (Remove-Item -Path .\php-8.4.1-nts-Win32-vs17-x64 -Recurse)
# (Test-Path -Path .\php-8.4.1-nts-Win32-vs17-x64.zip) -and ( 7z x o php-8.4.1-nts-Win32-vs17-x64 .\php-8.4.1-nts-Win32-vs17-x64.zip)

cd $__PROJECT__


set-PSDebug -Off
