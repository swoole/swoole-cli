#ifndef PHP_SWOOLE_CLI_HOOK_STREAM_H
#define PHP_SWOOLE_CLI_HOOK_STREAM_H

#include "php.h"
#include "sfx.h"
#include "hook_phar.h"

#define php_stream_open_wrapper(path, mode, options, opened)	swoole_cli_stream_open_wrapper((path), (mode), (options), (opened))

static inline void swoole_cli_hook_stream(php_stream *stream)
{
	if (swoole_cli_is_stream_exec_self(stream)) {
	    swoole_cli_hook_php_stream_ops *hook_ops = NULL;
		hook_ops = pemalloc(sizeof(swoole_cli_hook_php_stream_ops), stream->is_persistent);
		hook_ops->ops_orig = stream->ops;
		memcpy(&hook_ops->ops, stream->ops, sizeof(php_stream_ops));
		hook_ops->ops.seek = hook_plain_stream_seek;
		hook_ops->ops.stat = hook_plain_stream_stat;
		stream->ops = (php_stream_ops *) hook_ops;
	}
}

static inline void swoole_cli_unhook_stream(php_stream *stream)
{
	if (swoole_cli_is_stream_exec_self(stream)) {
        swoole_cli_hook_php_stream_ops *hook_ops = (swoole_cli_hook_php_stream_ops *) stream->ops;
        stream->ops = hook_ops->ops_orig;
        pefree(hook_ops, stream->is_persistent);
    }
}

static inline php_stream *swoole_cli_stream_open_wrapper(const char *path, const char *mode, int options, zend_string **opened_path)
{
    php_stream *stream = _php_stream_open_wrapper_ex(path, mode, options, opened_path, NULL STREAMS_CC);
    if (!stream) {
        return NULL;
    }
	swoole_cli_hook_stream(stream);

    return stream;
}

#endif /* PHP_SWOOLE_CLI_HOOK_STREAM_H */
