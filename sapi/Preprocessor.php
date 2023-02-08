<?php

namespace SwooleCli;

abstract class Project
{
    public string $name;
    public string $homePage = '';
    public string $license = '';
    public string $prefix = '';
    public int $licenseType = self::LICENSE_SPEC;

    public const LICENSE_SPEC = 0;
    public const LICENSE_APACHE2 = 1;
    public const LICENSE_BSD = 2;
    public const LICENSE_GPL = 3;
    public const LICENSE_LGPL = 4;
    public const LICENSE_MIT = 5;
    public const LICENSE_PHP = 6;

    public string $manual = '';

    function __construct(string $name)
    {
        $this->name = $name;
    }

    function withLicense(string $license, int $licenseType = self::LICENSE_SPEC): static
    {
        $this->license = $license;
        $this->licenseType = $licenseType;
        return $this;
    }

    function withHomePage(string $homePage): static
    {
        $this->homePage = $homePage;
        return $this;
    }

    public function withManual(string $manual): static
    {
        $this->manual = $manual;
        return $this;
    }
}

class Library extends Project
{
    public string $url;

    public string $configure = '';

    public string $file = '';

    public string $ldflags = '';
    public string $makeOptions = '';
    public string $makeInstallOptions = '';
    public string $beforeInstallScript = '';
    public string $afterInstallScript = '';
    public string $pkgConfig = '';
    public string $pkgName = '';

    public string $prefix = '/usr';
    public bool $skipBuildLicense = false;
    public bool $skipBuildInstall = false;
    public bool $cleanBuildDirectory = false;
    public string $untarArchiveCommand = 'tar';
    public string $beforeConfigureScript = '';
    public string $binPath = '';

    public function __construct(string $name, string $prefix = '/usr')
    {
        $this->withPrefix($prefix);
        parent::__construct($name);
    }

    function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function withPrefix(string $prefix): static
    {
        $this->prefix = $prefix;
        $this->withLdflags('-L' . $prefix . '/lib');
        $this->withPkgConfig($prefix . '/lib/pkgconfig');
        return $this;
    }

    function withFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    function withConfigure(string $configure): static
    {
        $this->configure = $configure;
        return $this;
    }

    function withLdflags(string $ldflags): static
    {
        $this->ldflags = $ldflags;
        return $this;
    }

    function withMakeOptions(string $makeOptions): static
    {
        $this->makeOptions = $makeOptions;
        return $this;
    }

    function withScriptBeforeInstall(string $script)
    {
        $this->beforeInstallScript = $script;
        return $this;
    }

    function withScriptAfterInstall(string $script)
    {
        $this->afterInstallScript = $script;
        return $this;
    }

    function withMakeInstallOptions(string $makeInstallOptions): static
    {
        $this->makeInstallOptions = $makeInstallOptions;
        return $this;
    }

    function withPkgConfig(string $pkgConfig): static
    {
        $this->pkgConfig = $pkgConfig;
        return $this;
    }

    function withPkgName(string $pkgName): static
    {
        $this->pkgName = $pkgName;
        return $this;
    }

    public function withCleanBuildDirectory(): static
    {
        $this->cleanBuildDirectory = true;
        return $this;
    }

    public function withSkipBuildInstall(): static
    {
        $this->skipBuildInstall = true;
        $this->skipBuildLicense = true;
        $this->withBinPath('');
        $this->disableDefaultPkgConfig();
        $this->disablePkgName();
        $this->disableDefaultLdflags();
        return $this;
    }

    public function withUntarArchiveCommand(string $command): static
    {
        $this->untarArchiveCommand = $command;
        return $this;
    }

    public function withScriptBeforeConfigure(string $script): static
    {
        $this->beforeConfigureScript = $script;
        return $this;
    }
    public function withSkipBuildLicense(): static
    {
        $this->skipBuildLicense = true;
        return $this;
    }
    public function disableDefaultLdflags(): static
    {
        $this->ldflags = '';
        return $this;
    }

    public function withBinPath(string $path): static
    {
        $this->binPath = $path;
        return $this;
    }

    public function disableDefaultPkgConfig(): static
    {
        $this->pkgConfig = '';
        return $this;
    }

    public function disablePkgName(): static
    {
        $this->pkgName = '';
        return $this;
    }
}

class Extension extends Project
{
    public string $url;
    public string $options = '';
    public string $peclVersion = '';
    public string $file = '';
    public string $path = '';

    function withOptions(string $options): static
    {
        $this->options = $options;
        return $this;
    }

    function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function withPeclVersion(string $peclVersion): static
    {
        $this->peclVersion = $peclVersion;
        return $this;
    }
}

