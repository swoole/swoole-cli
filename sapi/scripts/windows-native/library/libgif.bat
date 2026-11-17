@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libgif"

if exist "%__PROJECT__%\build\libgif\.completed" goto :skip


set "VCPKG_ROOT=%__PROJECT__%\var\native-build\vcpkg"
set PATH=%VCPKG_ROOT%;%PATH%
vcpkg install giflib:x64-windows-static

xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\giflib_x64-windows-static\include" "%__PROJECT__%\build\libgif\include" /E /I /H /Y
xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\giflib_x64-windows-static\lib" "%__PROJECT__%\build\libgif\lib" /E /I /H /Y

type nul > "%__PROJECT__%\build\libgif\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libgif build completed ! "

endlocal

