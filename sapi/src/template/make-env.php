#!/usr/bin/env bash

export LOGICAL_PROCESSORS=<?= trim($this->logicalProcessors). PHP_EOL ?>
export CMAKE_BUILD_PARALLEL_LEVEL=<?= $this->maxJob. PHP_EOL ?>

export CC=<?= $this->cCompiler . PHP_EOL ?>
export CXX=<?= $this->cppCompiler . PHP_EOL ?>
export LD=<?= $this->lld . PHP_EOL ?>
export PKG_CONFIG_PATH=<?= implode(':', $this->pkgConfigPaths) . PHP_EOL ?>
export PATH=<?= implode(':', $this->binPaths) . PHP_EOL ?>


export GLOBAL_PREFIX="<?= $this->getGlobalPrefix() ?>"
export WORK_DIR="<?= $this->getWorkDir() ?>"
export BUILD_DIR="<?= $this->getBuildDir() ?>"
export ROOT_DIR="<?= $this->getRootDir() ?>"


export PHP_VERSION="<?= BUILD_PHP_VERSION ?>"
export PHP_VERSION_ID=<?= BUILD_PHP_VERSION_ID . PHP_EOL ?>
export PHP_VERSION_TAG="<?= BUILD_PHP_VERSION_TAG ?>"
export CUSTOM_PHP_VERSION_ID=<?= BUILD_CUSTOM_PHP_VERSION_ID . PHP_EOL ?>

