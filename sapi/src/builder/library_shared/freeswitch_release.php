<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $lib = new Library('freeswitch_release');
    $lib->withHomePage('https://github.com/signalwire/freeswitch.git')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_GPL)
        ->withManual('https://freeswitch.com/#getting-started')
        ->withManual('https://developer.signalwire.com/freeswitch/FreeSWITCH-Explained/Installation/Linux/Debian_67240088#about')
        ->withSkipDownload()
        ->withBuildCached(false)
        ->withInstallCached(false)
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            # 参考https://github.com/signalwire/freeswitch/blob/master/docker/examples/Debian11/Dockerfile#L6

            export DEBIAN_FRONTEND=noninteractive

            test -f /etc/apt/sources.list.d/freeswitch.list && rm -f /etc/apt/sources.list.d/freeswitch.list
            apt-get update -y
            apt-get install -y lsb-release
            apt-get install -y apt-utils
            # apt-get clean all

EOF
        )
        ->withUntarArchiveCommand('')
        ->withSystemHttpProxy('debian')
        ->withEnv()
        ->withSystemOriginEnvPath()
        ->withBuildScript(
            <<<EOF

        echo "127.0.0.1 \$HOSTNAME" >> /etc/hosts

        # 这里申请 https://id.signalwire.com/personal_access_tokens

        TOKEN=\${FREESWITH_ACCESS_TOKKEN}

        apt-get update  -y
        apt-get install -y gnupg2 wget lsb-release

        if [ ! -f /etc/apt/sources.list.d/freeswitch.list ] ; then

            wget --http-user=signalwire --http-password=\$TOKEN -O /usr/share/keyrings/signalwire-freeswitch-repo.gpg https://freeswitch.signalwire.com/repo/deb/debian-release/signalwire-freeswitch-repo.gpg

            echo "machine freeswitch.signalwire.com login signalwire password \$TOKEN" > /etc/apt/auth.conf
            chmod 600 /etc/apt/auth.conf
            echo "deb [signed-by=/usr/share/keyrings/signalwire-freeswitch-repo.gpg] https://freeswitch.signalwire.com/repo/deb/debian-release/ `lsb_release -sc` main" > /etc/apt/sources.list.d/freeswitch.list
            echo "deb-src [signed-by=/usr/share/keyrings/signalwire-freeswitch-repo.gpg] https://freeswitch.signalwire.com/repo/deb/debian-release/ `lsb_release -sc` main" >> /etc/apt/sources.list.d/freeswitch.list

        fi

        # you may want to populate /etc/freeswitch at this point.
        # if /etc/freeswitch does not exist, the standard vanilla configuration is deployed
        apt-get update && apt-get install -y freeswitch-meta-all

EOF
        );

    $p->addLibrary($lib);
};
