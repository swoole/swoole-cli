#ifndef PHP_SWOOLE_CLI_SFX_H
#define PHP_SWOOLE_CLI_SFX_H

#include "php.h"

typedef size_t swoole_cli_sfx_size;

static inline bool swoole_cli_is_file_exec_self(const char *script_file) {
    if (script_file && PG(php_binary)) {
        if (0 == strcmp(PG(php_binary), script_file)) {
            return 1;
        }
        char *ptr = strstr(script_file, "://");
        if (NULL == ptr) {
            return 0;
        }
        return 0 == strcmp(PG(php_binary), ptr + 3);
    }
    return 0;
}

static inline bool swoole_cli_is_stream_exec_self(php_stream *stream) {
    return swoole_cli_is_file_exec_self(stream->orig_path);
}

swoole_cli_sfx_size swoole_cli_get_sfx_filesize(void);

static inline size_t swoole_cli_get_sfx_end_size(void) {
    return sizeof(swoole_cli_sfx_size);
}

#endif /* PHP_SWOOLE_CLI_SFX_H */
