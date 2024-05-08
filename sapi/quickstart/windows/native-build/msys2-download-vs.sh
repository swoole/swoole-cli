#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}

unset http_proxy
unset https_proxy

# curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
# curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'
