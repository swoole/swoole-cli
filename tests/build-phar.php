<?php
foreach(['none' => Phar::NONE, 'gz' => Phar::GZ, 'bz2' => Phar::BZ2] as $name => $compress) {
    $phar = new \Phar(__DIR__ . '/test-' . $name . '.phar');
    $phar->addFile(__DIR__ . '/test.php', 'test.php');
    $phar->setDefaultStub('test.php');
    if (Phar::NONE !== $compress) {
        $phar->compressFiles($compress);
        $phar->compress($compress);
    }
}
