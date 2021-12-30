FROM alpine:edge

# setup source repo, install dependencies
RUN echo -ne 'https://mirrors.ustc.edu.cn/alpine/edge/main\nhttps://mirrors.ustc.edu.cn/alpine/edge/community\n' > /etc/apk/repositories && \
 apk update && apk upgrade && \
 apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool

ARG LIBICONV_FILE
ARG OPENSSL_FILE
ARG CURL_FILE

ENV CC=clang
ENV CXX=clang++
ENV LD=ld.lld
#RUN mv /usr/bin/ld /usr/bin/ld.old && ln -s /usr/bin/ld.lld /usr/bin/ld

COPY ./pool/lib/$OPENSSL_FILE  /work/
COPY ./pool/lib/$CURL_FILE /work/
COPY ./pool/lib/$LIBICONV_FILE /work/

# build openssl
RUN mkdir -p /work/openssl && \
 tar --strip-components=1 -C /work/openssl -xf /work/${OPENSSL_FILE} && \
 rm /work/${OPENSSL_FILE} && \
 cd /work/openssl && \
 ./config -static --static no-shared --prefix=/usr/openssl && \
 make -j 8 && make install

# build curl
RUN mkdir -p /work/curl && \
 tar --strip-components=1 -C /work/curl -xf /work/${CURL_FILE} && \
 rm /work/${CURL_FILE} && \
 cd /work/curl && \
 autoreconf -fi && \
 ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl && \
 make -j 8 && make install

# build libiconv
RUN mkdir -p /work/libiconv && \
 tar --strip-components=1 -C /work/libiconv -xf /work/${LIBICONV_FILE} && \
 rm /work/${LIBICONV_FILE} && \
 cd /work/libiconv && \
 ./configure --prefix=/usr enable_static=yes enable_shared=no && \
 make install
