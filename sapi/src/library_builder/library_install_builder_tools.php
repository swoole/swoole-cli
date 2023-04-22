<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

// ================================================================================================
// Library
// ================================================================================================


/*
 * 使用 qemu 模拟和调试不同架构的二进制程序  运行交叉编译测试
 * amd64/x86 linux机器上运行程序 ARM程序
 * 一是系统模式 qemu-system，作为虚拟机监管器，模拟全系统，利用其他VMM(Xen, KVM, etc)来使用硬件提供的虚拟化支持，创建接近于主机性能的虚拟机；
 * 二是用户模式 qemu-user，作为用户态模拟器，利用动态代码翻译机制来执行不同于主机架构的代码
 *
 * qemu-user-binfmt
 *
 */
function install_qemu(Preprocessor $p): void
{
    $qemu_prefix = '/usr/qemu';
    $p->addLibrary(
        (new Library('qemu'))
            ->withHomePage('http://www.qemu.org/')
            ->withLicense('https://github.com/qemu/qemu/blob/master/COPYING.LIB', Library::LICENSE_GPL)
            ->withUrl('https://download.qemu.org/qemu-7.2.0.tar.xz')
            ->withManual('https://www.qemu.org/docs/master/')
            ->withUntarArchiveCommand('xz')
            ->withPrefix($qemu_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            set -eux
            pwd
            ls -lh .

            mkdir build
            cd build
            ../configure
            make
EOF
            )
            ->withBinPath($qemu_prefix . '/bin/')
    );
}


