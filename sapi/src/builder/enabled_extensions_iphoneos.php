<?php

// Keep the first iPhoneOS profile intentionally small. Server/process/JIT
// extensions add portability and App Store risks without helping the mobile
// runtime. GMP also pulls MPFR into the self-contained libphp.a.
return [
    'bcmath',
    'ctype',
    'filter',
    'gmp',
];
