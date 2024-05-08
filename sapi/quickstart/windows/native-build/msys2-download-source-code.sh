#!/usr/bin/env bash
set -x

git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git
git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git

