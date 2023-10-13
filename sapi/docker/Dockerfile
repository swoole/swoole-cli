FROM alpine:edge

ARG MIRROR=""

# setup source repo, install dependencies
RUN test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
RUN if [ "${MIRROR}" = "ustc" -o "${MIRROR}" = "china"   ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories ; } fi
RUN if [ "${MIRROR}" = "tuna" ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories ; } fi

RUN apk update --allow-untrusted
RUN apk upgrade --allow-untrusted
RUN apk add --allow-untrusted vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c gettext coreutils
RUN apk add --allow-untrusted bash
RUN apk add --allow-untrusted libc++-static
RUN apk add --allow-untrusted tini

WORKDIR /work
