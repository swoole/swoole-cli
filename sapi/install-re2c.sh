ROOT=$(pwd)
cd /tmp
RE2C_VERSION=3.0
wget https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz
tar xvf re2c-${RE2C_VERSION}.tar.xz
cd re2c-${RE2C_VERSION}
autoreconf -i -W all
./configure --prefix=/usr && make -j $(nproc) && make install
cd $ROOT
