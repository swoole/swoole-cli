@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%

VisualStudioSetup.exe ^
--locale en-US ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--add Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
--add Microsoft.VisualStudio.Component.Windows10SDK.20348	^
--add Microsoft.VisualStudio.Component.Windows10SDK ^
--passive  --force --norestart
