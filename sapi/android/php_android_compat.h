#ifndef TYPEPHP_PHP_ANDROID_COMPAT_H
#define TYPEPHP_PHP_ANDROID_COMPAT_H

#if defined(__ANDROID__) && !defined(__ASSEMBLER__)
#include <limits.h>
#include <unistd.h>

/* Bionic does not expose the BSD getdtablesize() API used by php://fd. */
static inline int typephp_android_getdtablesize(void)
{
    long value = sysconf(_SC_OPEN_MAX);
    return value > 0 && value <= INT_MAX ? (int) value : INT_MAX;
}

#define getdtablesize typephp_android_getdtablesize
#endif

#endif
