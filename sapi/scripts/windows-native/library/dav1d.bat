@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\dav1d


if exist "%__PROJECT__%\build\dav1d\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\dav1d-1.5.0.tar.gz

if  exist "%__PROJECT__%\thirdparty\dav1d" rmdir /s /q "%__PROJECT__%\thirdparty\dav1d"
mkdir "%__PROJECT__%\thirdparty\dav1d"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\dav1d"  %__PROJECT__%\pool\lib\dav1d-1.5.0.tar
cd %__PROJECT__%\thirdparty\dav1d\dav1d-1.5.0

dir

set "PATH=%__PROJECT__%\runtime\nasm;C:\Program Files\Meson;%PATH%;"
meson --version

if exist "build_static" (
    rmdir /S /Q build_static
)

:: https://github.com/videolan/dav1d.git

mkdir build
meson setup  build  ^
	-Dprefix=%__PROJECT__%\build\dav1d  ^
	-Dlibdir=%__PROJECT__%\build\dav1d\lib  ^
	-Dincludedir=%__PROJECT__%\build\dav1d\include  ^
	-Dbackend=ninja  ^
	-Dbuildtype=release  ^
	-Ddefault_library=static  ^
	-Db_staticpic=true  ^
	-Db_pie=true  ^
	-Dprefer_static=true  ^
	-Denable_asm=true  ^
	-Denable_tools=true  ^
	-Denable_examples=false  ^
	-Denable_tests=false  ^
	-Denable_docs=false  ^
	-Dlogging=false  ^
	-Dfuzzing_engine=none

	ninja -C build
	ninja -C build install

copy /Y "%__PROJECT__%\build\dav1d\lib\libdav1d.a" "%__PROJECT__%\build\dav1d\lib\avif_a.lib"
copy /Y "%__PROJECT__%\build\dav1d\lib\libdav1d.a" "%__PROJECT__%\build\dav1d\lib\aviflib"


if exist "%__PROJECT__%\build\dav1d\include\dav1d\dav1d.h" (
    type nul > "%__PROJECT__%\build\dav1d\.completed"
)


cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "dav1d build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal
