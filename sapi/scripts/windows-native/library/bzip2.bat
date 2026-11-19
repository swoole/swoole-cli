@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\bzip2
mkdir  %__PROJECT__%\build\bzip2\include
mkdir  %__PROJECT__%\build\bzip2\lib
if exist "%__PROJECT__%\build\bzip2\.completed" goto :skip

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\bzip2-1.0.8.tar.gz

if  exist "%__PROJECT__%\thirdparty\bzip2" rmdir /s /q "%__PROJECT__%\thirdparty\bzip2"
mkdir "%__PROJECT__%\thirdparty\bzip2"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\bzip2"  %__PROJECT__%\pool\lib\bzip2-1.0.8.tar
cd %__PROJECT__%\thirdparty\bzip2\bzip2-1.0.8\

dir

:: set "CFLAGS=/EHsc /MP /MT "
set CL=/MP
set "LDFLAGS=/VERBOSE:LIB	/DEFAULTLIB:libvcruntime.lib"

sed.exe -i".bak" "s/-MD/-MT/" makefile.msc
nmake /E -f makefile.msc

dumpbin /dependents bzip2.exe

copy /Y *.h "%__PROJECT__%\build\bzip2\include\"
copy /Y libbz2.lib "%__PROJECT__%\build\bzip2\lib\libbz2_a.lib"

type nul > "%__PROJECT__%\build\bzip2\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\bzip2\lib\libbz2_a.lib" |  findstr /i "DEFAULTLIB"

:: vcpkg install bzip2:x64-windows-static

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "bzip2 build completed ! "

endlocal
