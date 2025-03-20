<?php

declare(strict_types=1);

namespace SwooleCli\tests;

use PHPUnit\Framework\TestCase;

class intlTest extends TestCase
{

    public function testIntl(): void
    {
        $reflector = new \ReflectionExtension('intl');
        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());
        preg_match('/^ICU version (?:=>)?(.*)$/m', $output, $matches);
        $icuVersion = trim($matches[1]);
        $this->assertNotEmpty($icuVersion);


        $this->assertIsArray(\ResourceBundle::getLocales(''));
        $r = \ResourceBundle::create('root', 'ICUDATA', false);
        $this->assertNotEmpty($r->get("Version"));
        $this->assertNotTrue(
            intl_is_failure($r->getErrorCode()),
            'error_code: ' . $r->getErrorCode() . ':' . $r->getErrorMessage()
        );
    }

}
