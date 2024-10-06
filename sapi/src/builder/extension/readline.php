<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('readline'))
            ->withHomePage('https://www.php.net/readline')
            ->withOptions('--with-readline=' . READLINE_PREFIX)
            ->withDependentLibraries('ncurses', 'readline')
    );


    // 扩展钩子
    $p->withBeforeConfigureScript('readline', function (Preprocessor $p) {
        $workDir = $p->getWorkDir();
        $php_src = $p->getPhpSrcDir();
        $cmd = <<<EOF
        cd {$workDir}
        cp -f {$workDir}/sapi/patches/0001-fix-readline-not-work.patch {$php_src}/
        cd {$php_src}/
        git apply --check 0001-fix-readline-not-work.patch
        git apply 0001-fix-readline-not-work.patch
EOF;

        return $cmd;
    });
};
