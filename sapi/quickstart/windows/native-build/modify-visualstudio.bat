@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%


VisualStudioSetup.exe modify	--passive  --force  ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.Modules.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.CMake.Project ^
--add Microsoft.VisualStudio.Component.VC.Redist.14.Latest	^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Component.Windows10SDK.20348	^
--add Microsoft.VisualStudio.Component.Windows10SDK ^
--add Microsoft.Component.VC.Runtime.UCRTSDK	^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--remove Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
--remove Microsoft.VisualStudio.Workload.NativeDesktop ^
--remove Microsoft.VisualStudio.Component.VC.CLI.Support ^
--remove Microsoft.VisualStudio.Component.VC.Redist.MSM

