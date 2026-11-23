@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
cd %__PROJECT__%

cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\openssl.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\zlib.bat

cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\liblzma.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\liblz4.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\brotli.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libzstd.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\bzip2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libzip.bat

cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libiconv.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libxml2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libpsl.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libssh2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libnghttp2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\cares.bat
:: cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libidn2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\ngtcp2.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libnghttp3.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\curl.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\oniguruma.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\icu.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\gettext.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libsodium.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\wineditline.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\sqlite3.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\pgsql.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\mpir.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libxslt.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libpng.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\freetype.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libjpeg.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libgif.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libwebp.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\dav1d.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\svt_av1.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libyuv.bat
cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\libavif.bat

cd /d %__PROJECT__%
endlocal
