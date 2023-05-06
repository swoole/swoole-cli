<?php

$src = realpath(__DIR__ . '/../../../');
$name = "php-cli-v" . PHP_VERSION . "-cygwin-x64";

$dst = "/tmp/{$name}";
if (!is_dir($dst)) {
    mkdir($dst, 0777, true);
}

$match = "";
$patten = "#\s+(\S+)\s+\=\>\s+(\S+)\s+\(0x[a-f0-9]+\)#i";
$list = `ldd {$src}/bin/php-cli.exe`;

preg_match_all($patten, $list, $match);

if (!is_dir($dst . '/bin')) {
    mkdir($dst . '/bin');
}
if (!is_dir($dst . '/etc')) {
    mkdir($dst . '/etc');
}

foreach ($match[2] as $file) {
    if (str_starts_with($file, '/cygdrive/')) {
        continue;
    }
    echo $file . "\n";
    copy($file, $dst . "/bin/" . basename($file));
}


echo `chmod a+x {$src}/bin/php-cli.exe`;
copy("{$src}/bin/php-cli.exe", $dst . "/bin/php-cli.exe");
if (is_file("{$src}/bin/LICENSE")) {
    copy("{$src}/bin/LICENSE", $dst . "/LICENSE");
}

echo `cp -rL /etc/pki/ {$dst}/etc`;
echo "done.\n";


$pack = "cd " . dirname($dst) . " && zip -r {$name}.zip {$name} && cd -";
echo $pack . PHP_EOL;
echo `$pack`;

$move = "mv {$dst}.zip {$src}";
echo $move . PHP_EOL;
echo `$move`;

echo "clean..." . PHP_EOL;
echo `rm -rf {$dst}`;
