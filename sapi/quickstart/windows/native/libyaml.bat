@echo off


cd thirdparty\libyaml
dir
mkdir build
cd build
cmake .. ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON

cmake --build . --config Release --target install
