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

depends:
<?php foreach($this->nfpmDepends['deb'] as $pkg => $version) : ?>
    - <?=$pkg?> <?php if ($version) : ?>(<?=$version?>)<?php endif; ?> <?=PHP_EOL?>
<?php endforeach; ?>

contents:
    - src: "bin/swoole-cli"
      dst: "/usr/local/bin/swoole-cli"
