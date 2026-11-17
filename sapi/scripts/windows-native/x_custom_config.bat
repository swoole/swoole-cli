@echo off

setlocal enableextensions enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

cd /d %__PROJECT__%\var\native-build\php-src\

set X_MAKEFILE=%__PROJECT__%\var\native-build\php-src\Makefile

:: set "PATH=%ProgramFiles%\7-Zip;%ProgramFiles%\Git\bin;%__PROJECT__%\runtime\;%__PROJECT__%\runtime\nasm\;%__PROJECT__%\runtime\php;%__PROJECT__%\runtime\libarchive\bin;%PATH%"

rem https://learn.microsoft.com/zh-cn/cpp/error-messages/tool-errors/linker-tools-warning-lnk4098?view=msvc-170&redirectedfrom=MSDN
rem https://learn.microsoft.com/zh-cn/cpp/build/reference/md-mt-ld-use-run-time-library?view=msvc-170

rem -lucrt

sed.exe -i "s/DLL_LDFLAGS=\/dll /DLL_LDFLAGS=/" %X_MAKEFILE%
sed.exe -i "s/PHP_LDFLAGS=\$(DLL_LDFLAGS) \/nodefaultlib:libcmt \/def:\$(PHPDEF) /PHP_LDFLAGS=/" %X_MAKEFILE%

sed.exe -i "s/\/LD \/MD/\/MT/" %X_MAKEFILE%
sed.exe -i "s/\/D _USRDLL/ /" %X_MAKEFILE%


findstr /C:"x-show-var: " %X_MAKEFILE%
findstr /C:"x-show-var: " %X_MAKEFILE% > nul

if errorlevel 1 (
	echo "custom x-show-var config !"
	goto x-custom-show-var-start
) else (
	echo "custom x-show-var config file exits !"
	goto x-custom-show-var-end
)

:x-custom-show-var-start
echo x-show-var:                                          >> %X_MAKEFILE%
echo    ^@echo BUILD_DIR: $(BUILD_DIR)                    >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo CFLAGS: $(CFLAGS)                          >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo DEPS_CLI: $(DEPS_CLI)                      >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo CFLAGS_PHP_OBJ: $(CFLAGS_PHP_OBJ)          >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo CFLAGS_BD_ZEND: $(CFLAGS_BD_ZEND)          >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo CLI_GLOBAL_OBJ: $(CLI_GLOBAL_OBJS)         >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo ASM_OBJS: $(ASM_OBJS)                      >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo STATIC_EXT_LIBS: $(STATIC_EXT_LIBS)        >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo STATIC_EXT_LDFLAGS: $(STATIC_EXT_LDFLAGS)  >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo STATIC_EXT_CFLAGS: $(STATIC_EXT_CFLAGS)    >> %X_MAKEFILE%
echo    ^@echo ==================                         >> %X_MAKEFILE%
echo    ^@echo BUILD_DIR\PHPLIB: $(BUILD_DIR)\$(PHPLIB)   >> %X_MAKEFILE%
echo    ^@echo ==================                             >> %X_MAKEFILE%
echo    ^@echo BUILD_DIR\PHPDLL: $(BUILD_DIR)\$(PHPDLL)   >> %X_MAKEFILE%
echo    ^@echo ==================                             >> %X_MAKEFILE%
echo    ^@echo CLI_GLOBAL_OBJS_RESP: $(CLI_GLOBAL_OBJS_RESP)  >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo PHP_LDFLAGS: $(PHP_LDFLAGS)  >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo LIBS: $(LIBS)                >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo LIBS_CLI: $(LIBS_CLI)        >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo LDFLAGS: $(LDFLAGS)          >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo LDFLAGS_CLI: $(LDFLAGS_CLI)  >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo _VC_MANIFEST_EMBED_EXE: $(_VC_MANIFEST_EMBED_EXE) >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo PHPDEF: $(PHPDEF)            >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo PHPDLL_RES: $(PHPDLL_RES)    >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo ASM_OBJS: $(ASM_OBJS)        >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo MCFILE: $(MCFILE)            >> %X_MAKEFILE%
echo    ^@echo ==================           >> %X_MAKEFILE%
echo    ^@echo X_CUSTOM_LIBS: $(X_CUSTOM_LIBS)            >> %X_MAKEFILE%