class Preprocessor
{
    protected string $osType = 'linux';
    protected array $libraryList = [];
    protected array $extensionList = [];
    protected string $rootDir;
    protected string $libraryDir;
    protected string $extensionDir;
    protected array $pkgConfigPaths = [];
    protected string $phpSrcDir;
    protected string $dockerVersion = 'latest';
    /**
     * 指向 swoole-cli 所在的目录
     * $workDir/ext 存放扩展
     * $workDir/thirdparty 存放第三方库的源代码，编译后的 .a 文件会安装到系统的 /usr 目录下
     * 在 macOS 系统上，/usr 目录将会被替换为 $workDir/usr
     */
    protected string $workDir = '/work';
    protected string $extraLdflags = '';
    protected string $extraOptions = '';
    protected int $maxJob = 8;
    protected bool $installLibrary = true;

    /**
     * Extensions enabled by default
     * @var array|string[]
     */
    protected array $extEnabled = [
        'opcache',
        'curl',
        'iconv',
        'bz2',
        'bcmath',
        'pcntl',
        'filter',
        'session',
        'tokenizer',
        'mbstring',
        'ctype',
        'zlib',
        'zip',
        'posix',
        'sockets',
        'pdo',
        'sqlite3',
        'phar',
        'mysqlnd',
        'mysqli',
        'intl',
        'fileinfo',
        'pdo_mysql',
        'pdo_sqlite',
        'soap',
        'xsl',
        'gmp',
        'exif',
        'sodium',
        'openssl',
        'readline',
        'xml',
        'gd',
        'redis',
        'swoole',
        'yaml',
        'imagick',
        'mongodb',
    ];

    protected array $endCallbacks = [];
    protected array $extCallbacks = [];
    protected array $binPaths = [];

    function __construct(string $rootPath)
    {
        $this->rootDir = $rootPath;
        $this->libraryDir = $rootPath . '/pool/lib';
        $this->extensionDir = $rootPath . '/pool/ext';

        // 此目录用于存放源代码包
        if (!is_dir($rootPath . '/pool')) {
            mkdir($rootPath . '/pool');
        }
        if (!is_dir($this->libraryDir)) {
            mkdir($this->libraryDir);
        }
        if (!is_dir($this->extensionDir)) {
            mkdir($this->extensionDir);
        }

        switch (PHP_OS) {
            default:
            case 'Linux':
                $this->setOsType('linux');
                break;
            case 'Darwin':
                $this->setOsType('macos');
                break;
            case 'WINNT':
                $this->setOsType('win');
                break;
        }
    }

    protected function setOsType(string $osType)
    {
        $this->osType = $osType;
    }

    function getOsType()
    {
        return $this->osType;
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
    }

    public function getPhpSrcDir()
    {
        return $this->phpSrcDir ;
    }

    function setDockerVersion(string $dockerVersion)
    {
        $this->dockerVersion = $dockerVersion;
    }

    function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    function setWorkDir(string $workDir)
    {
        $this->workDir = $workDir;
    }

    function getWorkDir()
    {
        return $this->workDir;
    }

    function setExtraLdflags(string $flags)
    {
        $this->extraLdflags = $flags;
    }

    function setExtraOptions(string $options)
    {
        $this->extraOptions = $options;
    }

    function donotInstallLibrary()
    {
        $this->installLibrary = false;
    }

    function addLibrary(Library $lib)
    {
        if (empty($lib->file)) {
            $lib->file = basename($lib->url);
        }
        $skip_library_download = getenv('SKIP_LIBRARY_DOWNLOAD');
        if (empty($skip_library_download)) {
            if (!is_file($this->libraryDir . '/' . $lib->file)) {
                # echo `wget {$lib->url} -O {$this->libraryDir}/{$lib->file}`;
                # echo $lib->file;
                echo '[Library] file downloading: ' . $lib->file . PHP_EOL . 'download url: ' . $lib->url . PHP_EOL;
                `curl --connect-timeout 15 --retry 5 --retry-delay 5  -Lo {$this->libraryDir}/{$lib->file} '{$lib->url}'`;
                echo PHP_EOL;
                echo 'download ' . $lib->file . ' OK ' . PHP_EOL . PHP_EOL;
                // TODO PGP  验证
            } else {
                echo "[Library] file cached: " . $lib->file . PHP_EOL;
            }
        }

        if (!empty($lib->pkgConfig)) {
            $this->pkgConfigPaths[] = $lib->pkgConfig;
        }

        if (!empty($lib->binPath)) {
            $this->binPaths[] = $lib->binPath;
        }

        if (empty($lib->license)) {
            throw new \RuntimeException("require license");
        }

        $this->libraryList[] = $lib;
    }

