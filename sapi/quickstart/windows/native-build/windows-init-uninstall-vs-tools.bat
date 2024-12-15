@echo off

setlocal


echo %~dp0


cd /d %~dp0
cd /d ..\..\..\..\


set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-build-deps\


cd /d %__PROJECT__%\var\windows-build-deps\
dir

.\VisualStudioSetup.exe ^
uninstall ^
--path install="D:\vs" --path cache="D:\vs-cached" ^
--passive  --force --norestart

endlocal
