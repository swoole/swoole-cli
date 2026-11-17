@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
cd %__PROJECT__%

dumpbin /symbols .\var\native-build\php-src\x64\Release_TS\ext\swoole\ext-src\php_swoole.obj | findstr swoole_module_entry

dumpbin /DEPENDENTS ".\var\native-build\php-src\x64\Release_TS\php.exe"

.\var\native-build\php-src\x64\Release_TS\php.exe -m

:: .\var\native-build\php-src\x64\Release_TS\php.exe -a

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(extension_loaded('intl'));"
.\var\native-build\php-src\x64\Release_TS\php.exe -r "echo \Collator::create('zh_CN')->compare('a','b');"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(MessageFormatter::formatMessage('en', '{0,number}', [1234.5]));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "echo (new NumberFormatter('en_US', NumberFormatter::DECIMAL))->format(1234567.89);"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "echo (new IntlDateFormatter('zh_CN', IntlDateFormatter::FULL, IntlDateFormatter::FULL))->format(new DateTime('2026-11-11 12:00:00'));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "echo MessageFormatter::formatMessage('en', '{0,number,currency}', [1234.5]);"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "echo transliterator_transliterate('Any-Latin', '中文');"

.\var\native-build\php-src\x64\Release_TS\php.exe --ri swoole

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(class_exists('Swoole\Coroutine'));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "Swoole\Coroutine\run(function() { $cid = Swoole\Coroutine::getCid(); var_dump($cid); });"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(method_exists('Swoole\Coroutine', 'gethostbyname'));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(function_exists('Swoole\Coroutine\gethostbyname'));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "Swoole\Coroutine\run(function() { var_dump(Swoole\Coroutine::gethostbyname('www.baidu.com')); });"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "var_dump(gmp_add('12345678901234567890', '98765432109876543210'));"

.\var\native-build\php-src\x64\Release_TS\php.exe --ri gd

.\var\native-build\php-src\x64\Release_TS\php.exe -r "$im = imagecreatetruecolor(100, 50); $c = imagecolorallocate($im, 255, 0, 0); imagefilledrectangle($im, 0, 0, 99, 49, $c); imagepng($im, 'test.png'); imagedestroy($im); echo filesize('test.png');"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "$im = imagecreatetruecolor(200, 50); $w = imagecolorallocate($im, 255, 255, 255); imagettftext($im, 12, 0, 10, 30, $w, 'C:\Windows\Fonts\arial.ttf', 'Test'); imagepng($im, 'test_ttf.png'); imagedestroy($im); echo filesize('test_ttf.png');"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "$im = imagecreatefrompng('test.png'); imagejpeg($im, 'test.jpg', 90); imagedestroy($im); echo filesize('test.jpg');"



.\var\native-build\php-src\x64\Release_TS\php.exe -r "$im = imagecreatetruecolor(100, 100); imageavif($im, 'out.avif'); var_dump(file_exists('out.avif'));"

.\var\native-build\php-src\x64\Release_TS\php.exe -r "$im = imagecreatefromavif('out.avif'); var_dump($im !== false);"

cd /d %__PROJECT__%
endlocal

rem for /f "usebackq tokens=* delims=" %i in (`vswhere -products * -latest -prerelease -find **\VC\Auxiliary\Build\vcvarsall.bat`) do call "%i" x64
rem https://github.com/jingjingxyk/swoole-cli/releases/download/swoole-cli-v6.2.0.0/php.exe
rem curl.exe -fSLo php.exe https://github.com/jingjingxyk/swoole-cli/releases/download/swoole-cli-v6.2.0.0/php.exe

rem cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\test.bat
