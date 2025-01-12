@echo off

:: msys2 site: https://www.msys2.org/
:: start https://mirror.msys2.org/distrib/x86_64/msys2-x86_64-20230526.exe

setlocal enableextensions enabledelayedexpansion

echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
cd /d %__PROJECT__%\
echo %cd%


md %__PROJECT__%\var\msys2-build\

cd /d %__PROJECT__%\var\msys2-build\


set "SITE=https://mirror.msys2.org"

:getopt
if /i "%1" equ "--mirror" (
	if /i "%2" equ "china" (
		set "SITE=https://mirrors.tuna.tsinghua.edu.cn/msys2/"
	)
)
shift

if not (%1)==() goto getopt

:: curl.exe -fSLo msys2-x86_64-20241208.exe https://repo.msys2.org/distrib/x86_64/msys2-x86_64-20241208.exe
:: curl.exe -fSLo msys2-x86_64-20241208.exe https://mirror.msys2.org/distrib/x86_64/msys2-x86_64-20241208.exe
curl.exe -fSLo msys2-x86_64-20241208.exe %SITE%/distrib/x86_64/msys2-x86_64-20241208.exe

copy .\msys2-x86_64-20241208.exe %__PROJECT__%\msys2-x86_64.exe


endlocal
