/*
  +----------------------------------------------------------------------+
  | embed SAPI 的 CLI 符号桩                                              |
  +----------------------------------------------------------------------+
  | Copyright (c) The PHP Group                                          |
  +----------------------------------------------------------------------+
 */

/*
 * ext/readline/readline_cli.c 里的 GET_SHELL_CB 宏会调用
 * sapi/cli/php_cli.c 的 php_cli_get_shell_callbacks()（CLI 交互 shell 的回调表）。
 *
 * 但 php_cli.c 含有 main()，不能打进 libphp.a；而 readline_cli.c 又是
 * readline 扩展的一部分（readline.c 会引用它的 MINIT/MSHUTDOWN/MINFO），
 * 也无法从归档中排除。因此这里提供一个桩实现：embed 下没有 CLI 交互 shell，
 * 返回 NULL 让 readline_cli 跳过回调注册（不影响 readline 扩展的核心功能）。
 */

#include "php.h"
#include "sapi/cli/cli.h"

PHP_CLI_API cli_shell_callbacks_t *php_cli_get_shell_callbacks(void)
{
    return NULL;
}
