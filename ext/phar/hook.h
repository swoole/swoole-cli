#include "php.h"

#ifndef WORDS_BIGENDIAN
static inline uint32_t stream_pack_reverse_int32(uint32_t arg)
{
	uint32_t result;
	result = ((arg & 0xFF) << 24) | ((arg & 0xFF00) << 8) | ((arg >> 8) & 0xFF00) | ((arg >> 24) & 0xFF);

	return result;
}

static inline uint64_t stream_pack_reverse_int64(uint64_t arg)
{
	union Swap64 {
		uint64_t i;
		uint32_t ul[2];
	} tmp, result;
	tmp.i = arg;
	result.ul[0] = stream_pack_reverse_int32(tmp.ul[1]);
	result.ul[1] = stream_pack_reverse_int32(tmp.ul[0]);

	return result.i;
}
#endif

static inline bool is_file_exec_self(const char *script_file) {
	printf("is_file_exec_self, script_file=%s\n", script_file);
    if (script_file && PG(php_binary)) {
        printf("PG(php_binary)=%s\n", PG(php_binary));
        if (0 == strcmp(PG(php_binary), script_file)) {
            return 1;
        }
        char *ptr = strstr(script_file, "://");
        if (NULL == ptr) {
            return 0;
        }
        printf("ptr=%s, ptr+3=%s\n", ptr, ptr + 3);
        return 0 == strcmp(PG(php_binary), ptr + 3);
    }
    return 0;
}

static inline bool is_stream_exec_self(php_stream *stream) {
    return is_file_exec_self(stream->orig_path);
}

size_t get_sfx_filesize(void);

static inline size_t get_sfx_end_size(void) {
    return sizeof(size_t);
}

static inline int init_phar_stream_seek(php_stream *ps) {
    zend_off_t dummy;
    if (0 == ps->position) {
        printf("seek=0\n");
        // not appending mode
        ps->ops->seek(ps, 0, SEEK_SET, &dummy);
    } else if (0 < ps->position) {
        // appending mode
        // this will only called after micro_fileinfo_init,
        //  so it's sure thatself size wont be smaller then sfx size.
        printf("is_stream_exec_self-1\n");
        ps->position -= (is_stream_exec_self(ps) ? get_sfx_filesize() : 0) + get_sfx_end_size();
        ps->ops->seek(ps, ps->position, SEEK_SET, &dummy);
        printf("seek=%ld\n", ps->position);
    } else {
        // self should be seekable, if not, why?
        abort();
    }
    return SUCCESS;
}

// use original ops as ps->ops
#define orig_ops(myops, ps) \
    const php_stream_ops *myops = ps->ops; \
    ps->ops = ((const hook_php_stream_ops *)(ps->ops))->ops_orig;
// use with-offset ops as ps->ops
#define ours_ops(ps) ps->ops = myops;

typedef struct _hook_php_stream_ops {
    php_stream_ops ops;
    const php_stream_ops *ops_orig;
} hook_php_stream_ops;

int hook_plain_stream_seek(php_stream *stream, zend_off_t offset, int whence, zend_off_t *newoffset);

int hook_plain_stream_stat(php_stream *stream, php_stream_statbuf *ssb);
