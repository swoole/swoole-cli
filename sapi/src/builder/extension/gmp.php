<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gmp'))
            ->withHomePage('https://www.php.net/gmp')
            ->withOptions('--with-gmp=' . GMP_PREFIX)
            // gmp 扩展本身只用 GMP；额外声明依赖 mpfr 是为了把 MPFR（多精度浮点库，
            // 依赖 GMP）一并纳入构建，并在编译 libphp.a 时把 libmpfr.a 链接进去
            ->withDependentLibraries('gmp', 'mpfr')
    );
};
