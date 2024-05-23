@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%

VisualStudioSetup.exe export 	--passive  --force --norestart


VisualStudioSetup.exe uninstall	--passive  --force --norestart ^
--add Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
--add Microsoft.VisualStudio.Workload.NativeDesktop ^
--add Microsoft.VisualStudio.Component.VC.CLI.Support ^
--add Microsoft.VisualStudio.Component.VC.Redist.MSM

