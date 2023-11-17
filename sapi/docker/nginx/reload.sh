#!/in/env bash

set -eux
__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__} &&

docker exec -it web /bin/sh -c 'id -u && ps -ef && nginx -t'

while true
do
	read -r -p "Are You Sure? [Y/n] " input

	case $input in
	    [yY][eE][sS]|[yY])
			echo "Yes"
			    docker exec -it web /bin/sh -c 'nginx -s reload'
			    exit;
			;;

	    [nN][oO]|[nN])
			echo "No"
			exit 0
			;;

	    *)
			echo "Invalid input..."

			;;
	esac
done