function install_ninja(Preprocessor $p)
{
    $ninja_prefix = '/usr/ninja';
    $p->addLibrary(
        $lib = (new Library('ninja'))
            ->withHomePage('https://ninja-build.org/')
            //->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-linux.zip')
            ->withUrl('https://github.com/ninja-build/ninja/archive/refs/tags/v1.11.1.tar.gz')
            ->withFile('ninja-build-v1.11.1.tar.gz')
            ->withLicense('https://github.com/ninja-build/ninja/blob/master/COPYING', Library::LICENSE_APACHE2)
            ->withManual('https://ninja-build.org/manual.html')
            ->withManual('https://github.com/ninja-build/ninja/wiki')
            ->withPrefix($ninja_prefix)
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            //->withUntarArchiveCommand('unzip')
            ->withBuildScript(
                "
                # apk add ninja

                #  ./configure.py --bootstrap

                cmake -Bbuild-cmake
                cmake --build build-cmake
                mkdir -p {$ninja_prefix}/bin/
                cp build-cmake/ninja  {$ninja_prefix}/bin/
                return 0 ;
                ./configure.py --bootstrap
                mkdir -p /usr/ninja/bin/
                cp ninja /usr/ninja/bin/
                return 0 ;
            "
            )
            ->withBinPath($ninja_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
            ->withSkipBuildInstall()
    );

    if ($p->getOsType() == 'macos') {
        $lib->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-mac.zip');
    }
}


function install_musl(Preprocessor $p): void
{
    $workDir = $p->getWorkDir();
    $musl_libc_prefix= MUSL_LIBC_PREFIX;
    $p->addLibrary(
        (new Library('musl_libc'))
            ->withHomePage('https://musl.libc.org/')
            ->withLicense('https://git.musl-libc.org/cgit/musl/tree/COPYRIGHT', Library::LICENSE_MIT)
            ->withManual('https://musl.libc.org/manual.html')
            ->withUrl('https://musl.libc.org/releases/musl-1.2.3.tar.gz')
            ->disableDownloadWithMirrorURL()
            ->withPrefix($musl_libc_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($musl_libc_prefix)
            ->withBuildScript(
                <<<EOF
             // sudo apt install git build-essential
             ./configure --prefix={$musl_libc_prefix} \
             --disable-shared

EOF
            )
            ->withBinPath('$HOME/.cargo/bin')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}
function install_musl_cross_make(Preprocessor $p): void
{
    $workDir = $p->getWorkDir();
    $musl_cross_make_prefix= MUSL_CROSS_MAKE_PREFIX;
    $p->addLibrary(
        (new Library('musl_cross_make'))
            ->withHomePage('https://musl.libc.org/')
            ->withLicense('https://git.musl-libc.org/cgit/musl/tree/COPYRIGHT', Library::LICENSE_MIT)
            ->withManual('https://musl.libc.org/manual.html')
            ->withUrl('https://github.com/richfelker/musl-cross-make/archive/refs/tags/v0.9.9.tar.gz')
            ->withFile('musl-cross-make-v0.9.9.tar.gz')
            ->withDownloadScript(
                'musl-cross-make',
                <<<EOF
            git clone -b master --depth=1 https://github.com/richfelker/musl-cross-make.git
EOF
            )
            ->disableDownloadWithMirrorURL()
            ->withPrefix($musl_cross_make_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($musl_cross_make_prefix)
            ->withBuildScript(
                <<<EOF
             cp config.mak.dist config.mak

            cat >> config.mak <<_EOF_
TARGET = x86_64-linux-musl
GCC_VER = 11.2.0
COMMON_CONFIG += CFLAGS="-g0 -O3" CXXFLAGS="-g0 -O3" LDFLAGS="-s"
GCC_CONFIG += --enable-default-pie --enable-static-pie
_EOF_


EOF
            )
            ->withBinPath($musl_cross_make_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_rust(Preprocessor $p): void
{
    $workDir = $p->getWorkDir();
    $p->addLibrary(
        (new Library('rust_lang'))
            ->withHomePage('https://www.rust-lang.org')
            ->withLicense('https://github.com/rust-lang/rust/blob/master/LICENSE-APACHE', Library::LICENSE_APACHE2)
            ->withUrl('https://sh.rustup.rs')
            ->withManual('https://www.rust-lang.org/tools/install')
            ->withFile('rustup.sh')
            ->disableDownloadWithMirrorURL()
            ->withUntarArchiveCommand('mv')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF

            ls -lh
             # curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh
            RUSTUP_DIST_SERVER="https://mirrors.tuna.tsinghua.edu.cn/rustup rustup install stable " \
            RUSTUP_UPDATE_ROOT="https://mirrors.tuna.tsinghua.edu.cn/rustup/rustup" \
            curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh
            exit 0
            RUSTUP_HOME=/root/.rustup
            CARGO_HOME=/root/.cargo
            /root/.rustup
            /root/.cargo
            /root/.cargo/bin
            source "\$HOME/.cargo/env"

EOF
            )
            ->withBinPath('$HOME/.cargo/bin')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_nodejs(Preprocessor $p): void
{
    $workDir = $p->getWorkDir();
    $nodejs_prefix = NODEJS_PREFIX;
    $p->addLibrary(
        (new Library('nodejs_lang'))
            ->withHomePage('https://www.rust-lang.org')
            ->withLicense('https://github.com/rust-lang/rust/blob/master/LICENSE-APACHE', Library::LICENSE_SPEC)
            ->withUrl('https://nodejs.org/dist/v18.15.0/node-v18.15.0-linux-x64.tar.xz')
            ->withManual('https://nodejs.org/en/docs')
            ->withFile('node-v18.15.0-linux-x64.tar.xz')
            ->disableDownloadWithMirrorURL()
            ->withUntarArchiveCommand('mv')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($nodejs_prefix)
            ->withBuildScript(
                <<<EOF

            ls -lh
            xz -d node-v18.15.0-linux-x64.tar.xz
            tar -xvf node-v18.15.0-linux-x64.tar
            mv node-v18.15.0-linux-x64 /usr/nodejs

EOF
            )
            ->withBinPath($nodejs_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_golang(Preprocessor $p): void
{
    $golang_prefix = GOLANG_PREFIX;
    $workDir = $p->getWorkDir();
    $p->addLibrary(
        (new Library('golang'))
            ->withHomePage('https://golang.org')
            ->withLicense('https://github.com/golang/go/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://golang.google.cn/dl/go1.20.2.linux-amd64.tar.gz')
            ->withManual('https://golang.google.cn/doc/')
            ->withFile('go1.20.2.linux-amd64.tar.gz')
            ->disableDownloadWithMirrorURL()
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($golang_prefix)
            ->withBuildScript(
                <<<EOF

            ls -lh
            mkdir -p {$golang_prefix}
            cp -rf . {$golang_prefix}
            chmod a+x {$golang_prefix}/bin

EOF
            )
            ->withBinPath($golang_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_depot_tools(Preprocessor $p): void
{
    $depot_tools_prefix = DEPOT_TOOLS_PREFIX;
    $workDir = $p->getWorkDir();
    $p->addLibrary(
        (new Library('depot_tools'))
            ->withHomePage('https://chromium.googlesource.com/chromium/tools/depot_tools')
            ->withLicense(
                'https://chromium.googlesource.com/chromium/tools/depot_tools.git/+/refs/heads/main/LICENSE',
                Library::LICENSE_SPEC
            )
            ->withUrl('https://chromium.googlesource.com/chromium/tools/depot_tools')
            ->withManual(
                'https://commondatastorage.googleapis.com/chrome-infra-docs/flat/depot_tools/docs/html/depot_tools_tutorial.html#_setting_up'
            )
            ->withFile('depot_tools.tar.gz')
            ->disableDownloadWithMirrorURL()
            ->withDownloadScript(
                'depot_tools',
                <<<EOF
                git clone -b main  --single-branch  --depth=1  https://chromium.googlesource.com/chromium/tools/depot_tools
EOF
            )
            //->withUntarArchiveCommand('cp')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($depot_tools_prefix)
            ->withBuildScript(
                "
                mkdir -p $depot_tools_prefix
                cd ..
                cp -rf depot_tools/* $depot_tools_prefix
            "
            )
            ->withBinPath($depot_tools_prefix)
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_gn_test(Preprocessor $p): void
{
    $file = '';
    if ($p->getOsType() == 'linux') {
        $file = 'https://chrome-infra-packages.appspot.com/dl/gn/gn/linux-amd64/+/latest';
    }
    if ($p->getOsType() == 'macos') {
        $file = 'https:chrome-infra-packages.appspot.com/dl/gn/gn/mac-amd64/+/latest';
    }

    $gn_prefix = '/usr/gn';
    $p->addLibrary(
        (new Library('gn'))
            ->withHomePage('https://gn.googlesource.com/gn')
            ->withLicense('https://gn.googlesource.com/gn/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
            ->withUrl($file)
            ->withFile('gn-latest.zip')
            ->withManual('https://gn.googlesource.com/gn/')
            ->withUntarArchiveCommand('unzip')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($gn_prefix)
            ->withBuildScript(
                "
               chmod a+x gn
               mkdir -p $gn_prefix/bin/
               cp -rf gn $gn_prefix/bin/
            "
            )
            ->withBinPath($gn_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_gn(Preprocessor $p): void
{
    $gn_prefix = '/usr/gn';
    $p->addLibrary(
        (new Library('gn'))
            ->withHomePage('https://gn.googlesource.com/gn')
            ->withLicense('https://gn.googlesource.com/gn/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
            //->withUrl('https://gn.googlesource.com/gn')
            //->withUrl('https://chrome-infra-packages.appspot.com/dl/gn/gn/linux-amd64/+/latest')
            // ->withUrl('https:chrome-infra-packages.appspot.com/dl/gn/gn/mac-amd64/+/latest')
            ->withUrl('')
            ->withSkipDownload()
            ->withManual('https://gn.googlesource.com/gn/')
            ->withUntarArchiveCommand('cp')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($gn_prefix)
            ->withBuildScript(
                "
                cd gn
                ls -lha .

                python3 build/gen.py --allow-warning
                ninja -C out
                exit 0
                mkdir -p $gn_prefix
                cp -rf gn/* $gn_prefix
            "
            )
            ->withBinPath($gn_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}


function install_bazel(Preprocessor $p)
{
    /**
     * alpine 无法直接用 bazel ，原因： alpine 使用 musl ， Bazel 使用 glibc
     *
     * 需要把alpine 切换到 test 版本
     *  https://pkgs.alpinelinux.org/package/edge/testing/x86_64/bazel4
     */
    $bazel_prefix = '/usr/bazel';
    $p->addLibrary(
        (new Library('bazel'))
            ->withHomePage('https://bazel.build')
            ->withLicense('https://github.com/bazelbuild/bazel/blob/master/LICENSE', Library::LICENSE_APACHE2)
            //->withUrl('https://github.com/bazelbuild/bazel/releases/download/6.0.0/bazel-6.0.0-linux-x86_64')
            //->withUrl('https://github.com/bazelbuild/bazel/archive/refs/tags/6.0.0.tar.gz')
            //->withFile('bazel-6.0.0.tar.gz')
            ->withUrl(
                'https://github.com/bazelbuild/bazel/releases/download/7.0.0-pre.20230215.2/bazel-7.0.0-pre.20230215.2-dist.zip'
            )
            ->withUntarArchiveCommand('unzip')
            ->withManual('https://bazel.build/install')
            ->withManual('https://bazel.build/install/compile-source')
            ->withPrefix($bazel_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($bazel_prefix)
            ->withBuildScript(
                '
                # apk add openjdk13-jdk bash zip

                # 会自动安装 libx11  libtasn1  p11_kit gnutls

                env EXTRA_BAZEL_ARGS="--tool_java_runtime_version=local_jdk" bash ./compile.sh


                exit 0
                export PATH=$SYSTEM_ORIGIN_PATH
                export PKG_CONFIG_PATH=$SYSTEM_ORIGIN_PKG_CONFIG_PATH
                # 执行构建前

                # 执行构建

                # 执行构建后
                export PATH=$SWOOLE_CLI_PATH
                export PKG_CONFIG_PATH=$SWOOLE_CLI_PKG_CONFIG_PATH


                mv bazel /usr/bazel/bin/
                chmod a+x /usr/bazel/bin/bazel
                /usr/bazel/bin/bazel -h

               '
            )
            ->withBinPath($bazel_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disablePkgName()
            ->disableDefaultLdflags()
    );
}
