make -C src/bin install
make -C src/include install
make -C src/interfaces install
make -C src/common install
make -C src/port install
make -C doc install

# 参考文档
# https://www.postgresql.org/docs/current/install-make.html#INSTALL-PROCEDURE-MAKE
# https://www.postgresql.org/docs/16/install-make.html
# https://www.postgresql.org/docs/15/install-procedure.html#CONFIGURE-OPTIONS
# https://www.postgresql.org/docs/
