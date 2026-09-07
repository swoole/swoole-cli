<?php

// Keep the first iPhoneOS profile intentionally small. TypePHP applications
// are AOT compiled, so server/process/JIT extensions add portability and App
// Store risks without helping the mobile runtime. GMP also pulls MPFR into the
// SDK for PHPX's BigInt/BigFloat implementation.
return [
    'bcmath',
    'ctype',
    'filter',
    'gmp',
];
