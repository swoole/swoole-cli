@echo off

cmake --version

mkdir thirdparty\zlib

tar --strip-components=1 -C thirdparty\zlib -xf pool\lib\zlib-1.3.tar.gz

cd thirdparty\zlib
dir
mkdir build
cd build
cmake .. ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON

cmake --build . --config Release --target install
