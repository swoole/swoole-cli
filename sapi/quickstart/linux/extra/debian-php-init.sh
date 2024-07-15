export TZ="Etc/UTC"
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ >/etc/timezone
apt update -y
apt install -y php-cli php-pear php-dev php-curl php-intl
apt install -y php-mbstring php-tokenizer php-xml
apt install -y php-mysqlnd php-pgsql php-sqlite3 php-redis php-mongodb
