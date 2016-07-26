<?php

namespace ride\library\image;

use ride\library\image\color\HslColor;
use ride\library\image\color\RgbColor;
use ride\library\image\dimension\Dimension;
use ride\library\image\point\Point;

use \PHPUnit_Framework_TestCase;

class GenericImageFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerCreateImage
     */
    public function testCreateImage($className, $library) {
        $factory = new GenericImageFactory($library);
        $image = $factory->createImage();

        $this->assertEquals(get_class($image), $className);
    }

    public function providerCreateImage() {
        return array(
            array('ride\\library\\image\\GdImage', 'gd'),
            array('ride\\library\\image\\ImagickImage', 'imagick'),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidLibraryProvided
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructThrowsExceptionWhenInvalidLibraryProvided($library) {
        new GenericImageFactory($library);
    }

    public function providerConstructThrowsExceptionWhenInvalidLibraryProvided() {
        return array(
            array(true),
            array('test'),
            array(array()),
            array($this),
        );
    }

    public function testCreateDimension() {
        $width = 100;
        $height = 50;
        $factory = new GenericImageFactory('gd');

        $dimension = $factory->createDimension($width, $height);

        $this->assertTrue($dimension instanceof Dimension);
        $this->assertEquals($width, $dimension->getWidth());
        $this->assertEquals($height, $dimension->getHeight());
    }

    public function testCreatePoint() {
        $x = 100;
        $y = 50;
        $factory = new GenericImageFactory('gd');

        $point = $factory->createPoint($x, $y);

        $this->assertTrue($point instanceof Point);
        $this->assertEquals($x, $point->getX());
        $this->assertEquals($y, $point->getY());
    }

    public function testHtmlColor() {
        $htmlColor = '#336699';
        $factory = new GenericImageFactory('gd');

        $color = $factory->createHtmlColor($htmlColor);

        $this->assertTrue($color instanceof RgbColor);
        $this->assertEquals(51, $color->getRed());
        $this->assertEquals(102, $color->getGreen());
        $this->assertEquals(153, $color->getBlue());
    }

    public function testCreateRgbColor() {
        $red = 50;
        $green = 100;
        $blue = 150;
        $alpha = 0.75;
        $factory = new GenericImageFactory('gd');

        $color = $factory->createRgbColor($red, $green, $blue, $alpha);

        $this->assertTrue($color instanceof RgbColor);
        $this->assertEquals($red, $color->getRed());
        $this->assertEquals($green, $color->getGreen());
        $this->assertEquals($blue, $color->getBlue());
        $this->assertEquals($alpha, $color->getAlpha());
    }

    public function testCreateHslColor() {
        $hue = 270;
        $saturation = 0.7;
        $lightness = 0.3;
        $alpha = 0.75;
        $factory = new GenericImageFactory('gd');

        $color = $factory->createHslColor($hue, $saturation, $lightness, $alpha);

        $this->assertTrue($color instanceof HslColor);
        $this->assertEquals($hue, $color->getHue());
        $this->assertEquals($saturation, $color->getSaturation());
        $this->assertEquals($lightness, $color->getLightness());
        $this->assertEquals($alpha, $color->getAlpha());
    }

}
