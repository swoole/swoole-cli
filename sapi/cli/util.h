#ifndef PHP_SWOOLE_CLI_UTIL_H
#define PHP_SWOOLE_CLI_UTIL_H

#include "php.h"

#ifndef WORDS_BIGENDIAN
static inline uint32_t swoole_cli_pack_reverse_int32(uint32_t arg)
{
	uint32_t result;
	result = ((arg & 0xFF) << 24) | ((arg & 0xFF00) << 8) | ((arg >> 8) & 0xFF00) | ((arg >> 24) & 0xFF);

	return result;
}

static inline uint64_t swoole_cli_pack_reverse_int64(uint64_t arg)
{
	union Swap64 {
		uint64_t i;
		uint32_t ul[2];
	} tmp, result;
	tmp.i = arg;
	result.ul[0] = swoole_cli_pack_reverse_int32(tmp.ul[1]);
	result.ul[1] = swoole_cli_pack_reverse_int32(tmp.ul[0]);

	return result.i;
}
#endif

#endif /* PHP_SWOOLE_CLI_UTIL_H */
