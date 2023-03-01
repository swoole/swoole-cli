<?php
$phar = new \Phar(__DIR__ . '/test.phar');
$phar->buildFromDirectory(__DIR__);
$phar->setDefaultStub('test.php', 'test.php');
