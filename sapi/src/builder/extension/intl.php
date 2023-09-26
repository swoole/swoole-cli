<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->withExportVariable('ICU_CFLAGS', '$(pkg-config  --cflags --static icu-i18n  icu-io   icu-uc)');
    $p->withExportVariable('ICU_LIBS', '$(pkg-config    --libs   --static icu-i18n  icu-io   icu-uc)');
    $p->addExtension(
        (new Extension('intl'))
            ->withHomePage('https://www.php.net/intl')
            ->withOptions('--enable-intl')
            ->withDependentLibraries('icu')
    );

    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    if ($php_version_id < 7040) {
        $p->setExtHook('intl', function (Preprocessor $p) {
            //解决 ICU 多重定义BUG https://bugs.php.net/bug.php?id=80425
            $cmd = <<<EOF
                cd {$p->getPhpSrcDir()}/
                if [[ ! -f ext/intl/msgformat/msgformat_helpers.cpp.backup ]] ;then
                   sed -i.backup '67,71d' ext/intl/msgformat/msgformat_helpers.cpp
                   echo "ok"
                fi
EOF;

            return $cmd;
        });
    }
};
