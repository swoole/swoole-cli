
$__DIR__ = $PSScriptRoot

$__DIR__ = Split-Path -Parent $MyInvocation.MyCommand.Definition
$__PROJECT__ = ( Convert-Path "$__DIR__\..\..\..\..\")

Write-Host  $__DIR__
Write-Host  $__PROJECT__
Write-Host (Get-Location).Path


Invoke-Expression "cmd /c $__PROJECT__\var\windows-build-deps\php-sdk-binary-tools\phpsdk-vs17-x64.bat"
Invoke-Expression "cmd /c $__PROJECT__\sapi\quickstart\windows\native-build\native-build-php-config.bat"


cd $__PROJECT__
