@echo off


cd thirdparty\zlib
dir
mkdir build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX=c:\php-cli\zlib ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON

cmake --build . --config Release --target install
