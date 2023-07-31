<?php

namespace SwooleCli;

use AllowDynamicProperties;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;
use RuntimeException;
use SwooleCli\PreprocessorTrait\CompilerTrait;
use SwooleCli\PreprocessorTrait\DownloadBoxTrait;
use SwooleCli\PreprocessorTrait\WebUITrait;

#[AllowDynamicProperties]
class Preprocessor
{
    use DownloadBoxTrait;

    use WebUITrait;

    use CompilerTrait;

    public const VERSION = '1.6';
    public const IMAGE_NAME = 'phpswoole/swoole-cli-builder';
    public const CONTAINER_NAME = 'swoole-cli-builder';

    protected static ?Preprocessor $instance = null;

    protected array $prepareArgs = [];
    protected string $osType = 'linux';
    protected array $libraryList = [];
    protected array $extensionList = [];

    protected string $cCompiler = 'clang';
    protected string $cppCompiler = 'clang++';

    protected string $lld = 'ld.lld';

    protected array $libraryMap = [];

    protected array $extensionMap = [];
    /**
     * 仅用于预处理阶段
     * @var string
     */
    protected string $rootDir;
    protected string $libraryDir;
    protected string $extensionDir;
    protected array $pkgConfigPaths = [];
    protected string $phpSrcDir;
    protected string $dockerVersion = 'latest';
    /**
     * 指向 swoole-cli 所在的目录，在构建阶段使用
     * $workDir/pool/ext 存放扩展
     * $workDir/pool/lib 存放依赖库
     */
    protected string $workDir = '/work';
    /**
     * 依赖库的构建目录，在构建阶段使用
     * @var string
     */
    protected string $buildDir = '/work/thirdparty';
    /**
     * 编译后.a静态库文件安装目录的全局前缀，在构建阶段使用
     * @var string
     */
    protected string $globalPrefix = '/usr/local/swoole-cli';

    protected string $extraLdflags = '';
    protected string $extraOptions = '';
    protected string $extraCflags = '';

    protected array $variables = [];

    protected array $exportVariables = [];

    protected array $preInstallCommands = [
        'alpine' => [],
        'debian' => [],
        'ubuntu' => [],
        'macos' => []
    ];

    /**
     * default value : CPU   logical processors
     * @var string
     */
    protected string $maxJob = '${LOGICAL_PROCESSORS}';

    /**
     * CPU   logical processors
     * @var string
     */
    protected string $logicalProcessors = '';

    protected array $inputOptions = [];

    protected array $binPaths = [];
    /**
     * Extensions enabled by default
     * @var array|string[]
     */
    protected array $extEnabled = [
        //'opcache', //需要修改源码才能实现
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
        'redis',
        'swoole',
        'yaml',
        'imagick',
        //'mongodb', //php8.2 需要处理依赖库问题 more info ： https://github.com/jingjingxyk/swoole-cli/pull/79/files
        'gd',
    ];
    protected array $extEnabledBuff = [];
    protected array $endCallbacks = [];
    protected array $extCallbacks = [];

    protected array $extHooks = [];

    protected string $configureVarables;

    protected string $buildType = 'release';

    protected string $proxyConfig = '';

    protected string $httpProxy = '';

    protected bool $installLibraryCached = false;

