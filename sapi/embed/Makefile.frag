libphp: libs/libphp.a

# swoole-cli 对 PHP 内核做的 patch/hook，源码虽在 sapi/cli 下，却是 PHP 内核对象所必需：
#   patch.c      定义 opcache_module_entry（空壳模块，真实 opcache 走 zend_extension 注册）
#   hook_phar.c  定义 hook_plain_stream_seek / hook_plain_stream_stat
#                （ext/phar、ext/zip、ext/standard 等通过 hook_stream.h 的宏引用）
#   sfx.c        定义 swoole_cli_get_sfx_filesize（被 hook_phar.c 引用）
PHP_LIBPHP_CLI_OBJS = sapi/cli/patch.lo sapi/cli/sfx/hook_phar.lo sapi/cli/sfx/sfx.lo

# readline_cli.c 引用的 php_cli_get_shell_callbacks（在含 main() 的 php_cli.c 中）
# 由 sapi/embed/php_embed_stub.c 提供桩实现（返回 NULL），因此 readline_cli.o 保留在归档里

libs/libphp.a: $(PHP_GLOBAL_OBJS) $(PHP_BINARY_OBJS) $(PHP_EMBED_OBJS) $(PHP_LIBPHP_CLI_OBJS)
	@$(mkinstalldirs) libs
	rm -f $@
	ar qc $@ $(PHP_GLOBAL_OBJS:.lo=.o) $(PHP_BINARY_OBJS:.lo=.o) $(PHP_EMBED_OBJS:.lo=.o) $(PHP_LIBPHP_CLI_OBJS:.lo=.o)
	ranlib $@
	@echo ""
	@echo "Built $@"
	@echo "  members : $$(ar t $@ | wc -l)"
	@echo "  embed   : $$(nm -g $@ 2>/dev/null | grep -c ' T php_embed_init')"
	@echo ""

clean-libphp:
	rm -f libs/libphp.a

.PHONY: libphp clean-libphp
