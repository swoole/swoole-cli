<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->withPeclVersion('1.5.4')
            ->withDownloadScript(
                'xlswriter',
                <<<EOF
                    test -d php-ext-xlswriter && rm -rf php-ext-xlswriter

                    # git clone -b v1.5.4 --depth=1 --recursive https://github.com/viest/php-ext-xlswriter.git
                    git clone -b feature_openssl_dir --depth=1 --recursive https://github.com/jingjingxyk/php-ext-xlswriter.git

                    # git clone -b main_static_built --depth=1 --recursive https://github.com/viest/php-ext-xlswriter.git
                    mv php-ext-xlswriter xlswriter
EOF
            )
            ->withOptions(' --with-xlswriter --enable-reader --with-openssl-md5=yes')
    );
    //--with-openssl-md5 使用Openssl MD5
    //--with-bundled-md5 使用内置MD5
    $p->setExtCallback('xlswriter', function (Preprocessor $p) {
        $work_dir=$p->getWorkDir();
        $cmd=<<<EOF
          cd {$work_dir}/ext/xlswriter
          if [[ ! -f config.m4.backup ]] ;then
                # 替换为空行
                sed -i.backup "42s/.*//" config.m4
                sed -i.backup "187s/.*//" config.m4
                # 删除行
                # sed -i '42,187d' config.m4
          fi
          cd {$work_dir}/
EOF;
        //停用 hook
        $cmd='';
        return $cmd;
    });
};
