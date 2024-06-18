<?php

namespace SwooleCli;

abstract class Project
{
    public string $name;

    public string $aliasName = '';

    public string $url;

    public string $path = '';

    public string $file = '';

    public string $hash = '';
    public bool $hashVerify = false;

    public bool $enableHashVerify = false;

    public string $hashAlgo = '';


    public string $manual = '';

    public string $homePage = '';

    public string $license = '';

    public string $prefix = '';

    public array $deps = [];

    public string $downloadScript = '';

    public string $downloadDirName = '';

    public bool $enableDownloadScript = false;

    public int $licenseType = self::LICENSE_SPEC;

    public const LICENSE_SPEC = 0;
    public const LICENSE_APACHE2 = 1;
    public const LICENSE_BSD = 2;
    public const LICENSE_GPL = 3;
    public const LICENSE_LGPL = 4;

    public const LICENSE_MIT = 5;

    public const LICENSE_PHP = 6;

    public bool $enableLatestTarball = false;

    public bool $enableHttpProxy = true;

    public bool $enableGitProxy = false;

    public bool $enableDownloadWithMirrorURL = false;

    public bool $enableDownloadWithOriginURL = false;

    public bool $enableBuildCached = true;

    public bool $enableInstallCached = true;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function withLicense(string $license, int $licenseType = self::LICENSE_SPEC): static
    {
        $this->license = $license;
        $this->licenseType = $licenseType;
        return $this;
    }

    public function withHomePage(string $homePage): static
    {
        $this->homePage = $homePage;
        return $this;
    }

    public function withManual(string $manual): static
    {
        $this->manual = $manual;
        return $this;
    }

    public function withDependentLibraries(string ...$libs): static
    {
        $this->deps += $libs;
        return $this;
    }

    public function withMd5sum(string $md5sum): static
    {
        $this->withFileHash('md5', $md5sum);
        return $this;
    }

    /**
     * 配置 文件 签名验证
     * https://www.php.net/manual/zh/function.hash-algos.php
     * print_r(hash_algos());
     * @param string $algo [ 'md5' | 'sha1' | 'sha256' ]
     * @param string $hash
     * @return $this
     */
    public function withFileHash(string $algo, string $hash): static
    {
        $this->hashAlgo = $algo;
        $this->hash = $hash;
        $this->enableHashVerify = true;
        return $this;
    }

    /**
     * hash 签名验证 ，hash 不匹配，删除文件
     * @param string $file
     * @return bool
     */
    public function hashVerify(string $file): bool
    {
        if ($this->enableHashVerify && file_exists($file)) {
            switch ($this->hashAlgo) {
                case 'md5':
                case 'sha1':
                case 'sha256':
                    if (hash_file($this->hashAlgo, $file) === $this->hash) {
                        $this->hashVerify = true;
                    } else {
                        unlink($file);
                    }
                    break;
                default:
                    break;
            }
        }
        return $this->hashVerify;
    }

    public function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function withFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    public function withDownloadScript(string $downloadDirName, string $script): static
    {
        $this->enableDownloadScript = true;
        $this->downloadScript = $script;
        $this->downloadDirName = $downloadDirName;
        return $this;
    }

    public function withAliasName(string $name): static
    {
        $this->aliasName = $name;
        return $this;
    }

    public function withAutoUpdateFile(bool $enableLatestTarball = true): static
    {
        $this->enableLatestTarball = $enableLatestTarball;
        $this->enableBuildCached = false;
        $this->enableInstallCached = false;
        return $this;
    }

    public function withHttpProxy(bool $enableHttpProxy = true, $enableGitProxy = false): static
    {
        $this->enableHttpProxy = $enableHttpProxy;
        $this->enableGitProxy = $enableGitProxy;
        return $this;
    }

    public function withBuildCached(bool $enableBuildCached = true): static
    {
        $this->enableBuildCached = $enableBuildCached;
        return $this;
    }

    public function withInstallCached(bool $enableInstallCached = true): static
    {
        $this->enableInstallCached = $enableInstallCached;
        return $this;
    }

    public function withDownloadWithOriginURL(bool $enableDownloadWithOriginURL = true): static
    {
        $this->enableDownloadWithOriginURL = $enableDownloadWithOriginURL;
        return $this;
    }
}
