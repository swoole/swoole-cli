<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $rav1e_prefix = RAV1E_PREFIX;
    $lib = new Library('rav1e');
    $lib->withHomePage('https://github.com/xiph/rav1e.git')
        ->withLicense('https://github.com/xiph/rav1e/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://doc.rust-lang.org/reference/linkage.html')
        ->withManual('https://github.com/xiph/rav1e/blob/master/README.md')
        ->withUrl('https://github.com/xiph/rav1e/archive/refs/tags/v0.6.6.tar.gz')
        ->withFile('rav1e-v0.6.6.tar.gz')
        ->withPrefix($rav1e_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($rav1e_prefix)
        ->withBuildScript(
            <<<EOF
            # export PATH=\$SYSTEM_ORIGIN_PATH
            # export PKG_CONFIG_PATH=\$SYSTEM_ORIGIN_PKG_CONFIG_PATH

            # export PATH=\$SWOOLE_CLI_PATH
            # export PKG_CONFIG_PATH=\$SWOOLE_CLI_PKG_CONFIG_PATH

            rustc -V
            cargo -V
            rustc --print target-cpus
            export RUST_LOG=debug

            cargo cbuild --release \
            --prefix={$rav1e_prefix} \
            --libdir={$rav1e_prefix}/lib \
            -C link-arg=-lz -vv

           # --crate-type=staticlib \

            cargo cinstall --release


EOF
        )
        ->withPkgName('')
        ->withLdflags('')
        ->withPkgConfig('')
        ->withPreInstallCommand(
            <<<EOF
# library rav1e :
curl https://sh.rustup.rs -sSf | bash -s -- --quiet
source root/.cargo/env
export RUSTUP_HOME=/root/.rustup
export CARGO_HOME=/root/.cargo
export PATH=\$PATH:/root/.cargo/bin
rustc -V
cargo -V
# cargo --list
cargo install cargo-c
EOF
        )
    ;

    $p->addLibrary($lib);
};
