#!/bin/bash
set -x
{
docker stop download-box-web-server

} || {
echo $?
}
