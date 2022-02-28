<?php
$dir = dirname(__DIR__);
$dst = "/cygdrive/d/swoole-cli-v".SWOOLE_VERSION."-cygwin64/bin";
if (!is_dir($dst)) {
    mkdir($dst, 0777, true);
}

$match = "";
$patten = "#\s+(\S+)\s+\=\>\s+(\S+)\s+\(0x[a-f0-9]+\)#i";
$list = `ldd {$dir}/bin/swoole-cli.exe`;

preg_match_all($patten, $list, $match);

foreach($match[2] as $file) {
    if (str_starts_with($file, '/cygdrive/')) {
        continue;
    }
    echo $file."\n";
    copy($file, $dst."/".basename($file));
}


echo `chmod a+x {$dir}/bin/swoole-cli.exe`;
copy("{$dir}/bin/swoole-cli.exe", $dst."/swoole-cli.exe");

