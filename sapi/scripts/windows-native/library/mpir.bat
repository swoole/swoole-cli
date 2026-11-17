@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\


set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\mpir

if exist "%__PROJECT__%\build\mpir\.completed" goto :skip


set "VCPKG_ROOT=%__PROJECT__%\var\native-build\vcpkg"
set PATH=%VCPKG_ROOT%;%PATH%
rem mpir and gmp conflict
:: vcpkg remove gmp:x64-windows-static
vcpkg install mpir:x64-windows-static


xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\mpir_x64-windows-static\include" "%__PROJECT__%\build\mpir\include" /E /I /H /Y
xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\mpir_x64-windows-static\lib" "%__PROJECT__%\build\mpir\lib" /E /I /H /Y



copy /Y "%__PROJECT__%\build\mpir\lib\mpir.lib" "%__PROJECT__%\build\mpir\lib\mpir_a.lib"
copy /Y "%__PROJECT__%\build\mpir\lib\mpirxx.lib" "%__PROJECT__%\build\mpir\lib\mpirxx_a.lib"

type nul > "%__PROJECT__%\build\mpir\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "mpir build completed ! "


cd /d %__PROJECT__%
exit /b 0
endlocal

