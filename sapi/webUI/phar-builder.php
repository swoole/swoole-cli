<?php

$phar = new Phar("myapp.phar");
$phar->buildFromDirectory(__DIR__);
$phar->startBuffering();
$phar->setStub($phar->createDefaultStub('bootstrap.php'));
$phar->stopBuffering();
