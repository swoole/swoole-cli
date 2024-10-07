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

        cd {$php_src}/

        FOUND_DL_READLINE=$(grep -c '#ifdef COMPILE_DL_READLINE' ext/readline/readline_cli.c)

        if test $[FOUND_DL_READLINE] -gt 0 ; then
            # 获得待删除 区间
            START_LINE_NUM=$(sed  -n "/#ifdef COMPILE_DL_READLINE/=" ext/readline/readline_cli.c)
            START_LINE_NUM=$((\$START_LINE_NUM - 1))
            END_LINE_NUM=$(sed  -n "/PHP_MINIT_FUNCTION(cli_readline)/=" ext/readline/readline_cli.c)
            END_LINE_NUM=$((\$END_LINE_NUM - 5))

            sed -i.backup "\${START_LINE_NUM},\${END_LINE_NUM}d" ext/readline/readline_cli.c

            REPLACE_LINE_NUM=$(sed  -n "/#define GET_SHELL_CB(cb) (cb) = php_cli_get_shell_callbacks()/=" ext/readline/readline_cli.c)
            REPLACE_LINE_NUM=$((\$REPLACE_LINE_NUM + 1))

            sed -i.backup "\${REPLACE_LINE_NUM} s/.*/  /" ext/readline/readline_cli.c

        fi
EOF;

        return $cmd;
    });
};
