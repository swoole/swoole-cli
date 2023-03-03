<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // curl/imagemagick 对 brotli 静态库的支持有点问题，暂时关闭
    $options = '--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares';
    if ($p->getInputOption('with-brotli')) {
        $brotli_prefix = BROTLI_PREFIX;
        $p->addLibrary(
            (new Library('brotli'))
                ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
                ->withFile('brotli-1.0.9.tar.gz')
                ->withPrefix(BROTLI_PREFIX)
                ->withBuildScript(
                    <<<EOF
            cmake . -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX={$brotli_prefix} \
            -DBROTLI_SHARED_LIBS=OFF \
            -DBROTLI_STATIC_LIBS=ON \
            -DBROTLI_DISABLE_TESTS=ON \
            -DBROTLI_BUNDLED_MODE=OFF \
            && \
            cmake --build . --config Release --target install
EOF
                )
                ->withScriptAfterInstall(
                    <<<EOF
            rm -rf {$brotli_prefix}/lib/*.so.*
            rm -rf {$brotli_prefix}/lib/*.so
            rm -rf {$brotli_prefix}/lib/*.dylib
            cp  -f {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotli.a
            mv     {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotlicommon.a
            mv     {$brotli_prefix}/lib/libbrotlienc-static.a    {$brotli_prefix}/lib/libbrotlienc.a
            mv     {$brotli_prefix}/lib/libbrotlidec-static.a    {$brotli_prefix}/lib/libbrotlidec.a
EOF
                )
                ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
                ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
                ->withHomePage('https://github.com/google/brotli')
        );
        $options .= ' --with-brotli-dir=' . BROTLI_PREFIX;
    }
    $pgsql_prefix= PGSQL_PREFIX ;
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            ->withPrefix($pgsql_prefix)
            ->withBuildScript(
                <<<'EOF'
            ./configure --help
            
            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile
            # 替换指定行内容
            sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile
            package_names="openssl zlib icu-uc icu-io icu-i18n readline libxml-2.0  libxslt libzstd liblz4"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static $package_names )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static $package_names )" \
            LIBS="$(pkg-config      --libs-only-l   --static $package_names )" \
EOF . PHP_EOL . <<<EOF
            ./configure  \
            --prefix={$pgsql_prefix} \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt 
EOF
                .   <<<'EOF'
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
            make -C src/include install 

            make -C  src/bin/pg_config install

            make -C  src/common -j $cpu_nums all 
            make -C  src/common install 

            make -C  src/port -j $cpu_nums all 
            make -C  src/port install 
   
            make -C  src/backend/libpq -j $cpu_nums all 
            make -C  src/backend/libpq install 
   
            make -C src/interfaces/ecpg   -j $cpu_nums all-pgtypeslib-recurse all-ecpglib-recurse all-compatlib-recurse all-preproc-recurse
            make -C src/interfaces/ecpg  install-pgtypeslib-recurse install-ecpglib-recurse install-compatlib-recurse install-preproc-recurse
 
            # 静态编译 src/interfaces/libpq/Makefile  有静态配置  参考： all-static-lib
            
            make -C src/interfaces/libpq  -j $cpu_nums # soname=true
            make -C src/interfaces/libpq  install 
EOF
            )
            ->withPkgName('libpq')
            ->withScriptAfterInstall(
                <<<EOF
             rm -rf {$pgsql_prefix}/lib/*.so.*
             rm -rf {$pgsql_prefix}/lib/*.so
EOF
            )
    );
    $p->addExtension(
        (new Extension('swoole'))
        ->withOptions($options)
        ->withLicense('https://github.com/swoole/swoole-src/blob/master/LICENSE', Extension::LICENSE_APACHE2)
        ->withHomePage('https://github.com/swoole/swoole-src')
        ->depends('curl', 'openssl', 'cares', 'zlib')
    );
};
