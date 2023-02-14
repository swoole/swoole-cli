FROM alpine:edge

# setup source repo, install dependencies
RUN echo -ne 'https://mirrors.ustc.edu.cn/alpine/edge/main\nhttps://mirrors.ustc.edu.cn/alpine/edge/community\n' > /etc/apk/repositories && \
apk update && apk upgrade && \
apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c

ENV CC=clang
ENV CXX=clang++
ENV LD=ld.lld
WORKDIR /work
