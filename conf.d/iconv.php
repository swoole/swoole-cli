<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . ICONV_PREFIX . '/include');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_LDLAGS', '$SWOOLE_CLI_EXTRA_LDLAGS -L' . ICONV_PREFIX . '/lib');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_LIBS', '$SWOOLE_CLI_EXTRA_LIBS -liconv');
    $p->addExtension((new Extension('iconv'))->withOptions('--with-iconv=' . ICONV_PREFIX)->depends('libiconv'));
};
