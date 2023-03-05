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
            ->withScriptBeforeConfigure(
                <<<EOF


EOF
            )
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
    $ninja_prefix = '/usr/ninja' ;
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


function install_depot_tools(Preprocessor $p): void
{
    $depot_tools_prefix = '/usr/depot_tools';
    $p->addLibrary(
        (new Library('depot_tools'))
            ->withHomePage('https://chromium.googlesource.com/chromium/tools/depot_tools')
            ->withLicense('https://chromium.googlesource.com/chromium/tools/depot_tools.git/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
            ->withUrl('https://chromium.googlesource.com/chromium/tools/depot_tools')
            ->withFile('depot_tools')
            ->withSkipDownload()
            ->withManual('https://commondatastorage.googleapis.com/chrome-infra-docs/flat/depot_tools/docs/html/depot_tools_tutorial.html#_setting_up')
            ->withUntarArchiveCommand('cp')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($depot_tools_prefix)
            ->withBuildScript("
                mkdir -p $depot_tools_prefix
                cp -rf depot_tools/* $depot_tools_prefix
            ")
            ->withBinPath($depot_tools_prefix . '/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_gn_test(Preprocessor $p): void
{
    $file='';
    if ($p->getOsType() == 'linux') {
        $file='https://chrome-infra-packages.appspot.com/dl/gn/gn/linux-amd64/+/latest';
    }
    if ($p->getOsType() == 'macos') {
        $file='https:chrome-infra-packages.appspot.com/dl/gn/gn/mac-amd64/+/latest';
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
            ->withBuildScript("
               chmod a+x gn
               mkdir -p $gn_prefix/bin/
               cp -rf gn $gn_prefix/bin/
            ")
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
            ->withBuildScript("
                cd gn
                ls -lha .

                python3 build/gen.py --allow-warning
                ninja -C out
                exit 0
                mkdir -p $gn_prefix
                cp -rf gn/* $gn_prefix
            ")
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
            ->withUrl('https://github.com/bazelbuild/bazel/releases/download/7.0.0-pre.20230215.2/bazel-7.0.0-pre.20230215.2-dist.zip')
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
