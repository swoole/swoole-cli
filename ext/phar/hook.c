#include <hook.h>

size_t get_sfx_filesize(void) {
    static int sfx_filesize_inited = 0;
    static size_t sfx_filesize = 0;
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
        size_t script_size;
        php_stream_seek(stream, file_size - sizeof(script_size), SEEK_SET);
        php_stream_read(stream, (char *) &script_size, sizeof(script_size));
#ifndef WORDS_BIGENDIAN
        script_size = stream_pack_reverse_int64(script_size);
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

int hook_plain_stream_seek(php_stream *stream, zend_off_t offset, int whence, zend_off_t *newoffset) {
    printf("hook_plain_stream_seek, offset=%ld\n", offset);
    int ret = -1;
    zend_off_t realoffset;

    orig_ops(myops, stream);
    if (offset < 0) {
        offset -= get_sfx_end_size();
    } else {
        offset += get_sfx_filesize();
    }
    if (SEEK_SET == whence) {
        ret = stream->ops->seek(stream, offset, whence, &realoffset);
    } else {
        ret = stream->ops->seek(stream, offset, whence, &realoffset);
    }
    ours_ops(stream);
    if (-1 == ret) {
        return -1;
    }
    if (realoffset < get_sfx_filesize()) {
        php_error_docref(NULL, E_WARNING, "Seek on self stream failed");
        return -1;
    }
    *newoffset = realoffset - get_sfx_filesize();
    return ret;
}

int hook_plain_stream_stat(php_stream *stream, php_stream_statbuf *ssb) {
    printf("hook_plain_stream_stat\n");
    int ret = -1;

    orig_ops(myops, stream);
    ret = stream->ops->stat(stream, ssb);
    ours_ops(stream);
    if (-1 == ret) {
        return -1;
    }
    ssb->sb.st_size -= get_sfx_filesize() + get_sfx_end_size();
    return ret;
}
