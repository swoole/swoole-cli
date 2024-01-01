## cmake 构建

```bash

test -d build && rm -rf build
mkdir -p  build
cd build
cmake ..   -DCMAKE_BUILD_TYPE=Debug

cmake --build . --config Release

#cmake --build . --config Release --target install

./opencv_static_demo

```

## 使用 pkg-config 方式 使用 opencv5

```bash

source /home/jingjingxyk/swoole-cli/make-env.sh

PACKAGES='openssl  '
PACKAGES="$PACKAGES  zlib"
PACKAGES="$PACKAGES  opencv5 "

CPPFLAGS="$(pkg-config  --cflags-only-I  --static $PACKAGES)"
LDFLAGS="$(pkg-config   --libs-only-L    --static $PACKAGES) "
LIBS="$(pkg-config      --libs-only-l    --static $PACKAGES)"
REQUIRED_LIBRARIES="$(pkg-config --libs  --static $PACKAGES)"

CPPFLAGS="$CPPFLAGS -I/usr/local/swoole-cli/bzip2/include -I/usr/local/swoole-cli/libiconv/include -I/usr/local/swoole-cli/opencv/include/opencv5/ "
LDFLAGS="$LDFLAGS -L/usr/local/swoole-cli/bzip2/lib -L/usr/local/swoole-cli/libiconv/lib "
LIBS="$LIBS -lbz2 -liconv "


# clang++ -o main main.cpp `pkg-config --libs --cflags opencv5 `

clang++ --static -static -fpie -fPIE -o main main.cpp $CPPFLAGS $LDFLAGS $LIBS


```

## cmake 使用 opencv5
    cmake  set-pkg-config-path-in-cmake

    https://stackoverflow.com/questions/44487053/set-pkg-config-path-in-cmake

```cmake
# CMakeLists.txt

cmake_minimum_required(VERSION 3.22)
project(opencv_static_demo)

set(CMAKE_CXX_STANDARD 23)
set(CMAKE_C_STANDARD 23)

set(BUILD_STATIC_LIBS OFF)
set(BUILD_STATIC_LIBS ON)
if(NOT BUILD_SHARED_LIBS AND NOT BUILD_STATIC_LIBS)
    message(FATAL_ERROR "Both BUILD_SHARED_LIBS and BUILD_STATIC_LIBS have been disabled")
endif()


set(CMAKE_PREFIX_PATH /usr/local/swoole-cli/opencv/)
set(ENV{PKG_CONFIG_PATH} "/usr/local/swoole-cli/opencv//lib/pkgconfig:$ENV{PKG_CONFIG_PATH}")

# find_package(PkgConfig REQUIRED)
# pkg_check_modules(opencv REQUIRED IMPORTED_TARGET libopencv_core libopencv_imgproc libopencv_imgcodecs )


#set(OPENCV_ROOT_DIR /usr/local/swoole-cli/opencv/)
#find_package(opencv REQUIRED)
# add_library(opencv5)
#add_library(opencv5 SHARED IMPORTED)

# include_directories("/usr/local/swoole-cli/opencv/include/opencv5")
# link_directories("/usr/local/swoole-cli/opencv/lib")






# find_package(OpenCV REQUIRED COMPONENTS core imgproc highgui)
# find_package(OpenCV REQUIRED)
# message(${OpenCV_LIBS})
# include_directories(${OpenCV_INCLUDE_DIRS})

#add_executable(opencv_static_demo main.cpp)
#target_link_libraries (${PROJECT_NAME} ${OpenCV_LIBS})


find_package(PkgConfig REQUIRED)
pkg_search_module(PKG_OPENCV REQUIRED IMPORTED_TARGET opencv5)
add_executable(${PROJECT_NAME} main.cpp)
target_link_libraries(${PROJECT_NAME} PRIVATE PkgConfig::PKG_OPENCV)


```
