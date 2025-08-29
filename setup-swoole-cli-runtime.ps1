param(
    [string]
    $mirror = '',
    [string]
    $proxy = ''
)
# with utf8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8
# Set-PSDebug -Trace 1

# Set-StrictMode -Version Latest
# Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Set-ExecutionPolicy Bypass -Scope Process -Force

$PROJECT_DIR = Get-Location
$CPU_COUNT = $env:NUMBER_OF_PROCESSORS
$OS_ARCH = $env:PROCESSOR_ARCHITECTURE

$APP_VERSION = "v6.0.1"
$APP_NAME = 'swoole-cli'
$VERSION = "v6.0.1.0"

try
{
    if ($OS_ARCH -eq "ARM64")
    {
        $OS_ARCH = "arm64"
    }

    # Download and install
    $APP_DOWNLOAD_URL = "https://github.com/swoole/swoole-cli/releases/download/$VERSION/$APP_NAME-$APP_VERSION-cygwin-x64.zip"
    $APP_RUNTIME = "runtime\swoole-cli\"
    $TMP_APP_RUNTIME = "$env:TEMP"
    $FILE = "$APP_NAME-$APP_VERSION-cygwin-x64.zip"

    if ($mirror -eq 'china')
    {
        $APP_DOWNLOAD_URL = "https://storage.swoole.com/dist/$APP_NAME-$APP_VERSION-cygwin-x64.zip"
    }
    if ($proxy -ne '')
    {
        $env:HTTP_PROXY = $proxy
        $env:HTTPS_PROXY = $proxy
    }
    if (-not (Test-Path "$TMP_APP_RUNTIME\$FILE" -PathType Leaf))
    {
        if (Get-Command "curl.exe" -ErrorAction SilentlyContinue)
        {
            curl.exe -H 'Referer: https://www.swoole.com/download' -H 'User-Agent: download swoole-cli runtime with setup-swoole-cli-runtime.ps1' -fSLo "$TMP_APP_RUNTIME\$FILE" $APP_DOWNLOAD_URL
        }
        else
        {
            $headers = @{
                'User-Agent' = 'download swoole-cli runtime with setup-swoole-cli-runtime.ps1'
                'Referer' = 'https://www.swoole.com/download'
            }
            # Invoke-WebRequest $APP_DOWNLOAD_URL -UseBasicParsing -OutFile $FILE
            # Invoke-WebRequest -Uri $APP_DOWNLOAD_URL -OutFile  $FILE
            irm $APP_DOWNLOAD_URL -Headers $headers -outfile "$TMP_APP_RUNTIME\$FILE"
        }
    }

    if (Test-Path "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\" -PathType Container)
    {
        Remove-Item "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\" -Recurse -Force
    }
    Expand-Archive -Path "$TMP_APP_RUNTIME\$FILE" -DestinationPath $TMP_APP_RUNTIME
    #  Microsoft.PowerShell.Archive\Expand-Archive -Path $file -DestinationPath $tempDir -Force
    dir $TMP_APP_RUNTIME
    dir "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\"

    if (Test-Path "$PROJECT_DIR\$APP_RUNTIME\bin\swoole-cli.exe" -PathType Leaf)
    {
        Remove-Item "$PROJECT_DIR\$APP_RUNTIME\" -Recurse -Force
    }
    New-Item -ItemType Directory -Path "$PROJECT_DIR\$APP_RUNTIME" -Force

    Move-Item -Path "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\*" -Destination "$PROJECT_DIR\$APP_RUNTIME" -Force

    dir "$PROJECT_DIR\$APP_RUNTIME\"
    dir "$PROJECT_DIR\$APP_RUNTIME\bin\"
    dir "$PROJECT_DIR\$APP_RUNTIME\etc\"

    if (-NOT (Test-Path "$PROJECT_DIR\$APP_RUNTIME\etc\cacert.pem" -PathType Leaf))
    {
        irm "https://curl.se/ca/cacert.pem" -outfile "$PROJECT_DIR\$APP_RUNTIME\etc\cacert.pem"
    }
    dir "$PROJECT_DIR\$APP_RUNTIME\etc\"

    # Start-Process -FilePath $Installer -Args "/passive /norestart" -Wait
    # Start-Sleep 3
    # Remove-Item $Installer -ErrorAction Ignore


    $env:PATH += ";$PROJECT_DIR\$APP_RUNTIME\bin\"

    $scriptPath = $MyInvocation.MyCommand.Definition
    $drive = [System.IO.Path]::GetPathRoot($scriptPath).TrimEnd('\')
    Write-Output $drive
    $drive = (Split-Path -Path $PSScriptRoot -Qualifier).TrimEnd(':') + ":"
    Write-Output $drive
    $cygwin_drive = $drive.TrimEnd(":").ToLower()
    write-host $cygwin_drive

    $PHP_INI_DIR = "$PROJECT_DIR\$APP_RUNTIME\etc\"
    $PHP_INI_DIR = $PHP_INI_DIR.Replace('\', '/')
    $PHP_INI_DIR = $PHP_INI_DIR.Replace($drive, $cygwin_drive)
    $CYGWIN_PHP_INI_DIR = "/cygdrive/" + $PHP_INI_DIR
    $CYGWIN_PHP_INI = "/cygdrive/" + $PHP_INI_DIR + "/php.ini"

    write-host $CYGWIN_PHP_INI


    $template = @"
curl.cainfo="{0}/cacert.pem"
openssl.cafile="{0}/cacert.pem"
swoole.use_shortname=off
display_errors = On
error_reporting = E_ALL

upload_max_filesize="128M"
post_max_size="128M"
memory_limit="1G"
date.timezone="UTC"

opcache.enable=On
opcache.enable_cli=On
opcache.jit=1225
opcache.jit_buffer_size=128M

; jit more reference info https://mp.weixin.qq.com/s/Tm-6XVGQSlz0vDENLB3ylA

expose_php=Off
apc.enable_cli=1

"@ -f "$CYGWIN_PHP_INI_DIR"

    $template | Set-Content -Path "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini"
    Get-Content -Path "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini"

    write-host "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini"
    write-host $CYGWIN_PHP_INI

    function x-swoole-cli
    {
        $command = "$PROJECT_DIR\$APP_RUNTIME\bin\swoole-cli.exe -c $PROJECT_DIR\$APP_RUNTIME\etc\php.ini @args"
        Write-Host Invoke-Expression $command
    }
    # Set-Alias php "$PROJECT_DIR\$APP_RUNTIME\bin\swoole-cli.exe -c $CYGWIN_PHP_INI"
    # php -v

    swoole-cli -v
    swoole-cli --ri swoole
    swoole-cli -c "$CYGWIN_PHP_INI" --ri curl
    swoole-cli -c "$CYGWIN_PHP_INI" --ri openssl

}
catch
{
    Write-Host "[error] info: $_"
    Write-Host "except type: $( $_.Exception.GetType().FullName )"
    exit 1
}

