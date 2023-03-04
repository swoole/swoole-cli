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
            ->withHomePage('https://github.com/xdp-project/xdp-tools.git')
            ->withLicense('https://github.com/xdp-project/xdp-tools/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/xdp-project/xdp-tools/archive/refs/tags/v1.3.1.tar.gz')
            ->withFile('xdp-v1.3.1.tar.gz')
            ->withManual('https://github.com/xdp-project/xdp-tutorial')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                <<<EOF
            apk add llvm bpftool
           
EOF
            )
            ->withConfigure(
                <<<EOF
cd lib/libxdp 
make libxdp


exit 0 
./configure 
exit 0 
EOF
            )
            ->withBinPath($qemu_prefix . '/bin/')
            ->withSkipBuildInstall()
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
            ->withSkipDownload()
            ->withManual('https://commondatastorage.googleapis.com/chrome-infra-docs/flat/depot_tools/docs/html/depot_tools_tutorial.html#_setting_up')
            ->withUntarArchiveCommand('mv')
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
            ->withUntarArchiveCommand('mv')
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
