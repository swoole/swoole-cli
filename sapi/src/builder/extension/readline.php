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

        # 获得行号
        grep -n '#ifdef COMPILE_DL_READLINE' ext/readline/readline_cli.c | cut -d ':' -f 1
        awk '/#ifdef COMPILE_DL_READLINE/ { print NR }' ext/readline/readline_cli.c

        # 获得待删除 区间
        START_LINE_NUM=$(sed  -n "/#ifdef COMPILE_DL_READLINE/=" ext/readline/readline_cli.c)
        START_LINE_NUM=$((\$START_LINE_NUM - 1))
        END_LINE_NUM=$(sed  -n "/PHP_MINIT_FUNCTION(cli_readline)/=" ext/readline/readline_cli.c)
        REPLACE_LINE_NUM=$((\$END_LINE_NUM - 3))
        END_LINE_NUM=$((\$END_LINE_NUM - 4))

        sed "\${REPLACE_LINE_NUM}/^.*$/#define GET_SHELL_CB(cb) (cb) = php_cli_get_shell_callbacks()/" ext/readline/readline_cli.c

        sed -i.backup "\${START_LINE_NUM},\${END_LINE_NUM}d" ext/readline/readline_cli.c

EOF;

        return $cmd;
    });
};
