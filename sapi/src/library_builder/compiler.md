交叉编译 cross-compile binutils

宿主机（host）

目标机（target）

prefix：交叉编译器的安装位置

xxx-xxxx-xxxxx 平台描述

交叉编译器

预处理器（preprocessor）
编译器前端（frontend）
负责解析（parse）输入的源代码 负责语义（semantic checking）的检查 最终的结果常常是一个抽象的语法树（abstract syntax tree，或
AST）
编译器后端（backend）
主要负责分析，优化中间代码（Intermediate representation）以及生成机器代码（Code Generation）

g++ your_program.cpp -o your_program -ldl -Wl,-Bstatic -lxx -Wl,-Bdynamic

-Wl,-Bstatic 表示后面的库需要静态链接，-Wl,-Bdynamic 表示后面的库需要动态链接。

ld main.o -o main.out -pie --no-dynamic-linker

-Wl,--no-dynamic-linker

pkg-config libelf --exists

CC 编译器，对C源文件进行编译处理，生成汇编文件
LD 链接器(来自"链接编辑器"或"加载程序")。

CPP 代表" C预处理程序"
CXX 是C ++编译器
AS 是汇编语言编译器 将汇编文件生成目标文件（汇编文件使用的是指令助记符， AS将它翻译成机器码）
AR 是一个存档维护程序 打包器，用于库操作，可以通过该工具从一个库中删除或者增加目标代码模块
STRIP：以最终生成的可执行文件或者库文件作为输入，然后消除掉其中的源码
NM：查看静态库文件中的符号表
Objdump：查看静态库或者动态库的方法签名

cmake 生成依赖图
cmake --graphviz=./ffmpeg.dot

CMake toolchain file for cross compiling
cmake -DCMAKE_TOOLCHAIN_FILE="crosscompile.cmake"

clang 传递编译器参数例子：
clang -o vlc vlc.c.o -Wl,--as-needed -Wl,--no-undefined -Wl,-O1 -pie -Wl,--start-group modules/access/http/libvlc_http.a
compat/libcompat.a src/libvlccore.a -pthread -lm -ldl -Wl,--end-group

由于 OpenMP 内置于编译器中，因此无需安装外部库即可编译此代码
https://curc.readthedocs.io/en/latest/programming/OpenMP-C.html

自动微分 Adolc
https://github.com/coin-or/ADOL-C.git

       * # 需要特别设置的地方
       *   //  CFLAGS='-static -O2 -Wall'

       *    位置无关的可执行文件
       *    直接编译可执行文件 -fPIE
       *    直接编译成库      -fPIC
       地址无关代码（Position Independent Code, PIC）

BPF CO-RE (Compile Once – Run Everywhere)
https://github.com/libbpf/libbpf#bpf-co-re-compile-once--run-everywhere

# static-pie

# 位置无关的可执行文件(PIE)。PIE 是启用地址空间布局随机化 (ASLR) 的先决条件

## macos 库支持静态编译， 二进制程序不支持静态编译

    LIBS='-framework CoreFoundation -framework CoreServices -framework SystemConfiguration"

MIPS架构 龙芯处理器
s390x IBMSystemz系列大型机硬件平台
ppc64le 基于Power架构

clang with MUSL

clang hello.c -I /usr/include/x86_64-linux-musl --target=x86_64-unknown-linux-musl -nostdlib

TLS的四种模式 Global Dynamic,Local Dynamic,Initial Exec和Local Exec

单词缩写解释
Acronyms relevant to Executable and Linkable Format (ELF)
https://stevens.netmeister.org/631/elf.html

ABI： Application binary interface
a.out： Assembler output file format
PIC： Position independent code
PIE： Position independent executable

libtoolize --force --copy --automake
aclocal
autoheader
automake --foreign --copy --add-missing
autoconf
export CFLAGS="-O2 -Wall -W -Wunused-const-variable=0 -pipe -g"

预处理 gcc -E 、clang -E
编译 gcc -S 、clang -S
汇编 gcc -c 、clang -c
链接 gcc -o 、clang -o

C++中的volatile 阻止编译器优化变量

## 生成动态库 (lib 开头 , .so 结尾 )

    gcc -fPIC -shared test.c -O libtest.so

## 生成静态库(lib 开头 , .a 结尾 )

    gcc -c test.c
    ar -crv libtest.a test.o


