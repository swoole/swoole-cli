<?php

declare(strict_types=1);

namespace SwooleCli\tests;

use PHPUnit\Framework\TestCase;

class gdTest extends TestCase
{

    public function testGd(): void
    {
        $info = gd_info();
        $this->assertEquals($info["FreeType Support"], true);
        $this->assertEquals($info["GIF Read Support"], true);
        $this->assertEquals($info["GIF Create Support"], true);
        $this->assertEquals($info["JPEG Support"], true);
        $this->assertEquals($info["PNG Support"], true);
        $this->assertEquals($info["WBMP Support"], true);

        $this->assertEquals($info["XPM Support"], false);
        $this->assertEquals($info["XBM Support"], true);
        $this->assertEquals($info["WebP Support"], true);

        $this->assertEquals($info["BMP Support"], true);
        $this->assertEquals($info["TGA Read Support"], true);
        $this->assertEquals($info["JIS-mapped Japanese Font Support"], false);
        $this->assertEquals($info["AVIF Support"], true);
    }

    public function testGdAvif(): void
    {
        $origin_image = __DIR__ . '/res/project-0.png';
        $dest_image = __DIR__ . '/../../../test.avif';
        $image = imagecreatefromjpeg($origin_image);
        imageavif($image, $dest_image);
        $this->assertFileExists($dest_image);
    }

}
