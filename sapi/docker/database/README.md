# mysql

```shell



# 查看环境变量
# mysqld --verbose --help

# 创建用户
# CREATE USER 'username'@'ipaddress' IDENTIFIED BY 'password';

# 修改mysql用户授权 允许远程连接
# GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

# UPDATE user SET host='%' WHERE user='root';

# FLUSH PRIVILEGES;



# SHOW PROCESSLIST ;
# select user,host,plugin  from mysql.user;

# status
# SHOW STATUS LIKE 'Ssl_cipher';


# CREATE DATABASE my_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


# jdbc:mysql://10.192.99.1:3306?allowPublicKeyRetrieval=true&useSSL=false

```
