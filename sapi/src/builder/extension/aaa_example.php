<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // 添加没有依赖库的扩展，参考例子： src/builder/extension/redis.php

    //扩展依赖的静态链接库
    $depends = ['curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2'];

    //PHP 构建选项
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares ';
    $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    $options .= ' --with-nghttp2-dir=' . NGHTTP2_PREFIX;

    //动态配置 依赖的的静态链接库
    if ($p->getInputOption('with-swoole-pgsql')) {
        $options .= ' --enable-swoole-pgsql';
        $depends[] = 'pgsql';
    }

    $rootDir = $p->getRootDir();

    //默认这个名称应该和扩展名称一致、和本文件名称一致 ；
    $ext = (new Extension('aaa_example'))
        /*

        //设置别名 ； 定义的名字和扩展名字不一致时，需要设置别名为扩展名称
        ->withAliasName('swoole')

        */

        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->withManual('https://wiki.swoole.com/#/')
        /*

        //明确申明 使用源地址下载
        ->withDownloadWithOriginURL()

        //明确声明，每次都执行下载，不使用已下载的缓存文件
        ->withAutoUpdateFile()

        //明确申明 不使用代理
        //例子： 下载扩展方式三，把下载地址更换为 https://gitee.com/swoole/swoole.git ，不使用代理下载
        ->withHttpProxy(false)

        //明确申明 不使用缓存目录
        //例子： ext/swoole (每次都解压全新源代码到此目录）
        //例子： 下载扩展方式四 ，明确不使用  pool/ext/swoole-submodule.tar.gz 缓存文件；每一次都会拉取最新的代码
        ->withBuildCached(false)

        */


        # 下载扩展源代码 四种方式:  （任选一种即可，备注：PHP源码包含的扩展不需要下载）


        /* 下载扩展源代码方式一 start */
        // main分支 默认是这种方式 （去pecl.php.net 站点下载）
        //完整的下载地址 "https://pecl.php.net/get/swoole-5.0.3.tgz";
        //https://pecl.php.net 站点查看 版本号
        ->withPeclVersion('5.0.3')
        /* 下载扩展源代码方式一 end  */


        /* 下载扩展源代码方式二 start */
        ->withUrl('https://github.com/swoole/swoole-src/archive/refs/tags/v5.0.3.tar.gz')
        ->withFile('swoole-v5.0.3.tar.gz')
        /* 下载扩展源代码方式二 end   */


        /* 下载扩展源代码方式三 start */
        # 使用 git clone 下载，然后打包为 后缀名为 tar.gz 的文件
        ->withFile('swoole-latest.tar.gz')
        ->withDownloadScript(
            'swoole-src', # 待打包目录名称
            <<<EOF
            git clone -b master --depth=1 https://github.com/swoole/swoole-src.git
            # mirror
            # git clone -b master --depth=1 https://gitee.com/swoole/swoole.git
EOF
        )
        /* 下载扩展源代码方式三 end   */


        /* 下载扩展源代码方式四 start   */
        //扩展作为本项目的一个模块
        //使用时把 sapi/swoole 子模块源码打包为 pool/ext/swoole-git-submodule.tar.gz
        ->withFile('swoole-submodule.tar.gz')
        ->withDownloadScript(
            'swoole', # 待打包目录名称
            <<<EOF
            cd {$rootDir}/sapi
EOF
        )
        /* 下载扩展源代码方式四 end     */


        //swoole 依赖的扩展
        ->withDependentExtensions('curl', 'openssl', 'sockets', 'mysqlnd', 'pdo');


    # swoole 依赖的静态链接库


    /* 依赖的静态链接库 写法一 start   */
    $ext->withDependentLibraries('curl', 'openssl', 'cares', 'zlib', 'brotli', 'nghttp2');
    /* 依赖的静态链接库 写法一 end     */


    /* 依赖的静态链接库 写法二 start   */
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    /* 依赖的静态链接库 写法二 end     */


    $p->addExtension($ext);


    // 扩展钩子 写法
    $p->withBeforeConfigureScript('swoole', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $cmd = <<<EOF
        cd {$workdir}
        # 构建之前对 swoole 源码做一些特别处理
        # 比如加载一个补丁等
        # 比如修改 swoole 源码的构建文件
        # 实例 参考 protobuf.php 扩展配置 src/builder/extension/protobuf.php
EOF;

        return $cmd;
    });

    //导入环境变量

    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');

    $libiconv_prefix = ICONV_PREFIX;
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libiconv_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libiconv_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -liconv');
};
