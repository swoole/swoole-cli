<?php

namespace SwooleCli;

abstract class Project
{
    public string $name;
    public string $homePage = '';
    public string $license = '';
    public string $prefix = '';
    public int $licenseType = self::LICENSE_SPEC;

    const LICENSE_SPEC = 0;
    const LICENSE_APACHE2 = 1;
    const LICENSE_BSD = 2;
    const LICENSE_GPL = 3;
    const LICENSE_LGPL = 4;
    const LICENSE_MIT = 5;
    const LICENSE_PHP = 6;

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
}

class Library extends Project
{
    public string $url;
    public string $configure = '';
    public string $file = '';
    public string $ldflags = '';
    public string $makeOptions = '';
    public string $makeInstallOptions = '';
    public string $pkgConfig = '';
    public string $pkgName = '';
    public string $prefix = '/usr';
    public bool $clearDylib = false;

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

    function withClearDylib(bool $clearDylib = true): static
    {
        $this->clearDylib = $clearDylib;
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
    public string $osType;
    protected array $libraryList = [];
    protected array $extensionList = [];
    protected string $rootDir;
    protected string $libraryDir;
    protected string $extensionDir;
    protected array $pkgConfigPaths = [];
    protected string $phpSrcDir;
    protected string $dockerVersion = 'latest';
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
        'xml',
        'gd',
        'redis',
        'swoole',
        'yaml',
        'imagick',
    ];

    protected array $endCallbacks = [];
    protected array $extCallbacks = [];

    function __construct(string $rootPath)
    {
        $this->rootDir = $rootPath;
        $this->libraryDir = $rootPath . '/pool/lib';
        $this->extensionDir = $rootPath . '/pool/ext';

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

    function setOsType(string $osType)
    {
        $this->osType = $osType;
    }

    function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
    }

    function setDockerVersion(string $dockerVersion)
    {
        $this->dockerVersion = $dockerVersion;
    }

    function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    function setWorkdir(string $workDir)
    {
        $this->workDir = $workDir;
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
        if (!is_file($this->libraryDir . '/' . $lib->file)) {
            echo `wget {$lib->url} -O {$this->libraryDir}/{$lib->file}`;
            echo $lib->file;
        } else {
            echo "[Library] file cached: " . $lib->file . PHP_EOL;
        }

        if (!empty($lib->pkgConfig)) {
            $this->pkgConfigPaths[] = $lib->pkgConfig;
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
                $download_name = $ext->peclVersion == 'latest' ? $ext->name : $ext->name . '-' . $ext->peclVersion;
                echo "pecl download $download_name\n";
                echo `cd {$this->extensionDir} && pecl download $download_name && cd -`;
            } else {
                echo "[Extension] file cached: " . $ext->file . PHP_EOL;
            }

            $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
            if (!is_dir($dst_dir)) {
                echo `mkdir -p $dst_dir`;
                echo `tar --strip-components=1 -C $dst_dir -xf {$ext->path}`;
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

        ob_start();
        include __DIR__ . '/make.php';
        file_put_contents($this->rootDir . '/make.sh', ob_get_clean());

        ob_start();
        include __DIR__ . '/license.php';
        file_put_contents($this->rootDir . '/bin/LICENSE', ob_get_clean());

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
