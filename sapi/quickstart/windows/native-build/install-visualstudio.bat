@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%

vc_redist.x64.exe /install /passive /norestart
vc_redist.x86.exe /install /passive /norestart


VisualStudioSetup.exe ^
--locale en-US ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.Modules.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.CMake.Project ^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Component.Windows10SDK.20348	^
--add Microsoft.VisualStudio.Component.Windows10SDK ^
--add Microsoft.Component.VC.Runtime.UCRTSDK	^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--passive  --force --norestart



rem --add Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
rem --add Microsoft.VisualStudio.Workload.NativeDesktop ^
rem --add Microsoft.VisualStudio.Component.VC.Redist.14.Latest	^
rem --add Microsoft.VisualStudio.Component.VC.CLI.Support ^
rem --add Microsoft.VisualStudio.Component.VC.Redist.MSM	 ^