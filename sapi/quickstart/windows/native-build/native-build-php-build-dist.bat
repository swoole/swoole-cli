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

(
echo 'release-php: $(DEPS_CLI) $(CLI_GLOBAL_OBJS) $(BUILD_DIR)\$(PHPLIB) $(BUILD_DIR)\php.exe.res $(BUILD_DIR)\php.exe.manifest'
echo '	@echo DEPS_CLI: $(DEPS_CLI)'
echo '	@echo =================='
echo '	@echo CLI_GLOBAL_OBJ: $(CLI_GLOBAL_OBJS)'
echo '	@echo =================='
echo '	@echo BUILD_DIR\PHPLIB: $(BUILD_DIR)\$(PHPLIB)'
echo '	@echo =================='
echo '	@echo CLI_GLOBAL_OBJS_RESP: $(CLI_GLOBAL_OBJS_RESP)'
echo '	@echo =================='
echo '	@echo LIBS_CLI: $(LIBS_CLI)'
echo '	@echo =================='
echo '	@echo LDFLAGS: $(LDFLAGS)'
echo '	@echo =================='
echo '	@echo LDFLAGS_CLI: $(LDFLAGS_CLI)'
echo '	@echo =================='
echo '	@echo _VC_MANIFEST_EMBED_EXE: $(_VC_MANIFEST_EMBED_EXE)'
echo '	@echo =================='
echo '	@echo PHPDEF: $(PHPDEF)'
echo '	@echo =================='
echo '	@echo PHPDLL_RES: $(PHPDLL_RES)'
echo '	@echo ==================
echo '	@echo ASM_OBJS: $(ASM_OBJS)'
echo '	@echo =================='
echo '	@echo MCFILE: $(MCFILE)'
echo '	@echo =================='
echo '	@"$(LINK)" /nologo  $(CLI_GLOBAL_OBJS_RESP) $(BUILD_DIR)\$(PHPLIB) $(LIBS_CLI) $(BUILD_DIR)\php.exe.res /out:$(BUILD_DIR)\php.exe $(LDFLAGS) $(LDFLAGS_CLI) '
echo '	-@$(_VC_MANIFEST_EMBED_EXE)'
echo '	@echo SAPI sapi\cli build complete'
) >> %__PROJECT__%\Makefile


nmake release-php

rem nmake install

cd %__PROJECT__%
endlocal

