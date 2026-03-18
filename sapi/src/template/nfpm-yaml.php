name: "swoole-cli"
arch: "<?=$this->getDebArch()?>"
platform: "<?=$this->getOsType()?>"
version: "<?=$this->getSwooleVersion()?>"
section: "default"
priority: "extra"
maintainer: "service <service@swoole.com>"
description: |
    SWOOLE-CLI is a php binary distribution composed swoole & php-core & cli & fpm and mostly of common extensions
homepage: "https://github.com/swoole/swoole-cli"

overrides:
    deb:
        depends:
            - libc6 (>=2.35)
    rpm:
        depends:
            - glibc >= 2.35

contents:
    - src: "bin/swoole-cli"
      dst: "/usr/local/bin/swoole-cli"
    - src: "runtime/libs/"
      dst: "/usr/local/swoole-cli/lib/"
