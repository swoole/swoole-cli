#!/usr/bin/env bash

umask 077
wg genkey > privatekey
wg pubkey < privatekey > publickey
wg genkey | tee privatekey | wg pubkey > publickey