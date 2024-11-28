

# PowerShell 命令称为 cmdlet(读作 command-let)
# PowerShell ISE主要用于编写和调试PowerShell脚本
# 在 Windows PowerShell 中 curl 命令被映射为 Invoke-WebRequest


Get-Alias -Name curl
Get-Command curl
Get-Command curl.exe

# 包管理器（winget、chocolatey、scoop）
# 安裝 WinGet
# https://github.com/microsoft/winget-cli/releases/
# https://github.com/microsoft/winget-cli/releases/download/v1.10.40-preview/Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle

Add-AppxPackage -Path "C:\path\to\your\Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle"
Add-AppxPackage -RegisterByFamilyName -MainPackage Microsoft.DesktopAppInstaller_8wekyb3d8bbwe

Invoke-WebRequest -Uri "https://aka.ms/MicrosoftWinget" -UseBasicParsing | Invoke-Expression

Invoke-WebRequest -Uri https://github.com/asheroto/winget-install/releases/latest/download/winget-install.ps1  -OutFile .\winget-install.ps1

irm https://github.com/asheroto/winget-install/releases/latest/download/winget-install.ps1 | iex

irm winget.pro | iex

winget install notepad++

winget install --id Git.Git -e --source winget


# Chocolatey是一个开源的包管理器
# https://chocolatey.org/
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# https://community.chocolatey.org/install.ps1

irm https://community.chocolatey.org/install.ps1 | iex



# Scoop是Windows的命令行安装程序。
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Invoke-RestMethod -Uri https://get.scoop.sh | Invoke-Expression

irm https://get.scoop.sh | iex

# choco install <package-name>
scoop install aria2



# Windows Terminal
# https://github.com/microsoft/terminal

# Add-AppxPackage Microsoft.WindowsTerminal_<versionNumber>.msixbundle
# https://github.com/microsoft/terminal/releases/download/v1.21.3231.0/Microsoft.WindowsTerminal_1.21.3231.0_8wekyb3d8bbwe.msixbundle
Add-AppxPackage Microsoft.WindowsTerminal_1.21.3231.0_8wekyb3d8bbwe.msixbundle

winget install --id Microsoft.WindowsTerminal -e

choco install microsoft-windows-terminal

scoop install windows-terminal



# curl windows
# https://curl.se/windows/

Invoke-WebRequest -Uri https://github.com/git-for-windows/git/releases/download/v2.47.1.windows.1/Git-2.47.1-64-bit.exe -OutFile .\Git-2.47.1-64-bit.exe

start /wait .\Git-2.47.1-64-bit.exe /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="C:\Program Files\Git"


# vcpkg
# https://learn.microsoft.com/zh-cn/vcpkg/get_started/overview
# https://vcpkg.io/en/packages

git clone https://github.com/microsoft/vcpkg.git



# nmake /f Makefile.vc mode=dll VC=17 MACHINE=x86 DEBUG=yes




