<?php
foreach ([
    'none' => [
        'compress' => Phar::NONE,
    ],
    'alias' => [
        'compress' => Phar::NONE,
        'alias'    => true,
    ],
    'gz' => [
        'compress' => Phar::GZ,
    ],
    'bz2' => [
        'compress' => Phar::BZ2,
    ]
    ] as $name => $options) {
    $fileName = __DIR__ . '/test-' . $name . '.phar';
    if (is_file($fileName)) {
        unlink($fileName);
    }
    $phar = new \Phar($fileName);
    $phar->addFile(__DIR__ . '/test-phar.php', 'test.php');
    if ($options['alias'] ?? false) {
        $phar->setStub(<<<PHP
        #!/usr/bin/env php
        <?php

        Phar::mapPhar('swoole-cli.phar');
        require 'phar://swoole-cli.phar/test.php';

        __HALT_COMPILER();
        PHP);
    } else {
        $phar->setDefaultStub('test.php');
    }
    if (Phar::NONE !== $options['compress']) {
        $phar->compressFiles($options['compress']);
    }
}
