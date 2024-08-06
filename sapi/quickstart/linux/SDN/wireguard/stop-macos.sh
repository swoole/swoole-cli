#!/bin/bash
set -exu

__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}


wg-quick down wg0