    function addExtension(Extension $ext)
    {
        if ($ext->peclVersion) {
            if ($ext->peclVersion == 'latest') {
                $find = glob($this->extensionDir . '/' . $ext->name . '-*.tgz');
                if (!$find) {
                    goto _download;
                }
                $file = basename($find[0]);
            } else {
                $file = $ext->name . '-' . $ext->peclVersion . '.tgz';
            }

            $ext->file = $file;
            $ext->path = $this->extensionDir . '/' . $file;

            if (!is_file($ext->path)) {
                _download:
                # $download_name = $ext->peclVersion == 'latest' ? $ext->name : $ext->name . '-' . $ext->peclVersion;
                # echo "curl download {$download_name} " . PHP_EOL;
                # echo `cd {$this->extensionDir} && pecl download $download_name && cd -`;
                /*
                 * 不使用 pecl 下载扩展
                 * curl -lO https://pecl.php.net/get/redis
                 * curl -lO https://pecl.php.net/get/redis-5.3.7.tgz
                 */
                $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';
                $download_url = '';
                $download_name = '';
                if ($ext->peclVersion == 'latest') {
                    $download_name = $ext->name;
                    $download_url = "https://pecl.php.net/get/" . $download_name;
                } else {
                    $download_name = $ext->name . '-' . $ext->peclVersion . '.tgz';
                    $download_url = "https://pecl.php.net/get/" . $ext->name . '-' . $ext->peclVersion . '.tgz';
                }
                echo "curl downloading {$download_name} dongload link $download_url" . PHP_EOL;
                $cmd="cd {$this->extensionDir} && curl --user-agent '{$userAgent}' --connect-timeout 15 --retry 5 --retry-delay 5  -LO '{$download_url}' && cd -";
                echo $cmd;
                `$cmd`;
                echo 'download ' . $ext->file . ' OK ' . PHP_EOL . PHP_EOL;
            } else {
                echo "[Extension] file cached: " . $ext->file . PHP_EOL;
            }

            $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
            if (is_file($ext->path)) {
                echo `mkdir -p $dst_dir`;
                //扩展目录不存在指定扩展的文件夹，则解压扩展包到扩展目录
                if (!(new \FilesystemIterator($dst_dir))->valid()) {
                    echo `tar --strip-components=1 -C $dst_dir -xf {$ext->path}`;
                }
            }
        }

        $this->extensionList[] = $ext;
    }

    function addEndCallback($fn)
    {
        $this->endCallbacks[] = $fn;
    }

    function setExtCallback($name, $fn)
    {
        $this->extCallbacks[$name] = $fn;
    }

    function parseArguments(int $argc, array $argv)
    {
        /**
         * Scan and load files in directory
         */
        $extInclude = getenv('SWOOLE_CLI_EXT_INCLUDE') ?: $this->rootDir . '/conf.d';
        $extAvailabled = [];
        $files = scandir($extInclude);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..') {
                continue;
            }
            $extAvailabled[basename($f, '.php')] = require $extInclude . '/' . $f;
        }

        for ($i = 1; $i < $argc; $i++) {
            $op = $argv[$i][0];
            $ext = substr($argv[$i], 1);
            if ($op == '+') {
                $this->extEnabled[] = $ext;
            } elseif ($op == '-') {
                $key = array_search($ext, $this->extEnabled);
                if ($key !== false) {
                    unset($this->extEnabled[$key]);
                }
            }
        }

        foreach ($this->extEnabled as $ext) {
            if (!isset($extAvailabled[$ext])) {
                echo "unsupported extension[$ext]\n";
                continue;
            }
            ($extAvailabled[$ext])($this);
            if (isset($this->extCallbacks[$ext])) {
                ($this->extCallbacks[$ext])($this);
            }
        }
    }

    function gen()
    {
        $this->pkgConfigPaths[] = '$PKG_CONFIG_PATH';
        $this->pkgConfigPaths = array_unique($this->pkgConfigPaths);
        $this->binPaths[] = '$PATH';
        $this->binPaths = array_unique($this->binPaths);

        ob_start();
        include __DIR__ . '/make.php';
        file_put_contents($this->rootDir . '/make.sh', ob_get_clean());

        ob_start();
        include __DIR__ . '/license.php';
        file_put_contents($this->rootDir . '/bin/LICENSE', ob_get_clean());

        ob_start();
        include __DIR__ . '/credits.php';
        file_put_contents($this->rootDir . '/bin/credits.html', ob_get_clean());

        foreach ($this->endCallbacks as $endCallback) {
            $endCallback($this);
        }
    }

    /**
     * make -j {$n}
     * @param int $n
     */
    function setMaxJob(int $n)
    {
        $this->maxJob = $n;
    }

    function info()
    {
        echo '==========================================================' . PHP_EOL;
        echo "Extension count: " . count($this->extensionList) . PHP_EOL;
        echo '==========================================================' . PHP_EOL;
        foreach ($this->extensionList as $item) {
            echo $item->name . PHP_EOL;
        }

        echo '==========================================================' . PHP_EOL;
        echo "Library count: " . count($this->libraryList) . PHP_EOL;
        echo '==========================================================' . PHP_EOL;
        foreach ($this->libraryList as $item) {
            echo "{$item->name}\n";
        }
    }
}
