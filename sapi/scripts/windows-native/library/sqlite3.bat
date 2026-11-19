@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\sqlite3"

if exist "%__PROJECT__%\build\sqlite3\.completed" goto :skip

:: https://sqlite.org/2026/sqlite-autoconf-3540100.tar.gz

set "VCPKG_ROOT=%__PROJECT__%\var\native-build\vcpkg"
set PATH=%VCPKG_ROOT%;%PATH%

vcpkg install sqlite3:x64-windows-static


xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\sqlite3_x64-windows-static\include" "%__PROJECT__%\build\sqlite3\include" /E /I /H /Y
xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\sqlite3_x64-windows-static\lib" "%__PROJECT__%\build\sqlite3\lib" /E /I /H /Y

copy /Y "%__PROJECT__%\build\sqlite3\lib\sqlite3.lib" "%__PROJECT__%\build\sqlite3\lib\libsqlite3_a.lib"
copy /Y "%__PROJECT__%\build\sqlite3\lib\sqlite3.lib" "%__PROJECT__%\build\sqlite3\lib\libsqlite3.lib"

type nul > "%__PROJECT__%\build\sqlite3\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "sqlite3 build completed ! "

endlocal
