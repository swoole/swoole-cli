<?php

namespace SwooleCli;
abstract class Project
{
    public string $name;
    public string $manual = '';

    public string $tutorial = '';
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

    public function withTutorial(string $tutorial): static
    {
        $this->tutorial = $tutorial;
        return $this;
    }

    public function depends(string ...$libs): static
    {
        $this->deps += $libs;
        return $this;
    }
}
