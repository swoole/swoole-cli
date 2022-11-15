ROOT=$(pwd)
RE2C_VERSION=3.0

cd /tmp
wget https://github.com/swoole/swoole-cli/releases/download/v5.0.1/re2c.exe

if [ ! -f ./re2c.exe ]; then
    wget https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz
    tar xvf re2c-${RE2C_VERSION}.tar.xz
    cd re2c-${RE2C_VERSION}
    autoreconf -i -W all
    ./configure --prefix=/usr && make -j $(nproc) && make install
else
    mv re2c.exe /usr/bin
fi

cd $ROOT
