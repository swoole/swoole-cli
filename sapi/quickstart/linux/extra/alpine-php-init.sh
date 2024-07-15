apk add php82-cli php82-dev
apk add php82-iconv php82-mbstring php82-phar php82-openssl
apk add php82-posix php82-tokenizer php82-intl
apk add php82-dom php82-xmlwriter php82-xml php82-simplexml
apk add php82-pdo php82-sockets php82-curl php82-mysqlnd php82-pgsql php82-sqlite3
apk add php82-redis php82-mongodb

php82 -v
php82 --ini
php82 --ini | grep ".ini files"

ln -sf /usr/bin/php82 /usr/bin/php
ln -sf /usr/bin/phpize82 /usr/bin/phpize
ln -sf /usr/bin/php-config82 /usr/bin/php-config
