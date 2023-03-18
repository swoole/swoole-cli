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

    install_bzip2($p);//没有 libbz2.pc 文件，不能使用 pkg-config 命令  BZIP2_LIBS=-L/usr/bizp2/lib -lbz2  BZIP2_CFLAGS="-I/usr/bizp2/include"
    install_zlib($p);
    install_liblz4($p); //有多种安装方式，选择cmake方式安装
    install_libzstd($p); //zstd 依赖 lz4
    install_libzip($p); //zip 依赖 openssl zlib bzip2  liblzma zstd

    install_sqlite3($p);
    install_icu($p); //依赖 linux : -lstdc++ ; macOS:  libc++ //注意事项：https://www.zhihu.com/question/343205052
    install_oniguruma($p);
    install_mimalloc($p);

    install_libjpeg($p);
    install_libgif($p);//没有 libgif.pc 文件，不能使用 pkg-config 命令
    install_libpng($p); //依赖 zlib

    install_libwebp($p); //依赖 libgif libpng libjpeg
    install_freetype($p); //依赖 zlib bzip2 libpng  brotli  HarfBuzz  (HarfBuzz暂不启用，启用需要安装ninja meson python3 pip3 进行构建)

    install_imagemagick($p);//依赖 freetype2 libjpeg  libpng libwebp libxml2 libzip zlib libzstd liblzma bzlib2  lcms(默认不启用) libraw(默认不启用) libtiff(默认不启用) libjxl(默认不启用)

    install_libidn2($p);//依赖 intl libunistring ； (gettext库包含intl 、coreutils库包含libunistring ); //解决依赖 apk add  gettext  coreutils
    install_curl($p); //curl 依赖 openssl c-ares brotli libzstd idn(暂不启用) libidn2 libnghttp2 libnghttp3

    //参考 https://github.com/docker-library/php/issues/221
    install_pgsql($p);//依赖 openssl libxml2 libxslt  zlib readline icu libxml2 libxslt liblzma libiconv
    install_libffi($p);

    //扩展不兼容本项目
    //install_libmcrypt($p); //无 pkg-config 配置
    //扩展参数还需要调试
    //install_libxlsxwriter($p); //依赖zlib openssl （使用cmake，便于配置参数)
    //install_libexpat($p); //依赖zlib openssl （使用cmake，便于配置参数)
    //install_minizip($p);
    //install_libxlsxio($p); //依赖zlib openssl （使用cmake，便于配置参数)
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

    if (1) {
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

        install_libyuv($p);
        install_libavif($p); //依赖 libyuv
        install_libOpenEXR($p); // 依赖Imath，不存在，会自动到github.com 下载
        install_highway($p);
        install_libjxl($p); //libgif libjpeg libopenexr libpng libwebp libbrotli highway

        install_graphite2($p);
        install_harfbuzz($p); //依赖ninja icu zlib glib

        if (0) {
            install_xorgproto($p); //依赖 xorg-macros
            install_libXpm($p); //依赖 xorg-macros  xorgproto
        }

        install_libgd2($p);
        //GraphicsMagick  http://www.graphicsmagick.org/index.html
        install_GraphicsMagick($p);
    }

    if (0) {
        install_openssl_v3($p);
        install_openssl_v3_quic($p);
        install_libedit($p);


        install_libdeflate($p); //依赖 libzip zlib gzip
        install_bzip2_dev_latest($p);


        install_nettle($p); //加密库
        install_jansson($p);
        install_libtasn1($p);
        //install_libexpat($p);
        install_unbound($p); //依赖 libsodium nghttp2 nettle openssl ibtasn1 libexpat
        install_p11_kit($p);
        # TLS/ESNI/ECH/DoT/DoH/  参考文档https://zhuanlan.zhihu.com/p/572101957
        # SSL 比较 https://curl.se/docs/ssl-compared.html
        install_gnutls($p); //依赖 gmp libiconv  libtasn1 libzip  libzstd libbrotli libzlib
        install_boringssl($p);//需要 golang
        install_wolfssl($p);//
        install_libressl($p);//

        //参考 ：HTTP3 and QUIC 有多种实现   curl 使用 http3 参考： https://curl.se/docs/http3.html
        install_nghttp3($p); // 使用 GnuTLS或者wolfss，这样就不用更换openssl版本了 ；
        install_ngtcp2($p); //依赖gnutls nghttp3


        install_quiche($p); // 依赖 boringssl ，需要 rust ；
        install_msh3($p);  //需要安装库 bsd-compat-headers 解决 sys/queue.h 不存在的问题

        install_nghttp2($p);

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
        install_pcre2($p);
        install_pgsql_test($p);
        install_libgomp($p); //压缩算法
        install_libzip_ng($p); //zlib next
    }


    if (0) {
        install_libev($p); //无 pkg-config
    }
    if (0) {
        install_aria2($p); //依赖libuv openssl zlib libxml2 sqlite3 openssl c-ares
        install_socat($p); //依赖 readline openssl
    }

    if (0) {
        //Wasm
        //WebAssembly
        //Docker+Wasm  https://docs.docker.com/desktop/wasm/
    }

    //排版相关
    if (0) {
        install_graphviz($p); //依赖git libwbp freetype
        install_TeX($p); //排版系统
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
    if (0) {
        install_rav1e($p);
        install_aom($p);
        install_av1($p);
        install_libvpx($p);
        install_libopus($p);
        install_libx264($p);
        install_libx265($p);
        install_mp3lame($p);
        install_ffmpeg($p);
        // install_librabbitmq($p);
        install_opencv_contrib($p);
        install_opencv($p); //构建过程中，会去github.com 下载 ippicv xfeatures2d wechat_qrcode unifont  face_landmark_model.dat
        //依赖ffmpeg zlib ninja zlib libjpeg libwebp freetype
    }
    if (0) {
        // 改善iptables/netfilter的规模瓶颈，提高Linux内核协议栈IO性能
        // DPDK让用户态程序直接处理网络流，bypass掉内核，使用独立的CPU专门干这个事。

        // Berkeley Packet Filter (eBPF)
        // XDP让灌入网卡的eBPF程序直接处理网络流，bypass掉内核，使用网卡NPU专门干这个事。
        // XDP的全称是： eXpress Data Path

        //  XDP 是Linux 内核中提供高性能、可编程的网络数据包处理框架。
        //  eBPF/XDP

        install_dpdk($p); //ninja
        install_xdp($p);  //依赖 llvm bpftool
        install_ovs($p);  //依赖 openssl python3  ; 网络优化以来 dpdk
        install_ovn($p);
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

    if (0) {
        //apk add ninja
        //install_ninja($p); //源码编译ninja，alpine 默认没有提供源；默认不安装 //依赖python
        install_depot_tools($p); //依赖python
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

        //gdb bin/swoole-cli
        //set args -m
        //run


        //下载 boringssl 镜像地址 https://source.codeaurora.org/quic/lc

        //Vtune Threading Profiler是线程性能检测工具 , 分析负载平衡、同步开销过大等线程相关的性能问题

        //Perfdump 工具 Perfdump 是一个系统软件,当系统崩溃时,会调用它生成错误信息,供软件开发人员分析用


        // 动态链接库和静态链接库 https://www.cnblogs.com/Blog-c/p/7811190.html
        // .la 为libtool生成的共享库，其实是个配置文档。可以用file或者vim查看。
        // .ko 文件是Linux内核使用的动态链接文件后缀，属于模块文件，用在Linux系统启动时加载内核模块
    }

    if (0) {
        install_grpc($p); //use protobuf  https://github.com/grpc/grpc.git
        install_thrift($p); //https://thrift.apache.org/
    }
    if (0) {
        //申明式  和 命令式

         //一个为异构并行计算平台编写程序的工业标准  https://www.intel.com/content/www/us/en/docs/programmable/683846/22-1/opencl-library.html
        install_opencl($p); //OpenCL全称为Open Computing Language（开放计算语言） OpenCL不但支持数据并行，还支持任务并行
        //用于共享内存并行系统的多处理器程序设

        //Openmp和thread都是共享一个进程内存的并行，openmp最显著的特点是命令式(directive-based)语言
        //install_openmp($p);

        //并发编程：SIMD 介绍  https://zhuanlan.zhihu.com/p/416172020
    }
}
