#include "hook_phar.h"

int hook_plain_stream_seek(php_stream *stream, zend_off_t offset, int whence, zend_off_t *newoffset) {
    int ret = -1;
    zend_off_t realoffset;

    switch (whence) {
        case SEEK_SET:
            offset += swoole_cli_get_sfx_filesize();
            break;
        case SEEK_END:
            offset -= swoole_cli_get_sfx_end_size();
            break;
    }
    orig_ops(myops, stream);
    ret = stream->ops->seek(stream, offset, whence, &realoffset);
    ours_ops(stream);
    if (-1 == ret) {
        return -1;
    }
    if (realoffset < swoole_cli_get_sfx_filesize()) {
        php_error_docref(NULL, E_WARNING, "Seek on self stream failed");
        return -1;
    }
    *newoffset = realoffset - swoole_cli_get_sfx_filesize();
    return ret;
}

int hook_plain_stream_stat(php_stream *stream, php_stream_statbuf *ssb) {
    int ret = -1;

    orig_ops(myops, stream);
    ret = stream->ops->stat(stream, ssb);
    ours_ops(stream);
    if (-1 == ret) {
        return -1;
    }
    ssb->sb.st_size -= swoole_cli_get_sfx_filesize() + swoole_cli_get_sfx_end_size();
    return ret;
}
