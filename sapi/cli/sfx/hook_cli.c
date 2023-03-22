#include "hook_cli.h"
#include "sapi/cli/util.h"
#include "sfx.h"

static size_t zend_stream_fsize(zend_file_handle *file_handle) /* {{{ */
{
	ZEND_ASSERT(file_handle->type == ZEND_HANDLE_STREAM);
	if (file_handle->handle.stream.isatty) {
		return 0;
	}
	return file_handle->handle.stream.fsizer(file_handle->handle.stream.handle);
} /* }}} */

static int zend_stream_getc(zend_file_handle *file_handle) /* {{{ */
{
	char buf;

	if (file_handle->handle.stream.reader(file_handle->handle.stream.handle, &buf, sizeof(buf))) {
		return (int)buf;
	}
	return EOF;
} /* }}} */

static ssize_t zend_stream_read(zend_file_handle *file_handle, char *buf, size_t len) /* {{{ */
{
	if (file_handle->handle.stream.isatty) {
		int c = '*';
		size_t n;

		for (n = 0; n < len && (c = zend_stream_getc(file_handle)) != EOF && c != '\n'; ++n)  {
			buf[n] = (char)c;
		}
		if (c == '\n') {
			buf[n++] = (char)c;
		}

		return n;
	}
	return file_handle->handle.stream.reader(file_handle->handle.stream.handle, buf, len);
} /* }}} */

static ssize_t zend_stream_stdio_reader(void *handle, char *buf, size_t len) /* {{{ */
{
	return fread(buf, 1, len, (FILE*)handle);
} /* }}} */

static void zend_stream_stdio_closer(void *handle) /* {{{ */
{
	if (handle && (FILE*)handle != stdin) {
		fclose((FILE*)handle);
	}
} /* }}} */

static size_t zend_stream_stdio_fsizer(void *handle) /* {{{ */
{
	zend_stat_t buf;
	if (handle && zend_fstat(fileno((FILE*)handle), &buf) == 0) {
#ifdef S_ISREG
		if (!S_ISREG(buf.st_mode)) {
			return 0;
		}
#endif
		return buf.st_size;
	}
	return -1;
} /* }}} */

int zend_stream_init_fp_self_begin(zend_file_handle *handle, FILE *fp, const char *filename) {
	size_t file_size, offset;
	swoole_cli_sfx_size script_size;
    char *buf = NULL;

	memset(handle, 0, sizeof(zend_file_handle));
	handle->handle.fp = fp;
	handle->filename = filename ? zend_string_init(filename, strlen(filename), 0) : NULL;
	handle->type = ZEND_HANDLE_STREAM;
	handle->handle.stream.handle = handle->handle.fp;
	handle->handle.stream.isatty = isatty(fileno((FILE *)handle->handle.stream.handle));
	handle->handle.stream.reader = (zend_stream_reader_t)zend_stream_stdio_reader;
	handle->handle.stream.closer = (zend_stream_closer_t)zend_stream_stdio_closer;
	handle->handle.stream.fsizer = (zend_stream_fsizer_t)zend_stream_stdio_fsizer;
    file_size = zend_stream_fsize(handle);
    if (file_size == (size_t) -1) {
        return FAILURE;
    }
	fseek(fp, file_size - sizeof(script_size), SEEK_SET);
	zend_stream_read(handle, (char *) &script_size, sizeof(script_size));
#ifndef WORDS_BIGENDIAN
	script_size = swoole_cli_pack_reverse_int64(script_size);
#endif
    if (file_size < script_size + sizeof(script_size)) {
        return FAILURE;
    }
	offset = file_size - script_size - sizeof(script_size);
	fseek(fp, offset, SEEK_SET);
	if (script_size) {
		ssize_t read;
		size_t size = 0;
		buf = safe_emalloc(1, script_size, ZEND_MMAP_AHEAD);
		while ((read = zend_stream_read(handle, buf + size, script_size - size)) > 0) {
			size += read;
		}
		if (read < 0) {
			efree(buf);
			return FAILURE;
		}
		handle->buf = buf;
		handle->len = size;
	} else {
		size_t size = 0, remain = 4*1024;
		ssize_t read;
		buf = emalloc(remain);

		while ((read = zend_stream_read(handle, buf + size, remain)) > 0) {
			size   += read;
			remain -= read;

			if (remain == 0) {
				buf   = safe_erealloc(buf, size, 2, 0);
				remain = size;
			}
		}
		if (read < 0) {
			efree(buf);
			return FAILURE;
		}

		handle->len = size;
		if (size && remain < ZEND_MMAP_AHEAD) {
			buf = safe_erealloc(buf, size, 1, ZEND_MMAP_AHEAD);
		}
		handle->buf = buf;
	}

	if (handle->len == 0) {
		buf = erealloc(buf, ZEND_MMAP_AHEAD);
		handle->buf = buf;
	}

	memset(handle->buf + handle->len, 0, ZEND_MMAP_AHEAD);
	return SUCCESS;
}

int swoole_cli_seek_file_self_begin(zend_file_handle *file_handle, char *script_file)
{
	FILE *fp = VCWD_FOPEN(script_file, "rb");
	if (!fp) {
		php_printf("Could not open input file: %s\n", script_file);
		return FAILURE;
	}

    int result = zend_stream_init_fp_self_begin(file_handle, fp, script_file);
	if (SUCCESS != result) {
        return result;
    }
	file_handle->primary_script = 1;
	return SUCCESS;
}
