<?php
echo 'file=', __FILE__, PHP_EOL;

echo 'filesize=', $filesize = filesize(__FILE__), PHP_EOL;

echo 'content1.length=', strlen(file_get_contents(__FILE__)), PHP_EOL;

$fp = fopen(__FILE__, 'r');

fseek($fp, 0, SEEK_SET);
echo 'tell=', ftell($fp), PHP_EOL;
echo 'content2.length=', strlen(fread($fp, 4096)), PHP_EOL;

fseek($fp, 0, SEEK_SET);
echo 'tell=', ftell($fp), PHP_EOL;
fseek($fp, 1, SEEK_CUR);
echo 'tell=', ftell($fp), PHP_EOL;
echo 'second char=' . fread($fp, 1), PHP_EOL;

fseek($fp, 1, SEEK_SET);
echo 'tell=', ftell($fp), PHP_EOL;
echo 'second char=' . fread($fp, 1), PHP_EOL;

fseek($fp, -1, SEEK_END);
echo 'tell=', ftell($fp), PHP_EOL;
echo 'last char=' . fread($fp, 1), PHP_EOL;

fclose($fp);