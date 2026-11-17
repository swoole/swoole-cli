@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build"

if exist "%__PROJECT__%\build\lib\libcurl.lib" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\

if not exist "curl-8.21.0.tar.gz" curl.exe -fSLo curl-8.21.0.tar.gz  https://github.com/curl/curl/releases/download/curl-8_21_0/curl-8.21.0.tar.gz

7z.exe x -aoa -y   %__PROJECT__%\pool\lib\curl-8.21.0.tar.gz

if  exist "%__PROJECT__%\thirdparty\curl" rmdir /s /q "%__PROJECT__%\thirdparty\curl"
mkdir "%__PROJECT__%\thirdparty\curl"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\curl"  %__PROJECT__%\pool\lib\curl-8.21.0.tar

cd /d %__PROJECT__%\thirdparty\curl\curl-8.21.0\

dir

:: set "LIBCURL_PREFIX=%__PROJECT__%\build\curl"
set "LIBCURL_PREFIX=%__PROJECT__%\build"
set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "LIBNGHTTP2_PREFIX=%__PROJECT__%\build\libnghttp2"
set "LIBPSL_PREFIX=%__PROJECT__%\build\libpsl"
set "LIBZLIB_PREFIX=%__PROJECT__%\build\zlib"
set "LIBSSH2_PREFIX=%__PROJECT__%\build\libssh2"
set "LIBPSL_PREFIX=%__PROJECT__%\build\libpsl"
set "LIBBROTLI_PREFIX=%__PROJECT__%\build\brotli"
set "LIBZSTD_PREFIX=%__PROJECT__%\build\libzstd"
set "LIBCARES_PREFIX=%__PROJECT__%\build\cares"
set "LIBNGHTTP3_PREFIX=%__PROJECT__%\build\libnghttp3"
set "NGTCP2_PREFIX=%__PROJECT__%\build\ngtcp2"

mkdir  build
cd build
cmake -B . -S  .. ^
-DCMAKE_INSTALL_PREFIX="%LIBCURL_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DCURL_STATIC_CRT=ON ^
-DBUILD_STATIC_CURL=ON ^
-DENABLE_ARES=ON ^
-DENABLE_UNICODE=ON ^
-DCURL_ENABLE_SSL=ON ^
-DCURL_USE_OPENSSL=ON ^
-DCURL_USE_SCHANNEL=OFF ^
-DCURL_USE_MBEDTLS=OFF ^
-DCURL_USE_WOLFSSL=OFF ^
-DCURL_USE_GNUTLS=OFF ^
-DCURL_USE_RUSTLS=OFF ^
-DCURL_USE_LIBSSH2=ON ^
-DUSE_NGHTTP2=ON ^
-DUSE_NGTCP2=ON ^
-DUSE_WIN32_IDN=ON ^
-DCURL_BROTLI=ON ^
-DCURL_ZLIB=ON ^
-DCURL_BROTLI=ON ^
-DCURL_ZSTD=ON ^
-DCURL_USE_LIBPSL=ON ^
-DZLIB_ROOT=%LIBZLIB_PREFIX% ^
-DZSTD_LIBRARY=%LIBZSTD_PREFIX%\lib\libzstd_a.lib ^
-DCMAKE_EXE_LINKER_FLAGS="/VERBOSE:LIB" ^
-DCMAKE_VERBOSE_MAKEFILE=ON ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DPSL_STATIC /DNGHTTP2_STATICLIB /DCARES_STATICLIB " ^
-DCMAKE_PREFIX_PATH="%OPENSSL_PREFIX%;%LIBZLIB_PREFIX%;%LIBSSH2_PREFIX%;%LIBBROTLI_PREFIX%;%LIBZSTD_PREFIX%;%LIBNGHTTP2_PREFIX%;%LIBPSL_PREFIX%;%LIBCARES_PREFIX%;%LIBNGHTTP3_PREFIX%;%NGTCP2_PREFIX%;"




:: -DENABLE_UNICODE=ON
:: -DUSE_LIBIDN2=ON ^
:: -DCURL_USE_SCHANNEL=ON ^ enable Windows os native  SSL/TLS

cmake --build . --config Release
cmake --build . --config Release --target install


copy /Y %__PROJECT__%\build\lib\libcurl.lib "%__PROJECT__%\build\lib\libcurl_a.lib"

dumpbin /DIRECTIVES %__PROJECT__%\build\lib\libcurl.lib | findstr /i "DEFAULTLIB"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "curl build completed ! "

endlocal
