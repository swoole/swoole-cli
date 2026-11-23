@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libzip"
if exist "%__PROJECT__%\build\libzip\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%
cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libzip-1.9.2.tar.gz

if  exist "%__PROJECT__%\thirdparty\libzip" rmdir /s /q "%__PROJECT__%\thirdparty\libzip"
mkdir "%__PROJECT__%\thirdparty\libzip"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libzip"  %__PROJECT__%\pool\lib\libzip-1.9.2.tar
cd %__PROJECT__%\thirdparty\libzip\libzip-1.9.2\

dir
set "LIBLZ4_PREFIX=%__PROJECT__%\build\liblz4"
set "LIBLZMA_PREFIX=%__PROJECT__%\build\liblzma"
set "LIBZLIB_PREFIX=%__PROJECT__%\build\zlib"
set "LIBZSTD_PREFIX=%__PROJECT__%\build\libzstd"
set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "BZIP2_PREFIX=%__PROJECT__%\build\bzip2"

mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libzip" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DCMAKE_POLICY_DEFAULT_CMP0074=NEW ^
-DCMAKE_POLICY_DEFAULT_CMP0111=NEW ^
-DBUILD_TOOLS=OFF ^
-DBUILD_EXAMPLES=OFF ^
-DBUILD_DOC=OFF ^
-DLIBZIP_DO_INSTALL=ON ^
-DENABLE_GNUTLS=OFF  ^
-DENABLE_MBEDTLS=OFF ^
-DENABLE_OPENSSL=ON ^
-DOPENSSL_USE_STATIC_LIBS=TRUE ^
-DENABLE_BZIP2=ON ^
-DENABLE_COMMONCRYPTO=OFF ^
-DENABLE_LZMA=ON ^
-DENABLE_ZSTD=ON ^
-DOpenSSL_ROOT="%OPENSSL_PREFIX%" ^
-DZLIB_ROOT="%LIBZLIB_PREFIX%" ^
-DLibLZMA_ROOT="%LIBLZMA_PREFIX%" ^
-DLIBLZMA_LIBRARY="%LIBLZMA_PREFIX%\lib\liblzma_a.lib" ^
-DLibLZMA_INCLUDE_DIR="%LIBLZMA_PREFIX%\include" ^
-DZstd_ROOT="%LIBZSTD_PREFIX%" ^
-DZstd_LIBRARY="%LIBZSTD_PREFIX%\lib\libzstd_a.lib" ^
-DZstd_INCLUDE_DIR="%LIBZSTD_PREFIX%\include" ^
-DBZip2_ROOT="%BZIP2_PREFIX%" ^
-DBZIP2_LIBRARIES="%BZIP2_PREFIX%\lib\libbz2_a.lib" ^
-DBZIP2_LIBRARY="%BZIP2_PREFIX%\lib\libbz2_a.lib" ^
-DBZIP2_INCLUDE_DIR="%BZIP2_PREFIX%\include" ^
-DCMAKE_PREFIX_PATH="%LIBZSTD_PREFIX%;%LIBLZMA_PREFIX%;%LIBZLIB_PREFIX%;%LIBZSTD_PREFIX%;%OPENSSL_PREFIX%;%BZIP2_PREFIX%;" ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG /DLZMA_API_STATIC " ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_EXE_LINKER_FLAGS="/VERBOSE:LIB" ^
-DCMAKE_VERBOSE_MAKEFILE=ON


cmake --build . --config Release --target install

copy /Y %__PROJECT__%\build\libzip\lib\zip.lib "%__PROJECT__%\build\libzip\lib\zip_a.lib"
copy /Y %__PROJECT__%\build\libzip\lib\zip.lib "%__PROJECT__%\build\libzip\lib\libzip_a.lib"

type nul > "%__PROJECT__%\build\libzip\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\libzip\lib\libzip_a.lib" | findstr /i "DEFAULTLIB"
dumpbin /SYMBOLS "%__PROJECT__%\build\libzip\lib\libzip_a.lib" | findstr "__imp_"
dumpbin /SYMBOLS "%__PROJECT__%\build\libzip\lib\libzip_a.lib" | findstr "ZSTD_"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libzip build completed ! "

endlocal
