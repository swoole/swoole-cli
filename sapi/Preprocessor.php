<?php

namespace SwooleCli;

use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;

abstract class Project
{
    public string $name;
    public string $homePage = '';
    public string $license = '';
    public string $prefix = '';
    public array $deps = [];
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

    function depends(string ...$libs): static
    {
        $this->deps += $libs;
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
    public string $makeInstallCommand = 'install';

    public string $makeInstallOptions = '';
    public string $beforeInstallScript = '';
    public string $afterInstallScript = '';
    public string $pkgConfig = '';
    public string $pkgName = '';
    public string $prefix = '/usr';

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

    public function withMakeInstallCommand(string $makeInstallCommand): static
    {
        $this->makeInstallCommand = $makeInstallCommand;
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
    const VERSION = '1.5';
    const IMAGE_NAME = 'phpswoole/swoole-cli-builder';

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

    function getSystemArch()
    {
        $uname = posix_uname();
        switch ($uname['machine']) {
            case 'x86_64':
                return 'x64';
            case 'aarch64':
                return 'arm64';
            default:
                return $uname['machine'];
        }
    }

    function getImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return self::VERSION;
        } else {
            return self::VERSION . '-' . $arch;
        }
    }

    function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
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

    protected function downloadFile(string $url, string $file)
    {
        echo `wget {$url} -O {$file}`;
        if (!is_file($file) or filesize($file) == 0) {
            throw new \RuntimeException("Downloading file[$file] from url[$url] failed");
        }
    }

    function addLibrary(Library $lib)
    {
        if (empty($lib->file)) {
            $lib->file = basename($lib->url);
        }
        $skip_library_download = getenv('SKIP_LIBRARY_DOWNLOAD');
        if (empty($skip_library_download)) {
            if (!is_file($this->libraryDir . '/' . $lib->file)) {
                echo "[Library] {$lib->file} not found, downloading: " . $lib->url . PHP_EOL;
                $this->downloadFile($lib->url, "{$this->libraryDir}/{$lib->file}");
            } else {
                echo "[Library] file cached: " . $lib->file . PHP_EOL;
            }
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
            $download_name = $ext->peclVersion == 'latest' ? $ext->name : $ext->name . '-' . $ext->peclVersion;
            $ext->url = "https://pecl.php.net/get/$download_name";

            if (!is_file($ext->path)) {
                _download:
                echo "[Extension] {$ext->file} not found, downloading: " . $ext->url . PHP_EOL;
                $this->downloadFile($ext->url, $ext->path);
            } else {
                echo "[Extension] file cached: " . $ext->file . PHP_EOL;
            }

            $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
            if (!is_dir($dst_dir)) {
                echo `mkdir -p $dst_dir`;
            }

            echo `tar --strip-components=1 -C $dst_dir -xf {$ext->path}`;
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
            $value = substr($argv[$i], 1);
            if ($op == '+') {
                $this->extEnabled[] = $value;
            } elseif ($op == '-') {
                $key = array_search($value, $this->extEnabled);
                if ($key !== false) {
                    unset($this->extEnabled[$key]);
                }
            } elseif ($op == '@') {
                $this->setOsType($value);
            }
        }

        $this->extEnabled = array_unique($this->extEnabled);
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

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    protected function sortLibrary(): void
    {
        $libs = [];
        $sorter = new StringSort();
        foreach ($this->libraryList as $item) {
            $libs[$item->name] = $item;
            $sorter->add($item->name, $item->deps);
        }
        $sorted_list = $sorter->sort();
        foreach ($this->extensionList as $item) {
            if ($item->deps) {
                foreach ($item->deps as $lib) {
                    if (!isset($libs[$lib])) {
                        throw new \RuntimeException("The ext-{$item->name} depends on $lib, but it does not exist");
                    }
                }
            }
        }

        $libraryList = [];
        foreach ($sorted_list as $name) {
            $libraryList[] = $libs[$name];
        }
        $this->libraryList = $libraryList;
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    function gen()
    {
        $this->pkgConfigPaths[] = '$PKG_CONFIG_PATH';
        $this->pkgConfigPaths = array_unique($this->pkgConfigPaths);
        $this->sortLibrary();

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
