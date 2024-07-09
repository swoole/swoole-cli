
## 调试 PHP
```bash

algrind --leak-check=full --error-exitcode=1 --show-possibly-lost=no --read-inline-info=yes --keep-debuginfo=yes --undef-value-errors=no  php your_file.php

USE_ZEND_ALLOC=0 valgrind --log-file=/tmp/valgrind.log php your_file.php

gdb --args php your_file.php

```
