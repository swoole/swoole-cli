<?php
/**
 * @var $this SwooleCli\Preprocessor
 */

echo "License\n---------------------------------------------------------\n";
echo "musl-libc: http://git.musl-libc.org/cgit/musl/tree/COPYRIGHT\n";

foreach ($this->libraryList as $item) {
    echo "{$item->name}: {$item->license}\n";
}

$php_license = 'https://github.com/php/php-src/blob/master/LICENSE';
echo "php: $php_license\n";

foreach ($this->extensionList as $item) {
    if (!$item->license) {
        continue;
    }
    echo "php-ext-{$item->name}: {$item->license}\n";
}
