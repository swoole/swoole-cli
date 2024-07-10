ARG BASE_IMAGE="alpine:3.18"
FROM ${BASE_IMAGE}
# FROM alpine:3.18


ARG MIRROR=""


# setup source repo, install dependencies
RUN test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
RUN if [ "${MIRROR}" = "ustc" -o "${MIRROR}" = "china"   ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories ; } fi
RUN if [ "${MIRROR}" = "tuna" ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories ; } fi

RUN apk update
RUN apk upgrade

RUN apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c gettext coreutils
RUN apk add bash tini
RUN apk add libc++-static libltdl-static
RUN apk add gettext-dev
RUN apk add pigz parallel
WORKDIR /work
