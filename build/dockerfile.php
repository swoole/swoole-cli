<?php
/**
 * @var $this Preprocessor
 */
?>
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

<?php foreach ($this->libraryList as $item) : ?>
COPY ./pool/lib/<?=$item['file']?>  /work/
<?php endforeach; ?>

<?php foreach ($this->libraryList as $item) : ?>
# build [<?=$item['name']?>]
RUN mkdir -p /work/<?=$item['name']?> && \
tar --strip-components=1 -C /work/<?=$item['name']?> -xf /work/<?=$item['file']?> && \
rm /work/<?=$item['file']?> && \
cd /work/<?=$item['name']?> && \
<?=$item['configure']?> --prefix=/usr/<?=$item['name']?> && \
make -j <?=$this->maxJob?> && make install
<?php echo str_repeat(PHP_EOL, 2);?>
<?php endforeach; ?>
