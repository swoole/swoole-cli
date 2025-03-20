<?php

declare(strict_types=1);

namespace SwooleCli\tests;


$exts = get_loaded_extensions();


$project_dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');

foreach ($exts as $ext) {
    if (
        ($ext == "Core") ||
        ($ext == "SPL") ||
        ($ext == "session") ||
        ($ext == "standard") ||
        ($ext == "Phar") ||
        ($ext == "Reflection")
    ) {
        continue;
    }

    if (is_file($project_dir . '/sapi/src/tests/' . $ext . 'Test.php')) {
        echo $ext . ' : ';
        echo `{$project_dir}/bin/swoole-cli {$project_dir}/vendor/bin/phpunit {$project_dir}/sapi/src/tests/{$ext}Test.php`;
        echo PHP_EOL;
    }

}
