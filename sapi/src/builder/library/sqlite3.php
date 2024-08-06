<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $sqlite3_prefix = SQLITE3_PREFIX;
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
            ->withManual('https://www.sqlite.org/docs.html')
            ->withUrl('https://www.sqlite.org/2023/sqlite-autoconf-3430200.tar.gz')
            ->withFileHash('sha256', '6d422b6f62c4de2ca80d61860e3a3fb693554d2f75bb1aaca743ccc4d6f609f0')
            ->withPrefix($sqlite3_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                # 参考
                # https://github.com/sqlitebrowser/sqlitebrowser/blob/master/.github/workflows/build-appimage.yml#L38
                # CPPFLAGS="-DSQLITE_ENABLE_COLUMN_METADATA=1 -DSQLITE_MAX_VARIABLE_NUMBER=250000 -DSQLITE_ENABLE_RTREE=1 -DSQLITE_ENABLE_GEOPOLY=1 -DSQLITE_ENABLE_FTS3=1 -DSQLITE_ENABLE_FTS3_PARENTHESIS=1 -DSQLITE_ENABLE_FTS5=1 -DSQLITE_ENABLE_STAT4=1 -DSQLITE_ENABLE_JSON1=1 -DSQLITE_SOUNDEX=1 -DSQLITE_ENABLE_MATH_FUNCTIONS=1 -DSQLITE_MAX_ATTACHED=125 -DSQLITE_ENABLE_MEMORY_MANAGEMENT=1 -DSQLITE_ENABLE_SNAPSHOT=1" ./configure --enable-shared=no

                CFLAGS="-DSQLITE_ENABLE_COLUMN_METADATA=1" \
                ./configure \
                --prefix={$sqlite3_prefix} \
                --enable-shared=no \
                --enable-static=yes

EOF
            )
            ->withBinPath($sqlite3_prefix . '/bin/')
            ->withPkgName('sqlite3')
            ->withBinPath($sqlite3_prefix . '/bin/')
    );
    $p->withExportVariable('SQLITE_CFLAGS', '$(pkg-config  --cflags --static sqlite3)');
    $p->withExportVariable('SQLITE_LIBS', '$(pkg-config    --libs   --static sqlite3)');
};
