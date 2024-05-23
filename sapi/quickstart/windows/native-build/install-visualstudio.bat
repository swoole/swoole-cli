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
--add Microsoft.VisualStudio.Component.VC.CMake.Project ^
--add Microsoft.VisualStudio.Component.VC.Redist.14.Latest	^
--add Microsoft.VisualStudio.Component.VC.Redist.MSM	 ^
--add Microsoft.Component.VC.Runtime.UCRTSDK	^
--add Microsoft.VisualStudio.Component.VC.CLI.Support ^
--add Microsoft.VisualStudio.Workload.NativeDesktop ^
--add Microsoft.VisualStudio.Component.VC.Modules.x86.x64 ^
--passive  --force --norestart

