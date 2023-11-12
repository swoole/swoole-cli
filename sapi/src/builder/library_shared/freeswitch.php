<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freeswitch_prefix = FREESWITCH_PREFIX;
    $odbc_prefix = UNIX_ODBC_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;

    // 参考： https://github.com/signalwire/freeswitch/tree/master/docker

    $lib = new Library('freeswitch');
    $lib->withHomePage('https://github.com/signalwire/freeswitch.git')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_GPL)
        ->withManual('https://freeswitch.com/#getting-started')
        ->withManual('https://developer.signalwire.com/freeswitch/FreeSWITCH-Explained/Installation/Linux/Debian_67240088#about')
        ->withFile('freeswitch-latest.tar.gz')
        //->withAutoUpdateFile()
        ->withDownloadScript(
            'freeswitch',
            <<<EOF
            git clone -b master  --depth=1 https://github.com/signalwire/freeswitch.git
EOF
        )
        ->withPrefix($freeswitch_prefix)
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            # 参考https://github.com/signalwire/freeswitch/blob/master/docker/examples/Debian11/Dockerfile#L6

            export DEBIAN_FRONTEND=noninteractive

            apt-get update -y
            apt-get install -y lsb-release
            apt-get install -y apt-utils
            apt-get clean all


            # build
               apt install -y  build-essential cmake automake autoconf libtool-bin libtool pkg-config
            # general
               apt install -y  libssl-dev zlib1g-dev libdb-dev unixodbc-dev libncurses5-dev libexpat1-dev libgdbm-dev bison erlang-dev libtpl-dev libtiff5-dev uuid-dev
            # core
               apt install -y  libpcre3-dev libedit-dev libsqlite3-dev libcurl4-openssl-dev nasm
            # core codecs
               apt install -y  libogg-dev libspeex-dev libspeexdsp-dev
            # mod_enum
               apt install -y  libldns-dev
            # mod_python3
               apt install -y  python3-dev
            # mod_av
               apt install -y  libavformat-dev libswscale-dev   # libavresample-dev
            # mod_lua
               apt install -y  liblua5.2-dev
            # mod_opus
               apt install -y  libopus-dev
            # mod_pgsql
               apt install -y  libpq-dev
            # mod_sndfile
               apt install -y  libsndfile1-dev libflac-dev libogg-dev libvorbis-dev
            # mod_shout
               apt install -y  libshout3-dev libmpg123-dev libmp3lame-dev



EOF
        )
        ->withBuildScript(
            <<<EOF


EOF
        )

    ;

    $p->addLibrary($lib);
};
