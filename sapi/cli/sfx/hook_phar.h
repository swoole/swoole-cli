#ifndef PHP_SWOOLE_CLI_HOOK_PHAR_H
#define PHP_SWOOLE_CLI_HOOK_PHAR_H

#include "php.h"
#include "sfx.h"

// use original ops as ps->ops
#define orig_ops(myops, ps) \
    const php_stream_ops *myops = ps->ops; \
    ps->ops = ((const swoole_cli_hook_php_stream_ops *)(ps->ops))->ops_orig;
// use with-offset ops as ps->ops
#define ours_ops(ps) ps->ops = myops;
#define ret_orig(rtyp, name, stream, args) \
    do { \
        orig_ops(myops, stream); \
        rtyp ret = stream->ops->name(stream args); \
        ours_ops(stream); \
        return ret; \
    } while (0)
#define with_args(...) , __VA_ARGS__
#define nope

typedef struct _swoole_cli_hook_php_stream_ops {
    php_stream_ops ops;
    const php_stream_ops *ops_orig;
} swoole_cli_hook_php_stream_ops;

int hook_plain_stream_seek(php_stream *stream, zend_off_t offset, int whence, zend_off_t *newoffset);

int hook_plain_stream_stat(php_stream *stream, php_stream_statbuf *ssb);

#endif /* PHP_SWOOLE_CLI_HOOK_PHAR_H */
