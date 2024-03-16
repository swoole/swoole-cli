
apt install -y  php-cli php-pear php-dev php-curl php-intl
apt install -y  php-mbstring  php-tokenizer  php-xml


# apt install -y  php-mysqlnd php-pgsql php-sqlite3  php-redis php-mongodb

curl -Lo  /usr/local/bin/composer.phar https://getcomposer.org/download/latest-stable/composer.phar

ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer
chmod a+x /usr/local/bin/composer
