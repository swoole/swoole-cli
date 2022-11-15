<?php
$src = dirname(__DIR__);
$dst = "/cygdrive/d/swoole-cli-v".SWOOLE_VERSION."-cygwin-x64";
if (!is_dir($dst)) {
    mkdir($dst, 0777, true);
}

$match = "";
$patten = "#\s+(\S+)\s+\=\>\s+(\S+)\s+\(0x[a-f0-9]+\)#i";
$list = `ldd {$src}/bin/swoole-cli.exe`;

preg_match_all($patten, $list, $match);

if (!is_dir($dst.'/bin')) {
    mkdir($dst.'/bin');
}
if (!is_dir($dst.'/etc')) {
    mkdir($dst.'/etc');
}

foreach($match[2] as $file) {
    if (str_starts_with($file, '/cygdrive/')) {
        continue;
    }
    echo $file."\n";
    copy($file, $dst."/bin/".basename($file));
}

echo `chmod a+x {$src}/bin/swoole-cli.exe`;
copy("{$src}/bin/swoole-cli.exe", $dst."/bin/swoole-cli.exe");
copy("{$src}/bin/LICENSE", $dst."/LICENSE");
echo `cp -rL /etc/pki/ {$dst}/etc`;
echo "done.\n";