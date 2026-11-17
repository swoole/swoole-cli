@echo off

setlocal


echo %~dp0


cd /d %~dp0
cd /d ..\..\..\..\


set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\native-build\
md %__PROJECT__%\runtime\

cd /d %__PROJECT__%\var\native-build\
dir

:: Add-AppxPackage -Path ".\Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle"
:: install winget with script
:: Install-Script winget-install -Force
:: winget-install -Force

powershell -NoProfile -ExecutionPolicy Bypass -Command "Set-PSRepository -Name PSGallery -InstallationPolicy Trusted"
powershell -NoProfile -ExecutionPolicy Bypass -Command "Install-PackageProvider -Name NuGet -Force"
powershell -NoProfile -ExecutionPolicy Bypass -Command "Install-Script winget-install -Force"
set "PATH=%PATH%;C:\Program Files\WindowsPowerShell\Scripts"
powershell -NoProfile -ExecutionPolicy Bypass -Command "winget-install -Force -Wait"

set "WINGET_EXE="
for /f "delims=" %%i in ('powershell -NoProfile -Command "(Get-AppxPackage Microsoft.DesktopAppInstaller).InstallLocation + '\winget.exe'"') do set "WINGET_EXE=%%i"
echo %WINGET_EXE%

dir "%WINGET_EXE%"
"%WINGET_EXE%" --version
"%WINGET_EXE%" source reset --force
"%WINGET_EXE%" source update

"%WINGET_EXE%" install vim.vim  --silent --accept-package-agreements --accept-source-agreements
:: winget install --id mesonbuild.meson --exact --silent --accept-package-agreements --accept-source-agreements

:: "%WINGET_EXE%" search Python
:: python -m pip install --upgrade pip
:: python -m ensurepip --upgrade
"%WINGET_EXE%" install -e --id Python.Python.3.12 --exact --silent --accept-package-agreements --accept-source-agreements
python -m pip --version
python -m pip  install meson ninja
:: pip3 install uv
:: uv pip install meson ninja
:: uv venv --system
meson --version
ninja --version



:: winget search Microsoft.Edit
:: winget install Microsoft.Edit

:: winget source reset --force
:: winget source update
where.exe winget
"%WINGET_EXE%" list
echo "======================================================"
msiexec /i strawberry-perl-5.38.2.2-64bit.msi  /passive

:: .\vc_redist.x64.exe /install /passive /norestart

.\7z2409-x64.exe /S

:: silent install
.\gvim_9.2.1316_x64_signed.exe /S

:: msiexec.exe /i meson-1.12.1-64.msi /quiet /norestart
:: msiexec.exe /i meson-1.12.1-64.msi /quiet /norestart ALLUSERS=1
:: meson --version
:: ninja --version

:: dotnet tool install --global Nefarius.Tools.WDKWhere
:: wdkwhere


set "PATH=%ProgramFiles%\7-Zip;%PATH%;"
set "PATH=%__PROJECT__%\var\native-build\php-sdk-binary-tools\bin\;%__PROJECT__%\var\native-build\php-sdk-binary-tools\msys2\bin;%PATH%;"
echo "%PATH%"
echo %ProgramFiles%\7-Zip

cd /d %__PROJECT__%\runtime\

if  exist ".\nasm\" (
   rd /s /q ".\nasm\"
)

if  exist ".\libarchive\" (
   rd /s /q ".\libarchive\"
)

if  exist ".\php\" (
   rd /s /q ".\php\"
)

cd /d %__PROJECT__%\var\native-build\


if  exist ".\nasm\" (
   rmdir /s /q ".\nasm\"
)
if  exist ".\libarchive\" (
   rmdir /s /q ".\libarchive\"
)

if  exist ".\php-nts-Win32-x64" (
   rmdir /s /q ".\php-nts-Win32-x64"
)

cd /d %__PROJECT__%\var\native-build\vcpkg
:: 运行引导脚本，生成 vcpkg.exe
call bootstrap-vcpkg.bat

cd /d %__PROJECT__%\var\native-build\
7z.exe x -onasm nasm-2.16.03-win64.zip
7z.exe x -oDependencies Dependencies_x64_Release.zip
7z.exe x -ophp-nts-Win32-x64 php-nts-Win32-x64.zip

rem 7z.exe x -olibarchive libarchive-3.8.1.tar.gz
rem choco install archive

7z.exe x -aoa -y -oProcdump Procdump.zip

rem Using jom instead of nmake on Windows for multi-core acceleration
7z.exe x -aoa -y -ojom jom_1_1_7.zip

md %__PROJECT__%\pool\
7z.exe x -aoa -y -o"%__PROJECT__%\pool\" all-deps.zip


:: vcpkg install libarchive
:: vcpkg install libarchive:x64-linux-release
:: powershell -command "irm asheroto.com/winget | iex "
:: powershell -command "winget search libarchive --accept-source-agreements"
:: powershell -command "winget install libarchive "


move nasm\nasm-2.16.03 %__PROJECT__%\runtime\nasm
move libarchive\libarchive %__PROJECT__%\runtime\libarchive
xcopy jom\jom.exe %__PROJECT__%\runtime\jom\jom.exe
xcopy php-nts-Win32-x64 %__PROJECT__%\runtime\php /E /I /Y
xcopy cacert.pem %__PROJECT__%\runtime\php\cacert.pem


(
echo extension_dir="%__PROJECT__%\runtime\php\ext\"
echo extension=php_curl.dll
echo extension=php_bz2.dll
echo extension=php_openssl.dll
echo extension=php_fileinfo.dll
echo extension=php_exif.dll
echo extension=php_gd.dll
echo extension=php_gettext.dll
echo extension=php_gmp.dll
echo extension=php_intl.dll
echo extension=php_mbstring.dll
echo extension=php_pdo_mysql.dll
echo extension=php_pdo_pgsql.dll
echo extension=php_sqlite3.dll
echo extension=php_sockets.dll
echo extension=php_sodium.dll
echo extension=php_xsl.dll
echo extension=php_zip.dll

echo curl.cainfo="%__PROJECT__%\runtime\php\cacert.pem"
echo openssl.cafile="%__PROJECT__%\runtime\php\cacert.pem"
echo display_errors = On
echo error_reporting = E_ALL

echo upload_max_filesize="128M"
echo post_max_size="128M"
echo memory_limit="1G"
echo date.timezone="UTC"

echo opcache.enable_cli=1
echo opcache.jit=1254
echo opcache.jit_buffer_size=480M

echo expose_php=Off
echo apc.enable_cli=1

) > %__PROJECT__%\runtime\php\php.ini

call %__PROJECT__%\sapi\quickstart\windows\native-build\windows-init-show-install-result.bat


endlocal
