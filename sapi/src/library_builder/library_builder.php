<?php


function libraries_builder($p)
{
    install_openssl($p);
    install_libiconv($p);//没有 libiconv.pc 文件 不能使用 pkg-config 命令
    install_ncurses($p);
    install_readline($p);//依赖 ncurses

    install_liblzma($p);
    install_libxml2($p); //依赖 libiconv  liblzma
    install_libxslt($p); //依赖 libxml2 libiconv

    install_brotli($p); //有多种安装方式，选择使用cmake 安装
    install_cares($p);  // swoole 使用 SWOOLE_CFLAGS 实现
    install_gmp($p);    // GNU高精度算术运算库


    install_libyaml($p);
    install_libsodium($p);

    install_bzip2($p);//没有 libbz2.pc 文件，不能使用 pkg-config 命令
    // 使用时类似： BZIP2_LIBS=-L/usr/bizp2/lib -lbz2  BZIP2_CFLAGS="-I/usr/bizp2/include"
    install_zlib($p);
    install_liblz4($p); //有多种安装方式，选择cmake方式安装
    install_libzstd($p); //zstd 依赖 lz4
    install_libzip($p); //zip 依赖 openssl zlib bzip2  liblzma zstd

    install_sqlite3($p);
    install_icu($p);
    //依赖 linux : -lstdc++ ; macOS:  libc++ //注意事项：https://www.zhihu.com/question/343205052
    //CLDR 是 i18n 的一套核心规范( Common Locale Data Respository ) 即 通用的本地化数据存储库
    //https://cldr.unicode.org/

    // php composer 依赖的扩展 ： https://github.com/composer/composer/blob/c23beac9c508b701bb481d1c5269e7a2a79e0b60/src/Composer/Repository/PlatformRepository.php#L203

    install_oniguruma($p);
    //install_mimalloc($p);

    install_libjpeg($p);
    install_libgif($p);//没有 libgif.pc 文件，不能使用 pkg-config 命令
    install_libpng($p); //依赖 zlib

    install_libwebp($p); //依赖 libgif libpng libjpeg
    install_freetype($p); //依赖 zlib bzip2 libpng  brotli  HarfBuzz  (HarfBuzz暂不启用，启用需要安装ninja meson python3 pip3 进行构建)

    install_imagemagick($p);
    //依赖 freetype2 libjpeg  libpng libwebp libxml2 libzip zlib libzstd liblzma bzlib2
    //  lcms(默认不启用) libraw(默认不启用) libtiff(默认不启用) libjxl(默认不启用)

    install_libidn2($p);//依赖 intl libunistring ； (gettext库包含intl 、coreutils库包含libunistring );
    // //解决依赖 apk add  gettext  coreutils

    install_libssh2($p);
    install_nghttp2($p); //依赖 install_nghttp2($p);
    install_nghttp3($p); // 使用 GnuTLS或者wolfss，这样就不用更换openssl版本了 ；
    install_ngtcp2($p); //依赖gnutls nghttp3


    install_curl($p); //curl 依赖 openssl c-ares brotli libzstd idn(暂不启用) libidn2 libnghttp2 libnghttp3(暂不启用)

    //参考 https://github.com/docker-library/php/issues/221
    //install_pgsql($p);//依赖 openssl libxml2 libxslt  zlib readline icu libxml2 libxslt liblzma libiconv
    //install_libffi($p);

    //扩展不兼容本项目
    //install_libmcrypt($p); //无 pkg-config 配置
    //扩展参数还需要调试
    //install_libxlsxwriter($p); //依赖zlib openssl （使用cmake，便于配置参数)
    //install_libexpat($p); //依赖zlib openssl （使用cmake，便于配置参数)
    //install_minizip($p);
    //install_libxlsxio($p); //依赖zlib openssl （使用cmake，便于配置参数)
    // Use libzip instead of Minizip

    //扩展不兼容本项目
    //install_libevent($p);
    //install_libuv($p);


    # 扩展 mbstring 依赖 oniguruma 库
    # 扩展 intl 依赖 ICU 库
    # 扩展 gd 依赖 libpng，freetype 库 ；  freetype 依赖 zlib bzip2 libpng  brotli 等;  libwebp 依赖 giflib
    # 扩展 mongodb 依赖 openssl, zlib, ICU 等库
    # 本项目 opcache 是必装扩展，否则编译报错，不想启用opcache，需要修改源码: main/main.c
    # 本项目 swoole  是必装扩展，否则 sh make.sh archive 无法打包

    # php7 不支持openssl V3 ，PHP8 支持openssl V3 , openssl V3 默认库目录 /usr/openssl/lib64

    # label: build_path_bin , php_extension_patch , php_internal_extension , php_extension ,extension_library

    # pdo_pgsql,pdo_oci,pdo_odbc,ldap,ffi

    /**
     * # 需要特别设置的地方
     *   //  CFLAGS='-static -O2 -Wall'
     *     直接编译可执行文件 -fPIE
    *      直接编译成库      -fPIC
     *
     * export  CPPFLAGS=$(pkg-config  --cflags --static  libpq libcares libffi icu-uc icu-io icu-i18n readline )
     * LIBS=$(pkg-config  --libs --static   libpq libcares libffi icu-uc icu-io icu-i18n readline )
     * export LIBS="$LIBS -L/usr/lib -lstdc++"
     */
    if ($p->getOsType() == 'win') {
        install_re2c($p);
    }

    if ($p->getOsType() == 'macos') {
        install_bison($p);  // 源码编译bison, mongodb 需要
    }

    if (0) {
        install_php_parser($p); //imagemagick 安装过程中需要
    }

    if (0) {
        install_php_internal_extensions($p); //安装内置扩展; ffi  pgsql pdo_pgsql
    }

    if ($p->getOsType() == 'macos') {
        if (0) {
            install_php_internal_extension_curl_patch($p); //修改 `ext/curl/config.m4` ，去掉 `HAVE_CURL` 检测
        }
    }

    //====================================
    ///      TEST  验证
    //====================================

    if (0) {
        install_libgcrypt_error($p); //依赖 libiconv libintl
        install_libgcrypt($p); //依赖 libgcrypt_error
        install_gnupg($p);  // GNU Privacy Guard  ; OpenPGP 标准的完整免费实现 依赖 libgcrypt
    }

    if (0) {
        install_zookeeper_client($p);
        install_unixodbc($p);
    }

    if (0) {
        install_php_extension_swow($p); // libcat for Swow https://github.com/libcat/libcat.git
        install_php_extension_micro($p);
        install_php_extension_zookeeper($p);
        install_php_extension_wasm($p);
        // install_php_extension_fastdfs($p);
    }

    install_nasm($p);
    install_dav1d($p); //AV1解码器dav1d  依赖 nasm : apk add nasm   //https://github.com/videolan/dav1d.git
    install_libgav1($p);
    install_libyuv($p); //libyuv是Google开源的yuv图像处理库，实现对各种yuv数据之间的转换，包括数据转换，裁剪，缩放，旋转
    install_aom($p);
    install_libavif($p); //依赖 libyuv dav1d
    install_libx264($p);
    install_numa($p); //把NUMA看作集群运算的一个紧密耦合的形式 https://baike.baidu.com/item/NUMA/6906025
    install_libx265($p);
    install_libde265($p);
    install_svt_av1($p);
    install_libheif($p); //依赖 libde265
    install_libtiff($p); //依赖  zlib libjpeg liblzma  libzstd
    install_libgd2($p);
    if (0) {

        install_libtiff($p); //依赖  zlib libjpeg liblzma  libzstd
        install_lcms2($p); //lcms2  //依赖libtiff libjpeg zlib
        install_libraw($p);  //依赖 zlib  libjpeg liblcms2
        install_librsvg($p);
        install_libfribidi($p); //以来c2man
        //文本绘制引擎
        install_harfbuzz($p); //依赖ninja icu

        install_libde265($p);
        install_libheif($p); //依赖 libde265


        install_libOpenEXR($p); // 依赖Imath，不存在，会自动到github.com 下载
        install_highway($p);
        install_libjxl($p); //libgif libjpeg libopenexr libpng libwebp libbrotli highway

        install_graphite2($p);
        install_harfbuzz($p); //依赖ninja icu zlib glib

        if (0) {
            install_xorgproto($p); //依赖 xorg-macros
            //install_xorg_macros($p);
            //install_xorgproto($p);
            //install_libX11($p);
            install_libXpm($p); //依赖 xorg-macros  xorgproto libx11 # apk add util-macros xorgproto libx11
        }


        //GraphicsMagick  http://www.graphicsmagick.org/index.html
        install_GraphicsMagick($p);
    }

    if (0) {

        install_openssl_v1($p);
        install_openssl_v3($p);
        install_openssl_v3_quic($p);
        install_libedit($p);


        install_libdeflate($p); //依赖 libzip zlib gzip
        install_bzip2_dev_latest($p);


        install_unbound($p); //依赖 libsodium nghttp2 nettle openssl ibtasn1 libexpat
        install_p11_kit($p);
        # TLS/ESNI/ECH/DoT/DoH/  参考文档https://zhuanlan.zhihu.com/p/572101957
        # SSL 比较 https://curl.se/docs/ssl-compared.html
        install_nettle($p); //加密库
        install_libtasn1($p);
        install_gnutls($p); //依赖 gmp libiconv  libtasn1 libzip  libzstd libbrotli libzlib
        install_nghttp3($p); // 使用 GnuTLS或者wolfss，这样就不用更换openssl版本了 ；
        install_libev($p); //无 pkg-config
        install_ngtcp2($p); //依赖gnutls nghttp3
        install_nghttp2($p); //依赖 install_nghttp2($p);
        install_boringssl($p);//需要golang

        install_wolfssl($p);//
        install_libressl($p);//

        install_jansson($p); //c json 库

        //参考 ：HTTP3 and QUIC 有多种实现   curl 使用 http3 参考： https://curl.se/docs/http3.html



        install_quiche($p); // 依赖 boringssl ，需要 rust ；
        install_msh3($p);  //需要安装库 bsd-compat-headers 解决 sys/queue.h 不存在的问题


        install_coreutils($p);
        install_gnulib($p);
        install_libunistring($p); //coreutils 包含  libiconv
        install_gettext($p);// gettext 包含 intl

        install_libfastcommon($p);
        install_libserverframe($p);
        install_fastdfs($p); //依赖 libfastcommon libserverframe


        install_libunwind($p); //使用 libunwind 可以很方便的获取函数栈中的内容，极大的方便了对函数间调用关系的了解。

        install_jemalloc($p);
        install_tcmalloc($p);


        install_libelf($p);
        install_libbpf($p); //libbpf 库是一个基于 C/C++ 的通用 eBPF 库

        install_snappy($p);
        install_kerberos($p);
        install_fontconfig($p);
        //install_pcre2($p);
        install_pgsql_test($p);
        install_libgomp($p); //压缩算法
        install_libzip_ng($p); //zlib next
    }





    if (0) {
        install_aria2($p); //依赖libuv openssl zlib libxml2 sqlite3 openssl c-ares
        install_socat($p); //依赖 readline openssl
    }
    if (0) {
        install_pcre2($p);
        install_nginx($p);
    }

    if (0) {
        //Wasm
        //WebAssembly
        //Docker+Wasm  https://docs.docker.com/desktop/wasm/
    }

    //排版相关
    if (0) {
        //Linux_kernel_diagram   // https://makelinux.github.io/kernel/diagram/
        //  panzoom  用于向元素添加平移和缩放功能 https://timmywil.com/panzoom/demo/

        # apk add graphviz
        # pip3 install graphviz   -i https://pypi.tuna.tsinghua.edu.cn/simple
        //plantuml
        // https://www.graphviz.org/documentation/
        // https://www.graphviz.org/doc/info/lang.html
        install_graphviz($p); //依赖git libwbp freetype
        //networkx    //https://github.com/networkx/networkx.git
        //graphviz 是一个专门用于可视化图状数据结构的工具包，而networkx是专门用于表示图状数据结构以及操作图状数据结构的工具包。
        // 工业级的还得用neo4j搭配graphx  面对巨量数据   https://www.cnblogs.com/jingjingxyk/p/16826546.html
        // draw.io  https://app.diagrams.net/

        // ASCIIFlow Infinity  https://asciiflow.com/
        // flowchart.js        http://flowchart.js.org/
        // js-sequence-diagrams by bramp  https://bramp.github.io/js-sequence-diagrams/

        install_TeX($p); //排版系统


        /*
            UML - Unified modeling language UML 统一建模语言

            泛化（Generalizations），聚合（aggregations）和关联（associations）
            分栏（Rectangle）

            依赖（Dependencies）

            跟踪（Traces）
            实现（Realizations）
            嵌套（Nestings）

         */
        /*
           生成器（iterabler)
           生成器（Generator)

         */
        /*
           享有数据分析“三剑客之一”的盛名（NumPy、Matplotlib、Pandas）
         */
    }
    if (0) {
        /*
            gsm
            alsa-lib
            opus
            libsamplerate
            jack
            portaudio
            speex
            speexdsp
            libsrtp
            pjproject
        */
    }

    install_rav1e($p);  //https://www.cnblogs.com/eguid/p/16015446.html
    if (0) {

        install_aom($p);
        install_av1($p);
        install_libvpx($p);
        install_libopus($p);
        install_libx264($p);
        install_libx265($p);
        install_mp3lame($p);
        install_ffmpeg($p);
        install_vlc($p);
        /*
        ffmpeg -encoders
        ffmpeg -decoders
        ffmpeg -codecs
        ffmpeg -formats
        ffmpeg -muxers
        ffmpeg -demuxers
        ffmpeg -protocols
        ffmpeg -filters
        */

        // install_librabbitmq($p);
        install_opencv_contrib($p);
        install_opencv($p); //构建过程中，会去github.com 下载 ippicv xfeatures2d wechat_qrcode unifont  face_landmark_model.dat
        //依赖ffmpeg zlib ninja zlib libjpeg libwebp freetype

        //A free, open source XR platform
        install_monado($p);// https://gitlab.freedesktop.org/monado/monado
    }
    if (0) {
        // 改善iptables/netfilter的规模瓶颈，提高Linux内核协议栈IO性能
        // DPDK让用户态程序直接处理网络流，bypass掉内核，使用独立的CPU专门干这个事。

        // Berkeley Packet Filter (eBPF)
        // XDP让灌入网卡的eBPF程序直接处理网络流，bypass掉内核，使用网卡NPU专门干这个事。
        // XDP的全称是： eXpress Data Path

        //  XDP 是Linux 内核中提供高性能、可编程的网络数据包处理框架。
        //  eBPF/XDP

        //SDN（Software Defined Networking） 它将网络控制层和数据层分离，使得网络可以通过软件进行灵活的配置和管理。
        //DPDK（Data Plane Development Kit） 它提供了一套高性能的数据包处理库和驱动程序，可以让应用程序直接访问网络设备，绕过操作系统的开销。
        // VPP（Vector Packet Processing）它基于DPDK实现了一个高性能的软件路由器和交换机，可以支持多种协议和功能。
        // FRR（Free Range Routing）是一个开源项目，它提供了一套路由协议的实现，包括BGP、OSPF、IS-IS等，可以与VPP集成，实现动态路由功能。
        install_dpdk($p); //ninja
        install_xdp($p);  //依赖 llvm bpftool
        //Underlay/Overlay 新技术
        install_ovs($p);  //依赖 openssl python3  ; 网络优化以来 dpdk
        install_ovn($p);
        install_FRR($p);  //路由协议栈 实现和管理各种 IPv4 和 IPv6 路由协议的免费软件 //https://github.com/FRRouting/frr.git
        //'https://www.bianyuanyun.com/wp-content/uploads/2021/06/whitepaper-未来网络白皮书——白盒交换机技术白皮书.pdf'
    }

    if (0) {
        install_qemu($p);
    }

    //分布式构建(Distributed Builds)
    if (0) {
        install_bazel($p);  //use bazel docker https://bazel.build/install/docker-container

        //install_icecream($p); //https://github.com/icecc/icecream.git

        //原理： 类似 SwarmAgent  （Agent/Coordinator ）  //https://docs.unrealengine.com/5.1/en-US/unreal-swarm-in-unreal-engine/
    }

    if ($p->getInputOption('with-valgrind') == 'yes') {
        echo 111111111111111 . PHP_EOL;
        install_valgrind($p); //Valgrind是一款用于内存调试、内存泄漏检测以及性能分析的软件开发工具。
    }
    if ($p->getInputOption('with-capstone') == 'yes') {
        install_capstone($p);
    }
    install_rust($p);
    if (0) {
        // brew  //  https://mirrors.tuna.tsinghua.edu.cn/help/homebrew
        // brew  //  https://github.com/Homebrew/brew.git
        //apk add ninja
        install_rust($p);
        install_nodejs($p);
        install_golang($p);
        install_depot_tools($p); //依赖python

        //install_ninja($p); //源码编译ninja，alpine 默认没有提供源；默认不安装 //依赖python
        //install_depot_tools($p); //依赖python
        //install_gn($p);//依赖python
        //install_gn_test($p);//源码编译GN

        // sanitizer  动态代码分析的工具
        // AddressSanitizer (ASan)，检测内存问题，包括了 LeakSanitizer
        // LeakSanitizer (LSan)，检测内存泄漏问题
        // ThreadSanitizer (TSan)，检测数据竞争问题
        // UndefinedBehaviorSanitizer (UBSsan)，检测未定义行为
        // MemorySanitizer (MSan)，检测未初始化内存问题

        // capstone 反汇编工具 http://www.capstone-engine.org/
        install_capstone($p);

        install_valgrind($p); //Valgrind是一款用于内存调试、内存泄漏检测以及性能分析的软件开发工具。

        //#if defined(HAVE_DISASM) || defined(HAVE_GDB) || defined(HAVE_OPROFILE) || defined(HAVE_PERFTOOLS) || defined(HAVE_VTUNE)
        //dynasm
        install_dynasm($p);

        //perf-tools简介一个开发中的Linux性能测试使用的工具,能够收集ftrace和perf_events中乱七八糟的参数。ftrace和perf都是Linux中的内核跟踪工具

        //op-agent  OpManager提供全面的网络监控功能，可帮助您监控网络性能，实时检测故障隐患

        //OProfile是Linux内核支持的一种性能分析机制。 它在时钟中断处理入口处建立监测点，记录被中断的上下文现场，由配套的用户态的工具oprof_start负责在用户态收集数据

        //nm  结果参考 https://www.cnblogs.com/vaughnhuang/p/15771582.html
        // gcc的ar,nm,objdump,objcopy

        //gdb bin/swoole-cli
        //set args -m
        //run


        //下载 boringssl 镜像地址 https://source.codeaurora.org/quic/lc

        //Vtune Threading Profiler是线程性能检测工具 , 分析负载平衡、同步开销过大等线程相关的性能问题

        //Perfdump 工具 Perfdump 是一个系统软件,当系统崩溃时,会调用它生成错误信息,供软件开发人员分析用


        // 动态链接库和静态链接库 https://www.cnblogs.com/Blog-c/p/7811190.html
        // .la 为libtool生成的共享库，其实是个配置文档。可以用file或者vim查看。
        // .ko 文件是Linux内核使用的动态链接文件后缀，属于模块文件，用在Linux系统启动时加载内核模块

        //  gcov是一个测试代码覆盖率的工具。 https://zhuanlan.zhihu.com/p/410077415
    }

    if (0) {
        install_grpc($p); //use protobuf  https://github.com/grpc/grpc.git
        install_thrift($p); //https://thrift.apache.org/
    }
    if (0) {
        install_boost($p);
    }
    if (0) {
        //申明式  和 命令式

        //一个为异构并行计算平台编写程序的工业标准  https://www.intel.com/content/www/us/en/docs/programmable/683846/22-1/opencl-library.html
        install_opencl($p); //OpenCL全称为Open Computing Language（开放计算语言） OpenCL不但支持数据并行，还支持任务并行
        //用于共享内存并行系统的多处理器程序设

        //metal，opencl   vulkan和metal除了通用计算，还能做渲染  ;
        // CUDA，OpenCL，Metal GPU加速有什么区别 更多信息 https://www.zhihu.com/question/481772259/answer/2762594628

        //Openmp和thread都是共享一个进程内存的并行，openmp最显著的特点是命令式(directive-based)语言
        //install_openmp($p);

        //并发编程：SIMD 介绍  https://zhuanlan.zhihu.com/p/416172020
    }
    /*
    export PATH=$SYSTEM_ORIGIN_PATH
    export PKG_CONFIG_PATH=$SYSTEM_ORIGIN_PKG_CONFIG_PATH
    # 执行构建前

    # 执行构建操作

    # 执行构建后
    export PATH=$SWOOLE_CLI_PATH
    export PKG_CONFIG_PATH=$SWOOLE_CLI_PKG_CONFIG_PATH
    */

    /**
     * 交叉编译
        --build=BUILD           configure for building on BUILD [BUILD=HOST]
        --host=HOST             configure for HOST [guessed]
        --target=TARGET         configure for TARGET [TARGET=HOST]

     */

    /**
     * BIO 全称Block-IO 是一种阻塞同步的通信模式 BIO 全称Block-IO 是一种阻塞同步的通信模式。我们常说的Stock IO 一般指的是BIO。是一个比较传统的通信方式，模式简单，使用方便。但并发处理能力低，通信耗时，依赖网速。
     * NIO 全称New IO，也叫Non-Block IO 是一种非阻塞同步的通信模式。
     * AIO 也叫NIO2.0 是一种非阻塞异步的通信模式。在NIO的基础上引入了新的异步通道的概念，并提供了异步文件通道和异步套接字通道的实现。
     * AIO 并没有采用NIO的多路复用器，而是使用异步通道的概念
     *
     */

    /**
     * LC_ALL=C 是为了去除所有本地化的设置
     */

    /**
            SYSTEM=`uname -s 2>/dev/null`
            RELEASE=`uname -r 2>/dev/null`
            MACHINE=`uname -m 2>/dev/null`

            PLATFORM="$SYSTEM:$RELEASE:$MACHINE";
     */

    /**
     *     export CFLAGS="$(pkg-config  --cflags --static expat minizip ) "

           SET (CMAKE_EXE_LINKER_FLAGS "-static")

            target
                    ARCHIVE 静态库
                    LIBRARY 动态库
                    RUNTIME  可执行二进制文件

            # find_package的简单用法   https://blog.csdn.net/weixin_43940314/article/details/128252940
                      -D 从外部传入搜索路径：
                            <PackageName>_ROOT
                            <PackageName>_DIR

             c++(CMake篇)  https://zhuanlan.zhihu.com/p/470681241
            # CMAKE_BUILD_TYPE=Debug Release

            cmake -G"Unix Makefiles" .  \
            -DCMAKE_INSTALL_PREFIX={$libxlsxio_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$libminzip_prefix}/lib \
            -DCMAKE_INSTALL_INCLUDEDIR={$libminzip_prefix}/include \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_COLOR_MAKEFILE=ON

            set(libgav1_root "${CMAKE_CURRENT_SOURCE_DIR}")
            set(libgav1_build "${CMAKE_BINARY_DIR}")

            cmake -G 'Unix Makefiles' -DCMAKE_BUILD_TYPE=RelWithDebInfo ..
            cmake --build .
            cmake --install .

            cmake -G 'Visual Studio 17 2022' -DCMAKE_BUILD_TYPE=RelWithDebInfo ..
            cmake --build . --config Release
            cmake --install . --config Release

           CURL ARCHITECTURE   https://curl.se/docs/install.html#:~:text=26%20CPU%20Architectures
           CURL Cross compile  https://curl.se/docs/install.html#:~:text=Cross%20compile
     */


    //Hot Module Replacement（以下简称 HMR） inotify

    /*
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static libpng libjpeg dav1d libgav1)" \
            LDFLAGS="$(pkg-config --libs-only-L      --static libpng libjpeg dav1d libgav1)" \
            LIBS="$(pkg-config --libs-only-l         --static libpng libjpeg dav1d libgav1)" \

     */
    /*
         # https://mesonbuild.com/Builtin-options.html#build-type-options
         # meson configure build
         # meson wrap --help
        meson setup  build \
        -Dprefix={$xorgproto_prefix} \
        -Dbackend=ninja \
        -Dbuildtype=release \
        -Ddefault_library=static \
        -Db_staticpic=true \
        -Db_pie=true \
        -Dprefer_static=true
    */

    /*
        NUMA（Non Uniform Memory Access）技术可以使众多服务器像单一系统那样运转，同时保留小系统便于编程和管理的优点

    计算平台的体系结构  https://baike.baidu.com/item/NUMA/6906025
        当今数据计算领域的主要应用程序和模型可大致分为
        联机事务处理（OLTP）、
        决策支持系统（DSS）和企业信息通讯（BusinessCommunications）三大类。
        而小型独立服务器模式、SMP（对称多处理）模式、MPP（大规模并行处理）模式和NUMA模式，则是上述3类系统设计人员在计算平台的体系结构方面可以采用的选择。
     */

}
