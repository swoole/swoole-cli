FROM alpine:edge

# setup source repo, install dependencies
RUN echo -ne 'https://mirrors.ustc.edu.cn/alpine/edge/main\nhttps://mirrors.ustc.edu.cn/alpine/edge/community\n' > /etc/apk/repositories && \
apk update && apk upgrade && \
apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool

ENV CC=clang
ENV CXX=clang++
ENV LD=ld.lld
#RUN mv /usr/bin/ld /usr/bin/ld.old && ln -s /usr/bin/ld.lld /usr/bin/ld
