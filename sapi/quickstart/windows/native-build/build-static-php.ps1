
$__DIR__ = $PSScriptRoot

$__DIR__ = Split-Path -Parent $MyInvocation.MyCommand.Definition
$__PROJECT__ = ( Convert-Path "$__DIR__\..\..\..\..\")

Write-Host  $__DIR__
Write-Host  $__PROJECT__
Write-Host (Get-Location).Path

<#

var\windows-build-deps\php-sdk-binary-tools\phpsdk-vs17-x64.bat
cd var\windows-build-deps\php-src\

.\buildconf.bat -f
.\configure.bat --help

configure.bat ^
--disable-all         --disable-cgi      --enable-cli   ^
--enable-sockets      --enable-ctype     --enable-pdo    --enable-phar  ^
--enable-filter ^
--enable-xmlreader   --enable-xmlwriter ^
--enable-tokenizer



nmake /E php.exe

#>

exit
Invoke-Expression "cmd /c $__PROJECT__\var\windows-build-deps\php-sdk-binary-tools\phpsdk-vs17-x64.bat"
Invoke-Expression "cmd /c $__PROJECT__\sapi\quickstart\windows\native-build\native-build-php-config.bat"


cd $__PROJECT__
