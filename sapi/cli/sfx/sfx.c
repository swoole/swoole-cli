#include "sfx.h"
#include "sapi/cli/util.h"

swoole_cli_sfx_size swoole_cli_get_sfx_filesize(void) {
    static int sfx_filesize_inited = 0;
    static swoole_cli_sfx_size sfx_filesize = 0;
    if (!sfx_filesize_inited) {
        if (!PG(php_binary)) {
            return 0;
        }
        php_stream *stream = php_stream_open_wrapper(PG(php_binary), "rb", 0, NULL);
        php_stream_statbuf ssb;
        php_stream_stat(stream, &ssb);
        size_t file_size = (size_t) ssb.sb.st_size;
        if (file_size == (size_t) -1) {
            php_stream_close(stream);
            return 0;
        }
        swoole_cli_sfx_size script_size;
        php_stream_seek(stream, file_size - sizeof(script_size), SEEK_SET);
        php_stream_read(stream, (char *) &script_size, sizeof(script_size));
#ifndef WORDS_BIGENDIAN
        script_size = swoole_cli_pack_reverse_int64(script_size);
#endif
        if (file_size < script_size + sizeof(script_size)) {
            php_stream_close(stream);
            return 0;
        }
        sfx_filesize = file_size - script_size - sizeof(script_size);
        sfx_filesize_inited = 1;
        php_stream_close(stream);
    }
    return sfx_filesize;
}
