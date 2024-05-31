@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%\php-src\

set "INCLUDE=%INCLUDE%;%__PROJECT__%\openssl\include\;%__PROJECT__%\zlib\include"
set "LIB=%LIB%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib"
set "LIBPATH=%LIBPATH%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib\"

set CL=/MP
rem set RTLIBCFG=static
rem nmake   mode=static debug=false

set x_makefile=%__PROJECT__%\php-src\Makefile


echo release-php^: $(DEPS_CLI) $(CLI_GLOBAL_OBJS) $(BUILD_DIR)^\$(PHPLIB) $(BUILD_DIR)^\php.exe.res $(BUILD_DIR)^\php.exe.manifest  >> %x_makefile%
echo 	^@echo DEPS_CLI: $(DEPS_CLI)  >> %x_makefile%
echo 	^@echo ==================  >> %x_makefile%
echo 	^@echo CLI_GLOBAL_OBJ: $(CLI_GLOBAL_OBJS) >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo BUILD_DIR\PHPLIB: $(BUILD_DIR)\$(PHPLIB) >> %x_makefile%
echo 	^@echo ==================  >> %x_makefile%
echo 	^@echo CLI_GLOBAL_OBJS_RESP: $(CLI_GLOBAL_OBJS_RESP)  >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo LIBS_CLI: $(LIBS_CLI) >> %x_makefile%
echo 	^@echo ==================  >> %x_makefile%
echo 	^@echo LDFLAGS: $(LDFLAGS) >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo LDFLAGS_CLI: $(LDFLAGS_CLI)  >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo _VC_MANIFEST_EMBED_EXE: $(_VC_MANIFEST_EMBED_EXE)  >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo PHPDEF: $(PHPDEF) >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo PHPDLL_RES: $(PHPDLL_RES) >> %x_makefile%
echo 	^@echo ==================  >> %x_makefile%
echo 	^@echo ASM_OBJS: $(ASM_OBJS) >> %x_makefile%
echo 	^@echo ================== >> %x_makefile%
echo 	^@echo MCFILE: $(MCFILE) >> %x_makefile%
echo 	^@echo ==================   >> %x_makefile%
echo 	^@"$(LINK)" ^/nologo  $(CLI_GLOBAL_OBJS_RESP) $(BUILD_DIR)^\$(PHPLIB) $(LIBS_CLI) $(BUILD_DIR)^\php.exe.res /out:$(BUILD_DIR)^\php.exe $(LDFLAGS) $(LDFLAGS_CLI)    >> %x_makefile%
echo 	-@$(_VC_MANIFEST_EMBED_EXE)   >> %x_makefile%
echo 	^@echo SAPI sapi\cli build complete  >> %x_makefile%



nmake release-php

rem nmake install

cd %__PROJECT__%
endlocal