    protected function __construct()
    {
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

    public function setLinker(string $ld): static
    {
        $this->lld = $ld;
        return $this;
    }

    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected function setOsType(string $osType)
    {
        $this->osType = $osType;
    }

    public function getOsType(): string
    {
        return $this->osType;
    }

    public function getSystemArch(): string
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

    public function getImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return self::VERSION;
        } else {
            return self::VERSION . '-' . $arch;
        }
    }

    public function getBaseImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return 'base';
        } else {
            return 'base' . '-' . $arch;
        }
    }

    public function getBaseImageDockerFile(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return 'Dockerfile';
        } else {
            return 'Dockerfile' . '-' . $arch;
        }
    }

    public function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
    }

    public function getPhpSrcDir(): string
    {
        return $this->phpSrcDir;
    }

    public function setGlobalPrefix(string $prefix): void
    {
        $this->globalPrefix = $prefix;
    }

    public function getGlobalPrefix(): string
    {
        return $this->globalPrefix;
    }

    public function setRootDir(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getPrepareArgs(): array
    {
        return $this->prepareArgs;
    }

    public function setLibraryDir(string $libraryDir)
    {
        $this->libraryDir = $libraryDir;
    }

    public function setExtensionDir(string $extensionDir)
    {
        $this->extensionDir = $extensionDir;
    }

    public function setWorkDir(string $workDir)
    {
        $this->workDir = $workDir;
    }

    public function setBuildDir(string $buildDir)
    {
        $this->buildDir = $buildDir;
    }

    public function getBuildDir(): string
    {
        return $this->buildDir;
    }

    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    public function setExtraLdflags(string $flags)
    {
        $this->extraLdflags = $flags;
    }

    public function setExtraCflags(string $flags)
    {
        $this->extraCflags = $flags;
    }

    public function setConfigureVarables(string $varables)
    {
        $this->configureVarables = $varables;
    }

    public function setExtraOptions(string $options)
    {
        $this->extraOptions = $options;
    }

    public function setInstallLibraryCached(bool $installLibraryCached): void
    {
        $this->installLibraryCached = $installLibraryCached;
    }

    /**
     * make -j {$n}
     * @param int $n
     * @return Preprocessor
     */
    public function setMaxJob(int $n): static
    {
        $this->maxJob = (string)$n;
        return $this;
    }

    public function getMaxJob(): string
    {
        return $this->maxJob;
    }

    /**
     * set CPU  logical processors
     * @param string $logicalProcessors
     * @return $this
     */
    public function setLogicalProcessors(string $logicalProcessors): static
    {
        $this->logicalProcessors = $logicalProcessors;
        return $this;
    }

    public function setBuildType(string $buildType): static
    {
        $this->buildType = $buildType;
        return $this;
    }

    public function getBuildType(): string
    {
        return $this->buildType;
    }

    public function setProxyConfig(string $shell = '', string $httpProxy = ''): static
    {
        $this->proxyConfig = $shell;
        $this->httpProxy = $httpProxy;
        return $this;
    }

    public function getProxyConfig(): string
    {
        return $this->proxyConfig;
    }

    public function getHttpProxy(): string
    {
        return $this->httpProxy;
    }


    public function setExtEnabled(array $extEnabled = []): static
    {
        $this->extEnabled = array_merge($this->extEnabledBuff, $extEnabled);
        return $this;
    }

    public function donotInstallLibrary(): void
    {
        $this->installLibrary = false;
    }

    /**
     * @param string $url
     * @param string $file
     * @param string $md5sum
     * @param string $httpProxyConfig
     * @return void
     */
    protected function downloadFile(string $url, string $file, string $md5sum, string $httpProxyConfig = ''): void
    {
        $retry_number = DOWNLOAD_FILE_RETRY_NUMBE;
        $user_agent = DOWNLOAD_FILE_USER_AGENT;//--user-agent='{$user_agent}'
        $wait_retry = DOWNLOAD_FILE_WAIT_RETRY;
        $connect_timeout = DOWNLOAD_FILE_CONNECTION_TIMEOUT;
        echo PHP_EOL;
        if ($this->getInputOption('with-downloader') === 'wget') {
            $cmd = "wget   {$url}  -O {$file}  -t {$retry_number} --wait={$wait_retry} -T {$connect_timeout} ";
        } else {
            $cmd = "curl  --connect-timeout {$connect_timeout} --retry {$retry_number}  --retry-delay {$wait_retry}  -Lo '{$file}' '{$url}' ";
        }
        $cmd = $httpProxyConfig . PHP_EOL . $cmd;
        echo $cmd;
        echo PHP_EOL;
        echo `$cmd`;
        echo PHP_EOL;
        if (is_file($file) && (filesize($file) == 0)) {
            unlink($file);
        }
        // 下载失败
        if (!is_file($file) or filesize($file) == 0) {
            throw new Exception("Downloading file[" . basename($file) . "] from url[$url] failed");
        }
        // 下载文件的 MD5 不一致
        if (!empty($md5sum) and !$this->checkFileMd5sum($file, $md5sum)) {
            throw new Exception("The md5 of downloaded file[$file] is inconsistent with the configuration");
        }
    }

    /**
     * @param string $file
     * @param string $md5sum
     * @param string $downloadScript
     * @return void
     */
    protected function downloadFileWithScript(string $file, string $md5sum, string $downloadScript): void
    {
        echo PHP_EOL;
        echo $downloadScript;
        echo PHP_EOL;
        echo `$downloadScript`;
        echo PHP_EOL;

        if (is_file($file) && (filesize($file) == 0)) {
            unlink($file);
        }
        // 下载失败
        if (!is_file($file) or filesize($file) == 0) {
            throw new Exception("Downloading file[" . basename($file) . "]  failed");
        }
        // 下载文件的 MD5 不一致
        if (!empty($md5sum) and !$this->checkFileMd5sum($file, $md5sum)) {
            throw new Exception("The md5 of downloaded file[$file] is inconsistent with the configuration");
        }
    }

    /**
     * @param string $path
     * @param string $md5
     * @return bool
     */
    protected function checkFileMd5sum(string $path, string $md5): bool
    {
        // md5 不匹配，删除文件
        if ($md5 != md5_file($path)) {
            unlink($path);
            return false;
        }
        return true;
    }

    /**
     * @param Library $lib
     * @throws Exception
     */
    public function addLibrary(Library $lib): void
    {
        if ($lib->enableDownloadScript || !empty($lib->url)) {
            if (empty($lib->file)) {
                if ($lib->enableDownloadScript) {
                    $lib->file = $lib->name . '.tar.gz';
                } else {
                    $lib->file = basename($lib->url);
                }
            }

            if (!empty($this->getInputOption('with-download-mirror-url'))) {
                $lib->url = $this->getInputOption('with-download-mirror-url') . '/libraries/' . $lib->file;
                $lib->enableDownloadWithMirrorURL = true;
            }
            $lib->path = $this->libraryDir . '/' . $lib->file;

            // 本地文件被修改，MD5 不一致，删除后重新下载
            if (!empty($lib->md5sum) and is_file($lib->path)) {
                $this->checkFileMd5sum($lib->path, $lib->md5sum);
            }

            //文件内容为空
            if (file_exists($lib->path) && (filesize($lib->path) == 0)) {
                unlink($lib->path);
            }

            //获取最新的源码包
            if (is_file($lib->path) && $lib->enableLatestTarball) {
                unlink($lib->path);
            }

            if (!$this->getInputOption('with-skip-download')) {
                if (file_exists($lib->path)) {
                    echo "[Library] file cached: " . $lib->file . PHP_EOL;
                } else {
                    $httpProxyConfig = $this->getProxyConfig();
                    if (!$lib->enableHttpProxy) {
                        $httpProxyConfig = '';
                    }
                    if ($lib->enableDownloadScript && !$lib->enableDownloadWithMirrorURL) {
                        if (!empty($lib->downloadScript) && !empty($lib->downloadDirName)) {
                            $workDir = $this->getRootDir();
                            $cacheDir = '${__DIR__}/var/tmp/download/lib';
                            $lib->downloadScript = <<<EOF
                            __DIR__={$workDir}/
                            {$httpProxyConfig}
                            test -d {$cacheDir} && rm -rf {$cacheDir}
                            mkdir -p {$cacheDir}
                            cd {$cacheDir}
                            test -d {$lib->downloadDirName} && rm -rf {$lib->downloadDirName}
                            {$lib->downloadScript}
                            cd {$lib->downloadDirName}
                            test -f {$lib->path} || tar   -zcf {$lib->path} ./
                            cd {$workDir}

EOF;

                            $this->downloadFileWithScript(
                                $lib->path,
                                $lib->md5sum,
                                $lib->downloadScript
                            );
                        } else {
                            throw new Exception(
                                "[Library] withDownloadScript() require \$downloadDirName and \$downloadScript  "
                            );
                        }
                    } else {
                        echo "[Library] {$lib->file} not found, downloading: " . $lib->url . PHP_EOL;
                        $this->downloadFile($lib->url, $lib->path, $lib->md5sum, $httpProxyConfig);
                    }
                }
            }
        } else {
            throw new Exception(
                "[Library] require url OR downloadscript "
            );
        }
        if (!empty($lib->pkgConfig)) {
            $this->pkgConfigPaths[] = $lib->pkgConfig;
        }

        if (!empty($lib->binPath)) {
            $this->binPaths[] = $lib->binPath;
        }

        if (empty($lib->license)) {
            throw new Exception("require license");
        }

        if (!empty($lib->preInstallCommands)) {
            foreach (['alpine', 'debian', 'ubuntu', 'macos'] as $os) {
                if (!empty($lib->preInstallCommands[$os])) {
                    $this->preInstallCommands[$os] = array_merge(
                        $this->preInstallCommands[$os],
                        $lib->preInstallCommands[$os]
                    );
                }
            }
        }

        $this->libraryList[] = $lib;
        $this->libraryMap[$lib->name] = $lib;
    }

    public function addExtension(Extension $ext): void
    {
        if (!empty($ext->peclVersion) || $ext->enableDownloadScript || !empty($ext->url)) {
            if (!empty($ext->url)) {
                if (empty($ext->file)) {
                    $ext->file = $ext->name . '.tgz';
                }
            }
            if (!empty($ext->peclVersion)) {
                $ext->file = $ext->name . '-' . $ext->peclVersion . '.tgz';
                $ext->url = "https://pecl.php.net/get/{$ext->file}";
            }

            if ($ext->enableDownloadScript) {
                if (empty($ext->file)) {
                    $ext->file = $ext->name . '.tgz';
                }
                $ext->url = '';
            }
            $ext->path = $this->extensionDir . '/' . $ext->file;

            if (!empty($this->getInputOption('with-download-mirror-url'))) {
                $ext->url = $this->getInputOption('with-download-mirror-url') . '/extensions/' . $ext->file;
                $ext->enableDownloadWithMirrorURL = true;
            }

            // 检查文件的 MD5，若不一致删除后重新下载
            if (!empty($ext->md5sum) and file_exists($ext->path)) {
                $this->checkFileMd5sum($ext->path, $ext->md5sum);
            }

            //文件内容为空，重新下载
            if (file_exists($ext->path) && (filesize($ext->path) == 0)) {
                unlink($ext->path);
            }

            //不使用缓存包，拉取最新源码包
            if (file_exists($ext->path) && $ext->enableLatestTarball) {
                unlink($ext->path);
            }

            if (!$this->getInputOption('with-skip-download')) {
                if (!file_exists($ext->path)) {
                    $httpProxyConfig = $this->getProxyConfig();
                    if (!$ext->enableHttpProxy) {
                        $httpProxyConfig = '';
                    }
                    if ($ext->enableDownloadScript && !$ext->enableDownloadWithMirrorURL) {
                        if (!empty($ext->downloadScript) && !empty($ext->downloadDirName)) {
                            $workDir = $this->getRootDir();
                            $cacheDir = '${__DIR__}/var/tmp/download/ext';
                            $ext->downloadScript = <<<EOF
                            __DIR__={$workDir}/
                            {$httpProxyConfig}
                            test -d {$cacheDir} && rm -rf {$cacheDir}
                            mkdir -p {$cacheDir}
                            cd {$cacheDir}
                            test -d {$ext->downloadDirName} && rm -rf {$ext->downloadDirName}
                            {$ext->downloadScript}
                            cd {$ext->downloadDirName}
                            test -f {$ext->path} ||  tar  -zcf {$ext->path} ./
                            cd {$workDir}

EOF;

                            $this->downloadFileWithScript(
                                $ext->path,
                                $ext->md5sum,
                                $ext->downloadScript
                            );
                        } else {
                            throw new Exception(
                                "[Extension] withDownloadScript() require \$downloadDirName and \$downloadScript  "
                            );
                        }
                    } else {
                        echo "[Extension] {$ext->file} not found, downloading: " . $ext->url . PHP_EOL;
                        $this->downloadFile($ext->url, $ext->path, $ext->md5sum, $httpProxyConfig);
                    }
                } else {
                    echo "[Extension] file cached: " . $ext->file . PHP_EOL;
                }

                $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
                $ext_name = $ext->name;
                if (!empty($ext->aliasName)) {
                    $dst_dir = "{$this->rootDir}/ext/{$ext->aliasName}";
                    $ext_name = $ext->aliasName;
                }
                if (($ext->enableLatestTarball || !$ext->enableBuildLibraryCached)
                    &&
                    (!empty($ext->peclVersion) || $ext->enableDownloadScript || !empty($ext->url))
                ) {
                    $this->deleteDirectoryIfExists($dst_dir);
                }

                $this->mkdirIfNotExists($dst_dir, 0777, true);
                $cached = $dst_dir . '/.completed';
                if (file_exists($cached) && $ext->enableBuildLibraryCached) {
                    if (in_array($this->buildType, ['dev', 'debug'])) {
                        echo '[ext/' . $ext_name . '] cached ' . PHP_EOL;
                    }
                } else {
                    echo `tar --strip-components=1 -C $dst_dir -xf {$ext->path}`;
                    if ($ext->enableBuildLibraryCached) {
                        touch($cached);
                    }
                }
            }
        }
        $this->extensionList[] = $ext;
        $this->extensionMap[$ext->name] = $ext;
    }

    public function getLibrary(string $name): ?Library
    {
        if (!isset($this->libraryMap[$name])) {
            return null;
        }
        return $this->libraryMap[$name];
    }

    public function getLibraryPackages(): array
    {
        $packages = [];
        /**
         * @var $item Library
         */
        foreach ($this->libraryList as $item) {
            if (!empty($item->pkgNames)) {
                $packages = array_merge($packages, $item->pkgNames);
            }
        }
        return $packages;
    }

    public function withBinPath(string $path): static
    {
        $this->binPaths[] = $path;
        return $this;
    }

    public function withVariable(string $key, string $value): static
    {
        $this->variables[] = [$key => $value];
        return $this;
    }

    public function withExportVariable(string $key, string $value): static
    {
        $this->exportVariables[] = [$key => $value];
        return $this;
    }

    public function withPreInstallCommand(string $os, string $preInstallCommand): static
    {
        if (!empty($os) && in_array($os, ['alpine', 'debian', 'ubuntu', 'macos']) && !empty($preInstallCommand)) {
            $this->preInstallCommands[$os][] = $preInstallCommand;
        }
        return $this;
    }

    public function getExtension(string $name): ?Extension
    {
        if (!isset($this->extensionMap[$name])) {
            return null;
        }
        return $this->extensionMap[$name];
    }

    public function existsLibrary(string $name): bool
    {
        return isset($this->libraryMap[$name]);
    }

    public function existsExtension(string $name): bool
    {
        return isset($this->extensionMap[$name]);
    }

    public array $extensionDependentLibraryMap = [];

    public array $extensionDependentPackageNameMap = [];

    public array $extensionDependentPackageNames = [];

    public function setExtensionDependency(): void
    {
        $extensionDepsMap = [];
        foreach ($this->extensionList as $extension) {
            if (empty($extension->deps)) {
                $this->extensionDependentLibraryMap[$extension->name] = [];
            } else {
                $extensionDepsMap[$extension->name] = $extension->deps;
            }
        }

        foreach ($extensionDepsMap as $extensionName => $deps) {
            $pkgNames = [];
            foreach ($deps as $libraryName) {
                $packages = [];
                $this->getLibraryDependenciesByName($libraryName, $packages);
                foreach ($packages as $item) {
                    if (empty($item)) {
                        continue;
                    } else {
                        $pkgNames[] = trim($item);
                    }
                }
                $this->extensionDependentLibraryMap[$extensionName][] = $libraryName;
            }
            $this->extensionDependentPackageNameMap[$extensionName] = $pkgNames;
        }
        $pkgNames = [];
        foreach ($this->extensionDependentPackageNameMap as $extensionName => $value) {
            $pkgNames = array_merge($pkgNames, $value);
            $this->extensionDependentPackageNameMap[$extensionName] = array_values(array_unique($value));
        }
        $this->extensionDependentPackageNames = array_values(array_unique($pkgNames));
    }

    private function getLibraryDependenciesByName($libraryName, &$packages): void
    {
        if (!isset($this->libraryMap[$libraryName])) {
            throw new RuntimeException('library ' . $libraryName . ' no found');
        }
        $lib = $this->libraryMap[$libraryName];
        if (!empty($lib->pkgNames)) {
            $packages = array_merge($packages, $lib->pkgNames);
        }
        if (!empty($lib->deps)) {
            foreach ($lib->deps as $name) {
                $this->getLibraryDependenciesByName($name, $packages);
            }
        }
    }

    public function addEndCallback($fn)
    {
        $this->endCallbacks[] = $fn;
    }

    public function setExtCallback($name, $fn)
    {
        $this->extCallbacks[$name] = $fn;
    }

    public function setExtHook($name, $fn)
    {
        $this->extHooks[$name] = $fn;
    }

    public function parseArguments(int $argc, array $argv)
    {
        $this->prepareArgs = $argv;
        // parse the parameters passed in by the user
        for ($i = 1; $i < $argc; $i++) {
            $arg = $argv[$i];
            $op = $arg[0];
            $value = substr($argv[$i], 1);
            if ($op == '+') {
                $this->extEnabled[] = $value;
                $this->extEnabledBuff[] = $value;
            } elseif ($op == '-') {
                if ($arg[1] == '-') {
                    $_ = explode('=', substr($arg, 2));
                    $this->inputOptions[$_[0]] = $_[1] ?? true;
                } else {
                    $key = array_search($value, $this->extEnabled);
                    if ($key !== false) {
                        unset($this->extEnabled[$key]);
                    }
                }
            } elseif ($op == '@') {
                $this->setOsType($value);
            }
        }
    }

    /**
     * Get the value of an input option, attempting to read from command-line arguments and environment variables,
     * and returning the default value if not set
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getInputOption(string $key, string $default = ''): string
    {
        if (isset($this->inputOptions[$key])) {
            return $this->inputOptions[$key];
        }
        $env = getenv('SWOOLE_CLI_' . str_replace('-', '_', strtoupper($key)));
        if ($env !== false) {
            return $env;
        }
        return $default;
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
                        throw new Exception("The ext-{$item->name} depends on $lib, but it does not exist");
                    }
                }
            }
        }

        $libraryList = [];
        foreach ($sorted_list as $name) {
            if (empty($name)) {
                continue;
            }
            $libraryList[] = $libs[$name];
        }
        $this->libraryList = $libraryList;
    }

    protected function mkdirIfNotExists(string $dir, int $permissions = 0777, bool $recursive = false): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, $permissions, $recursive);
        }
    }

    protected function deleteDirectoryIfExists($path): bool
    {
        try {
            if (file_exists($path)) {
                $iterator = new \DirectoryIterator($path);
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isDot()) {
                        continue;
                    }
                    if ($fileinfo->isDir()) {
                        if ($this->deleteDirectoryIfExists($fileinfo->getPathname())) {
                            rmdir($fileinfo->getPathname());
                        }
                    }
                    if ($fileinfo->islink()) {
                        throw new Exception(
                            'file is ' . $fileinfo->getPathname() . ' link ; The real path is ' . $fileinfo->getRealPath()
                        );
                    }
                    if ($fileinfo->isFile()) {
                        unlink($fileinfo->getPathname());
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    /**
     * Scan and load config files in directory
     */
    protected function scanConfigFiles(string $dir, array &$extAvailabled)
    {
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..' or substr($f, -4, 4) != '.php') {
                continue;
            }
            $path = $dir . '/' . $f;
            if (is_dir($path)) {
                $this->scanConfigFiles($path, $extAvailabled);
            } else {
                $extAvailabled[basename($f, '.php')] = require $path;
            }
        }
    }

    public function loadDependentExtension($extension_name)
    {
        if (!isset($this->extensionMap[$extension_name])) {
            $file = realpath(__DIR__ . '/builder/extension/' . $extension_name . '.php');
            if (!is_file($file)) {
                throw new Exception("The ext-$extension_name does not exist");
            }
            $func = require $file;
            $func($this);
        }
        if (isset($this->extensionMap[$extension_name])) {
            $deps = $this->extensionMap[$extension_name]->dependentExtensions;
            if (!empty($deps)) {
                foreach ($deps as $extension_name) {
                    $this->loadDependentExtension($extension_name);
                }
            }
        }
    }

    public function loadDependentLibrary($library_name)
    {
        if (!isset($this->libraryMap[$library_name])) {
            $file = realpath(__DIR__ . '/builder/library/' . $library_name . '.php');
            if (!is_file($file)) {
                throw new Exception("The library-$library_name does not exist");
            }
            $func = require $file;
            $func($this);
        }

        if (isset($this->libraryMap[$library_name])) {
            $deps = $this->libraryMap[$library_name]->deps;
            if (!empty($deps)) {
                foreach ($deps as $library_name) {
                    $this->loadDependentLibrary($library_name);
                }
            }
        }
    }

    public function generateFile(string $templateFile, string $outFile): bool
    {
        if (!is_file($templateFile)) {
            return false;
        }
        ob_start();
        include $templateFile;
        return file_put_contents($outFile, ob_get_clean());
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    public function execute(): void
    {
        if (empty($this->rootDir)) {
            $this->rootDir = dirname(__DIR__, 2);
        }
        if (empty($this->libraryDir)) {
            $this->libraryDir = $this->rootDir . '/pool/lib';
        }
        if (empty($this->extensionDir)) {
            $this->extensionDir = $this->rootDir . '/pool/ext';
        }
        $this->mkdirIfNotExists($this->libraryDir, 0777, true);
        $this->mkdirIfNotExists($this->extensionDir, 0777, true);
        include __DIR__ . '/constants.php';

        $extAvailabled = [];
        $this->scanConfigFiles(__DIR__ . '/builder/extension', $extAvailabled);
        $confPath = $this->getInputOption('conf-path');
        if ($confPath) {
            $confDirList = explode(':', $confPath);
            foreach ($confDirList as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }
                $this->scanConfigFiles($dir, $extAvailabled);
            }
        }
        install_libraries($this);
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

        if ($this->getOsType() == 'macos') {
            if (is_file('/usr/local/opt/bison/bin/bison')) {
                $this->withBinPath('/usr/local/opt/bison/bin');
            } else {
                $this->loadDependentLibrary("bison");
            }
        }

        // autoload extension depend extension
        foreach ($this->extensionMap as $ext) {
            foreach ($ext->dependentExtensions as $extension_name) {
                $this->loadDependentExtension($extension_name);
            }
        }

        // autoload  library depend library
        foreach ($this->extensionMap as $ext) {
            foreach ($ext->deps as $library_name) {
                $this->loadDependentLibrary($library_name);
            }
        }

        $this->pkgConfigPaths = array_filter(array_unique($this->pkgConfigPaths));

        if ($this->getOsType() == 'macos') {
            $libcpp = '-lc++';
        } else {
            $libcpp = '-lstdc++';
        }
        $packagesArr = $this->getLibraryPackages();
        if (!empty($packagesArr)) {
            $packages = implode(' ', $packagesArr);
            $this->withVariable('PACKAGES', $packages);
            $this->withVariable('CPPFLAGS', '$CPPFLAGS $(pkg-config --cflags-only-I --static $PACKAGES ) ');
            $this->withVariable('LDFLAGS', '$LDFLAGS $(pkg-config --libs-only-L --static $PACKAGES ) ');
            $this->withVariable('LIBS', '$LIBS $(pkg-config --libs-only-l --static $PACKAGES ) ' . $libcpp);
        }
        if (!empty($this->varables) || !empty($packagesArr)) {
            $this->withExportVariable('CPPFLAGS', '$CPPFLAGS');
            $this->withExportVariable('LDFLAGS', '$LDFLAGS');
            $this->withExportVariable('LIBS', '$LIBS');
        }
        $this->binPaths[] = '$PATH';
        $this->binPaths = array_filter(array_unique($this->binPaths));
        $this->sortLibrary();
        $this->setExtensionDependency();

        if ($this->getInputOption('with-skip-download')) {
            $this->generateLibraryDownloadLinks();
            $this->generateFile(__DIR__ . '/template/make-download-box.php', $this->rootDir . '/make-download-box.sh');
        }

        $this->generateFile(__DIR__ . '/template/make-install-deps.php', $this->rootDir . '/make-install-deps.sh');
        $this->generateFile(__DIR__ . '/template/make.php', $this->rootDir . '/make.sh');
        $this->generateFile(__DIR__ . '/template/make-env.php', $this->rootDir . '/make-env.sh');
        $this->mkdirIfNotExists($this->rootDir . '/bin');
        $this->generateFile(__DIR__ . '/template/license.php', $this->rootDir . '/bin/LICENSE');
        $this->generateFile(__DIR__ . '/template/credits.php', $this->rootDir . '/bin/credits.html');

        copy($this->rootDir . '/sapi/scripts/pack-sfx.php', $this->rootDir . '/bin/pack-sfx.php');

        if ($this->getInputOption('with-dependency-graph')) {
            $this->generateFile(
                __DIR__ . '/template/extension-dependency-graph.php',
                $this->rootDir . '/bin/ext-dependency-graph.graphviz.dot'
            );
        }
        if ($this->getInputOption('with-web-ui')) {
            $this->generateWebUIData();
        }
        foreach ($this->endCallbacks as $endCallback) {
            $endCallback($this);
        }

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
