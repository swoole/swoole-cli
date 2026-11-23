@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\pgsql"

if exist "%__PROJECT__%\build\pgsql\.completed" goto :skip


set "VCPKG_ROOT=%__PROJECT__%\var\native-build\vcpkg"
set PATH=%VCPKG_ROOT%;%PATH%

vcpkg install libpq:x64-windows-static


xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\libpq_x64-windows-static\include" "%__PROJECT__%\build\pgsql\include" /E /I /H /Y
xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\libpq_x64-windows-static\lib" "%__PROJECT__%\build\pgsql\lib" /E /I /H /Y

copy /Y "%__PROJECT__%\build\pgsql\lib\pq.lib" "%__PROJECT__%\build\pgsql\lib\libpq.lib"

type nul > "%__PROJECT__%\build\pgsql\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "pgsql build completed ! "

endlocal
