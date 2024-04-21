<?php

namespace SwooleCli;

abstract class Project
{
    public string $name;

    public string $url;

    public string $path = '';

    public string $file = '';

    public string $md5sum = '';

    public string $sha1 = '';
    public bool $hashVerify = false;

    public bool $enableHashVerify = false;

    public string $hashVerifyMethod = '';


    public string $manual = '';

    public string $homePage = '';

    public string $license = '';

    public string $prefix = '';

    public array $deps = [];

    public int $licenseType = self::LICENSE_SPEC;

    public const LICENSE_SPEC = 0;
    public const LICENSE_APACHE2 = 1;
    public const LICENSE_BSD = 2;
    public const LICENSE_GPL = 3;
    public const LICENSE_LGPL = 4;

    public const LICENSE_MIT = 5;
    public const LICENSE_PHP = 6;

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
        $this->md5sum = $md5sum;
        $this->hashVerifyMethod = 'md5';
        $this->enableHashVerify = true;
        return $this;
    }

    public function withSha1(string $sha1): static
    {
        $this->sha1 = $sha1;
        $this->hashVerifyMethod = 'sha1';
        $this->enableHashVerify = true;
        return $this;
    }

    /*
     * hash 验证 ，hash 不匹配，删除文件
     */
    public function fileHashVerify(string $file): bool
    {
        if ($this->enableHashVerify) {
            switch ($this->hashVerifyMethod) {
                case 'md5':
                    if (md5_file($file) === $this->md5sum) {
                        $this->hashVerify = true;
                    } else {
                        unlink($file);
                    }
                    break;
                case 'sha1':
                    if (sha1_file($file) === $this->sha1) {
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
}
