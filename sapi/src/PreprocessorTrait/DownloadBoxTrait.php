<?php

declare(strict_types=1);

namespace SwooleCli\PreprocessorTrait;

trait DownloadBoxTrait
{
    public string $downloadScriptHeader = <<<'EOF'
#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}
mkdir -p ${__DIR__}/tmp
mkdir -p ${__DIR__}/lib
mkdir -p ${__DIR__}/ext


EOF;

    protected function generateDownloadLinks(): void
    {
        $retry_number = DOWNLOAD_FILE_RETRY_NUMBE;
        $wait_retry = DOWNLOAD_FILE_WAIT_RETRY;
        $connect_timeout = DOWNLOAD_FILE_CONNECTION_TIMEOUT;

        $this->mkdirIfNotExists($this->getRootDir() . '/var/download-box/', 0755, true);

        $download_commands = ['POOL=$(realpath ${__DIR__}/../../pool/)'];
        $download_commands[] = PHP_EOL;

        $extract_files = ['set -x'];
        $extract_files[] = PHP_EOL;

        $download_urls = [];
        foreach ($this->extensionMap as $item) {
            echo $item->name . PHP_EOL;

            if ((!empty($item->peclVersion) || !empty($item->url)) || $item->enableDownloadWithMirrorURL) {
                $download_urls[] = $item->url . PHP_EOL . " out=" . $item->file;

                $download_commands[] = "test -f \${POOL}/ext/{$item->file} || curl  --connect-timeout {$connect_timeout} --retry {$retry_number}  --retry-delay {$wait_retry} -Lo ext/{$item->file} {$item->url}" . PHP_EOL;

                $extract_files[] = "mkdir -p ext/{$item->name}" . PHP_EOL;
                $extract_files[] = "tar --strip-components=1 -C ext/{$item->name} -xf pool/ext/{$item->file}" . PHP_EOL;
            }
        }
        file_put_contents($this->getRootDir() . '/var/download-box/download_extension_urls.txt', implode(PHP_EOL, $download_urls));

        $download_scripts = [];
        foreach ($this->extensionMap as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
                $workDir = '${__DIR__}/';
                $cacheDir = '${__DIR__}/tmp/ext/' . $item->name;
                $downloadScript = <<<EOF

## ------------------- download extension {$item->name} start -------------------
test -d {$cacheDir} && rm -rf {$cacheDir}
mkdir -p {$cacheDir}
cd {$cacheDir}
{$item->downloadScript}
cd {$item->downloadDirName}
test -f {$workDir}/ext/{$item->file} || tar -czf {$workDir}/ext/{$item->file} ./
cd {$workDir}
## ------------------- download extension {$item->name} end -------------------

EOF;

                $download_scripts[] = $downloadScript . PHP_EOL;

                $extract_files[] = "mkdir -p ext/{$item->name}" . PHP_EOL;
                $extract_files[] = "tar --strip-components=1 -C ext/{$item->name} -xf pool/ext/{$item->file}" . PHP_EOL;

            }
        }
        file_put_contents(
            $this->getRootDir() . '/var/download-box/download_extension_use_git.sh',
            $this->downloadScriptHeader . PHP_EOL .
            implode(PHP_EOL, $download_scripts)
        );

        $download_urls = [];
        foreach ($this->libraryList as $item) {
            if ((!empty($item->url) && !$item->enableDownloadScript) || $item->enableDownloadWithMirrorURL) {
                $url = '';
                $item->mirrorUrls[] = $item->url;
                if (!empty($item->mirrorUrls)) {
                    $newMirrorUrls = [];
                    foreach ($item->mirrorUrls as $value) {
                        $newMirrorUrls[] = trim($value);
                    }
                    $url = implode("\t", $newMirrorUrls);
                }
                $download_urls[] = $url . PHP_EOL . " out=" . $item->file;

                $download_commands[] = "test -f \${POOL}/lib/{$item->file} || curl  --connect-timeout {$connect_timeout} --retry {$retry_number}  --retry-delay {$wait_retry} -Lo lib/{$item->file} {$item->url}" . PHP_EOL;

                $extract_files[] = "mkdir -p thirdparty/{$item->name}" . PHP_EOL;
                $extract_files[] = "tar --strip-components=1 -C thirdparty/{$item->name} -xf pool/lib/{$item->file}" . PHP_EOL;

            }
        }
        file_put_contents($this->getRootDir() . '/var/download-box/download_library_urls.txt',
            implode(PHP_EOL, $download_urls)
        );
        file_put_contents(
            $this->rootDir . '/var/download-box/download_library_use_script_for_windows.sh',
            $this->downloadScriptHeader . PHP_EOL .
            implode(PHP_EOL, $download_commands)
        );


        $download_scripts = [];
        foreach ($this->libraryList as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
                $workDir = '${__DIR__}/';
                $cacheDir = '${__DIR__}/tmp/lib/' . $item->name;
                $downloadScript = <<<EOF

## ------------------- download library {$item->name} start -------------------
test -d {$cacheDir} && rm -rf {$cacheDir}
mkdir -p {$cacheDir}
cd {$cacheDir}
{$item->downloadScript}
cd {$item->downloadDirName}
test -f  {$workDir}/lib/{$item->file} || tar  -czf {$workDir}/lib/{$item->file} ./
cd {$workDir}
## ------------------- download library {$item->name} end -------------------

EOF;

                $download_scripts[] = $downloadScript . PHP_EOL;


                $extract_files[] = "mkdir -p thirdparty/{$item->name}" . PHP_EOL;
                $extract_files[] = "tar --strip-components=1 -C thirdparty/{$item->name} -xf pool/lib/{$item->file}" . PHP_EOL;

            }
        }
        file_put_contents(
            $this->rootDir . '/var/download-box/download_library_use_git.sh',
            $this->downloadScriptHeader . PHP_EOL .
            implode(PHP_EOL, $download_scripts)
        );

        file_put_contents(
            $this->rootDir . '/var/download-box/extract-files.sh',
            implode(PHP_EOL, $extract_files)
        );

    }
}
