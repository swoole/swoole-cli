<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->setExportVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . BZIP2_PREFIX . '/include');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_LDLAGS', '$SWOOLE_CLI_EXTRA_LDLAGS -L' . BZIP2_PREFIX . '/lib');
    $p->setExportVarable('SWOOLE_CLI_EXTRA_LIBS', '$SWOOLE_CLI_EXTRA_LIBS -lbz2');
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=' . BZIP2_PREFIX)->depends('bzip2'));
};
