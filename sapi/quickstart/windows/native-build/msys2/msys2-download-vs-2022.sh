#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../../
  pwd
)
cd ${__PROJECT__}

unset HTTP_PROXY
unset HTTPS_PROXY
unset NO_PROXY

curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
# curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
# curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'

test -f vc_redist.x64.exe || curl -Lo vc_redist.x64.exe https://aka.ms/vs/17/release/vc_redist.x64.exe
test -f vc_redist.x86.exe || curl -Lo vc_redist.x86.exe https://aka.ms/vs/17/release/vc_redist.x86.exe

