__DIR__=$(
  cd "$(dirname "$0")" || exit
  pwd
)

WORKDIR=$(
  cd "${__DIR__}"/../../ || exit
  pwd
)

ORIGIN_SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "sapi/SWOOLE-VERSION.conf")
SWOOLE_VERSION=$(echo "${ORIGIN_SWOOLE_VERSION}" | sed 's/[^a-zA-Z0-9]/_/g')
CURRENT_SWOOLE_VERSION=''

cd "${WORKDIR}" || exit

TGZ_FILE="${WORKDIR}/pool/ext/swoole-${ORIGIN_SWOOLE_VERSION}.tgz"
SWOOLE_DIR="${WORKDIR}/ext/swoole/"

if [ -f "ext/swoole/CMakeLists.txt" ] ;then
    CURRENT_SWOOLE_VERSION=$(grep 'set(SWOOLE_VERSION' ext/swoole/CMakeLists.txt | awk '{ print $2 }' | sed 's/)//')
    if [[ "${CURRENT_SWOOLE_VERSION}" =~ "-dev" ]]; then
        echo 'swoole version master'
        if [ -n "${GITHUB_ACTION}" ]; then
            test -f "$TGZ_FILE" && rm -f "$TGZ_FILE"
            CURRENT_SWOOLE_VERSION=''
        fi
    fi
fi

if [ "${SWOOLE_VERSION}" != "${CURRENT_SWOOLE_VERSION}" ] ;then
    if [ ! -f "$TGZ_FILE" ] ;then
        echo "downloading swoole-${ORIGIN_SWOOLE_VERSION}.tgz"
        test -d /tmp/swoole && rm -rf /tmp/swoole
        git clone -b "${ORIGIN_SWOOLE_VERSION}" https://github.com/swoole/swoole-src.git /tmp/swoole
        status=$?
        if [[ $status -ne 0 ]]; then { echo $status ; exit 1 ; } fi
        cd  /tmp/swoole || exit
        rm -rf /tmp/swoole/.git/
        tar -czvf "$TGZ_FILE" .
    fi

    if [ ! -d "$SWOOLE_DIR" ] ;then
        echo "unpacking swoole-${ORIGIN_SWOOLE_VERSION}.tgz"
        mkdir -p "${SWOOLE_DIR}"
        tar --strip-components=1 -C "${SWOOLE_DIR}" -xf "$TGZ_FILE"
    fi
fi
