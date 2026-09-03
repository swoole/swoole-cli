libphp: libs/libphp.a

libs/libphp.a: $(PHP_GLOBAL_OBJS) $(PHP_BINARY_OBJS) $(PHP_EMBED_OBJS)
	@$(mkinstalldirs) libs
	rm -f $@
	ar qc $@ $(PHP_GLOBAL_OBJS:.lo=.o) $(PHP_BINARY_OBJS:.lo=.o) $(PHP_EMBED_OBJS:.lo=.o)
	ranlib $@
	@echo ""
	@echo "Built $@"
	@echo "  members : $$(ar t $@ | wc -l)"
	@echo "  embed   : $$(nm -g $@ 2>/dev/null | grep -c ' T php_embed_init')"
	@echo ""

clean-libphp:
	rm -f libs/libphp.a

.PHONY: libphp clean-libphp
