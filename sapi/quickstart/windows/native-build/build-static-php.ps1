$__DIR__ = $PSScriptRoot
Write-Host (Get-Location).Path


$__DIR__ = Split-Path -Parent $MyInvocation.MyCommand.Definition

Write-Host $__DIR__
# call %__PROJECT__%\var\windows-build-deps\php-sdk-binary-tools\phpsdk-vs17-x64.bat

# Invoke-Expression "cmd /c $__PROJECT__\native-build-php-config.bat"
# Invoke-Expression "cmd /c $__PROJECT__\native-build-php-config.bat"