:x-custom-show-var-end


findstr /C:"x-release-static-php:" %X_MAKEFILE% >nul

if errorlevel 1 (
	echo "custom x-release-static-php config !"
	goto x-release-php-start
) else (
	echo "custom x-release-static-php config file exits !"
	goto x-release-php-end
)

:x-release-php-start
echo. >> %X_MAKEFILE%

echo. >> %X_MAKEFILE%

rem  Makefile
echo x-release-static-php^: generated_files $(DEPS_CLI) $(PHP_GLOBAL_OBJS) $(CLI_GLOBAL_OBJS) $(STATIC_EXT_OBJS)  $(ASM_OBJS)  $(BUILD_DIR)\php.exe.res $(BUILD_DIR)\php.exe.manifest   >> %X_MAKEFILE%
echo 	@"$(LINK)" /nologo  $(STATIC_EXT_OBJS_RESP) $(PHP_GLOBAL_OBJS_RESP) $(CLI_GLOBAL_OBJS_RESP) $(STATIC_EXT_LIBS) $(ASM_OBJS) $(LIBS) $(LIBS_CLI) $(BUILD_DIR)\php.exe.res /out:$(BUILD_DIR)\php.exe $(LDFLAGS) $(LDFLAGS_CLI) $(X_CUSTOM_LIBS) >> %X_MAKEFILE%
rem echo 	-@$(_VC_MANIFEST_EMBED_EXE) >> %X_MAKEFILE%
echo 	@if exist php.exe.manifest $(MT) -nologo -manifest php.exe.manifest -outputresource:php.exe    >> %X_MAKEFILE%


rem /ENTRY:main
rem /ENTRY:main php8ts.lib

rem https://www.cnblogs.com/sherry-best/archive/2013/04/15/3022705.html
rem https://learn.microsoft.com/zh-CN/cpp/c-runtime-library/crt-library-features?view=msvc-170&viewFallbackFrom=vs-2019

rem echo 	^@"$(LINK)" ^/nologo  $(PHP_GLOBAL_OBJS) $(PHP_GLOBAL_OBJS_RESP) $(CLI_GLOBAL_OBJS) $(CLI_GLOBAL_OBJS_RESP)  $(STATIC_EXT_OBJS_RESP)  $(STATIC_EXT_OBJS)  $(ASM_OBJS) $(LIBS) $(LIBS_CLI) $(BUILD_DIR)^\php.exe.res /out:$(BUILD_DIR)^\php.exe $(LDFLAGS) $(LDFLAGS_CLI)    >> %x_makefile%
rem echo 	^@"$(LINK)" ^/nologo $(DEPS_CLI)  $(STATIC_EXT_OBJS_RESP)  $(STATIC_EXT_LIBS)  $(ASM_OBJS) $(LIBS) $(LIBS_CLI)    $(BUILD_DIR)^\php.exe.res  /out:$(BUILD_DIR)^\php.exe $(LDFLAGS) $(LDFLAGS_CLI)  >> %x_makefile%
rem echo 	-@$(_VC_MANIFEST_EMBED_EXE)   >> %x_makefile%
rem echo 	^@echo SAPI sapi\cli build complete  >> %x_makefile%
rem echo 	@if exist php.exe.manifest $(MT) -nologo -manifest php.exe.manifest -outputresource:php.exe    >> %x_makefile%


rem /MANIFEST:php.exe.manifest /MANIFESTUAC:uiAccess /SUBSYSTEM:CONSOLE  /subsystem:windows

:x-release-php-end



cd %__PROJECT__%
endlocal
