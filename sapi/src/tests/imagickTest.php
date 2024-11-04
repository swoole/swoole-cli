<?php

declare(strict_types=1);

namespace SwooleCli\tests;

use PHPUnit\Framework\TestCase;
use Imagick;
use ImagickDraw;
use ImagickPixel;

class imagickTest extends TestCase
{

    public function testImagick(): void
    {
        $img = new Imagick();
        $bg = new ImagickPixel();
        $bg->setColor('white');
        $ImagickDraw = new ImagickDraw();
        $ImagickDraw->setFont(__DIR__ . '/res/font.ttf');
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

        $this->assertTrue($img->setCompression(Imagick::COMPRESSION_BZIP));
        $this->assertTrue($img->setCompression(Imagick::COMPRESSION_ZIP));
    }

}
