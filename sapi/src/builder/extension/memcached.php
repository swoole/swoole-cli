<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = [];
    $options[] = '--enable-memcached ';
    $options[] = '--with-libmemcached-dir=' . LIBMEMCACHED_AWESOME_PREFIX;
    $options[] = '--with-zlib-dir=' . ZLIB_PREFIX;
    $options[] = '--enable-memcached-session';
    $options[] = '--enable-memcached-json';

    $options[] = '--disable-memcached-protocol';
    $options[] = '--disable-memcached-sasl';
    $options[] = '--disable-memcached-igbinary';
    $options[] = '--disable-memcached-msgpack';
    //$options[] = '--enable-memcached-protocol'; //依赖libevent 库
    //$options[] = '--enable-memcached-igbinary';
    //$options[] = '--enable-memcached-msgpack';
    //$options[] = '--enable-memcached-sasl';


    $dependentLibraries = ['libmemcached']; // libmemcached 静态编译失败 ,使用 libmemcached-awesome 代替
    $dependentLibraries = ['libmemcached_awesome', 'zlib'];
    $dependentExtensions = ['session']; //依赖 session json 扩展

    $ext = (new Extension('memcached'))
        ->withOptions(implode(' ', $options))
        ->withBuildCached(false)
        ->withLicense('https://php.net/license/3_01.txt', Extension::LICENSE_PHP)
        ->withHomePage('http://pecl.php.net/package/memcached')
        ->withManual('https://github.com/php-memcached-dev/php-memcached')
        ->withPeclVersion('3.2.0')
        ->withDependentLibraries(... $dependentLibraries)
        ->withDependentExtensions(... $dependentExtensions);

    $p->addExtension($ext);

};
