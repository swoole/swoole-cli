<?php declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use imagick;
use ImagickPixel;
use ImagickDraw;

final class MainTest extends TestCase
{
    public function testExtesnions(): void
    {
        $exts = get_loaded_extensions();
        $this->assertContains('swoole', $exts);
        $this->assertContains('gd', $exts);
        $this->assertContains('imagick', $exts);
        $this->assertContains('opcache', $exts);
        $this->assertContains('redis', $exts);
        $this->assertContains('mongodb', $exts);
    }

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
    }

    public function testImagick(): void
    {
        $img = new Imagick();
        $bg = new ImagickPixel();
        $bg->setColor('white');
        $ImagickDraw = new ImagickDraw();
        $ImagickDraw->setFont(__DIR__ . '/font.ttf');
        $ImagickDraw->setFontSize(20);

        $alphanum = 'ABXZRMHTL23456789';
        $string = substr(str_shuffle($alphanum), 2, 6);
        $img->newImage(85, 30, $bg);
        $img->annotateImage($ImagickDraw, 4, 20, 0, $string);
        $img->swirlImage(20);

        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));
        $ImagickDraw->line(rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30));

        $img->drawImage($ImagickDraw);
        $this->assertTrue($img->setImageFormat('png'));
        $this->assertTrue($img->setImageFormat('jpeg'));
        $this->assertTrue($img->setImageFormat('webp'));

        $this->assertTrue($img->setCompression(imagick::COMPRESSION_BZIP));
        $this->assertTrue($img->setCompression(imagick::COMPRESSION_ZIP));
    }
}
