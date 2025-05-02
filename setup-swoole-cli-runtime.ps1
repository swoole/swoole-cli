param(
    [string]
    $mirror = ''
)
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
        $APP_DOWNLOAD_URL = "https://wenda-1252906962.file.myqcloud.com/dist/$APP_NAME-$APP_VERSION-cygwin-x64.zip"
    }

    if (Get-Command "curl.exe" -ErrorAction SilentlyContinue)
    {
        curl.exe -fSLo "$TMP_APP_RUNTIME\$FILE" $APP_DOWNLOAD_URL
    }
    else
    {
        # Invoke-WebRequest $APP_DOWNLOAD_URL -UseBasicParsing -OutFile $FILE
        # Invoke-WebRequest -Uri $APP_DOWNLOAD_URL -OutFile  $FILE
        irm $APP_DOWNLOAD_URL -outfile "$TMP_APP_RUNTIME\$FILE"
    }

    Expand-Archive -Path "$TMP_APP_RUNTIME\$FILE" -DestinationPath $TMP_APP_RUNTIME
    #  Microsoft.PowerShell.Archive\Expand-Archive -Path $file -DestinationPath $tempDir -Force
    dir $TMP_APP_RUNTIME
    dir "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\"

    New-Item -ItemType Directory -Path "$PROJECT_DIR\$APP_RUNTIME" -Force
    Move-Item -Path "$TMP_APP_RUNTIME\$APP_NAME-$APP_VERSION-cygwin-x64\*" -Destination "$PROJECT_DIR\$APP_RUNTIME"

    dir "$PROJECT_DIR\$APP_RUNTIME\"
    dir "$PROJECT_DIR\$APP_RUNTIME\bin\"
    dir "$PROJECT_DIR\$APP_RUNTIME\etc\"

    irm "https://curl.se/ca/cacert.pem" -outfile "$PROJECT_DIR\$APP_RUNTIME\etc\cacert.pem"
    dir "$PROJECT_DIR\$APP_RUNTIME\etc\"

    # Start-Process -FilePath $Installer -Args "/passive /norestart" -Wait
    # Start-Sleep 3
    # Remove-Item $Installer -ErrorAction Ignore

    $template = @"
curl.cainfo="{0}\cacert.pem"
openssl.cafile="{0}\cacert.pem"
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

; jit 更多配置参考 https://mp.weixin.qq.com/s/Tm-6XVGQSlz0vDENLB3ylA

expose_php=Off
apc.enable_cli=1

"@ -f "$PROJECT_DIR\$APP_RUNTIME\etc\"

    $template | Set-Content -Path "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini"
    Get-Content -Path "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini"

    $env:PATH += ";$PROJECT_DIR\$APP_RUNTIME\bin\"

    function x-swoole-cli
    {
        $command = "$PROJECT_DIR\$APP_RUNTIME\bin\swoole-cli.exe -c $PROJECT_DIR\$APP_RUNTIME\etc\php.ini @args"
        Write-Host Invoke-Expression $command
    }

    swoole-cli -v
    swoole-cli --ri swoole
    swoole-cli -c "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini" --ri curl
    swoole-cli -c "$PROJECT_DIR\$APP_RUNTIME\etc\php.ini" --ri openssl
}
catch
{
    Write-Host "[error] info: $_"
    Write-Host "except type: $( $_.Exception.GetType().FullName )"
    exit 1
}

