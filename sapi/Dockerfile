FROM alpine:edge

# setup source repo, install dependencies
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
RUN apk update --allow-untrusted
RUN apk upgrade --allow-untrusted
RUN apk add --allow-untrusted vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c
RUN apk add --allow-untrusted bash
WORKDIR /work
