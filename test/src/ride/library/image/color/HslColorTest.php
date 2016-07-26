<?php

namespace ride\library\image\color;

use \PHPUnit_Framework_TestCase;

class HslColorTest extends PHPUnit_Framework_TestCase {

    public function testHsl() {
        $hue = 180;
        $saturation = 0.3;
        $lightness = 0.7;
        $alpha = 0.5;

        $color = new HslColor($hue, $saturation, $lightness, $alpha);

        $this->assertEquals($hue, $color->getHue());
        $this->assertEquals($saturation, $color->getSaturation());
        $this->assertEquals($lightness, $color->getLightness());
        $this->assertEquals($alpha, $color->getAlpha());

        $hue = 270;

        $color = $color->setHue($hue)->setSaturation($lightness)->setLightness($alpha)->setAlpha($saturation);

        $this->assertEquals($hue, $color->getHue());
        $this->assertEquals($lightness, $color->getSaturation());
        $this->assertEquals($alpha, $color->getLightness());
        $this->assertEquals($saturation, $color->getAlpha());
    }

    /**
     * @dataProvider providerInvalidHueValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructHueThrowsExceptionWhenInvalidValueProvided($value) {
        new HslColor($value, 0.5, 0.5);
    }

    /**
     * @dataProvider providerInvalidHueValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetHueThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new HslColor(0.5, 0.5, 0.5);
        $color->setHue($value);
    }

    public function providerInvalidHueValue() {
        return array(
            array(null),
            array(-1),
            array('test'),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructSaturationThrowsExceptionWhenInvalidValueProvided($value) {
        new HslColor(0.5, $value, 0.5);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetSaturationThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new HslColor(0.5, 0.5, 0.5);
        $color->setSaturation($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructLightnessThrowsExceptionWhenInvalidValueProvided($value) {
        new HslColor(0.5, 0.5, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetLightnessThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new HslColor(0.5, 0.5, 0.5);
        $color->setLightness($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructAlphaThrowsExceptionWhenInvalidValueProvided($value) {
        new HslColor(360, 1, 1, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetAlphaThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new HslColor(360, 1, 1, 1);
        $color->setAlpha($value);
    }

    public function providerInvalidValue() {
        return array(
            array(null),
            array(-1),
            array(256),
            array('test'),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerToString
     */
    public function testToString($expected, $hue, $saturation, $lightness, $alpha = 1) {
        $color = new HslColor($hue, $saturation, $lightness, $alpha);

        $this->assertEquals($expected, (string) $color);
    }

    public function providerToString() {
        return array(
            array('HSL(360,0,0)', 360, 0, 0),
            array('HSL(255,0.3,0.7)', 255, 0.3, 0.7),
            array('HSLA(140,0.1,0.1,0.7)', 140, 0.1, 0.1, 0.7),
        );
    }

    /**
     * @dataProvider providerGetHtmlColor
     */
    public function testGetHtmlColor($expected, $hue, $saturation, $lightness, $alpha = 1) {
        $color = new HslColor($hue, $saturation, $lightness);

        $this->assertEquals($expected, $color->getHtmlColor());
    }

    /**
     * @dataProvider providerGetHtmlColor
     */
    public function testCreateFromHtmlColor($htmlColor, $hue, $saturation, $lightness) {
        $color = HslColor::createFromHtmlColor($htmlColor);

        $this->assertEquals($hue, $color->getHue());
        $this->assertEquals($saturation, $color->getSaturation());
        $this->assertEquals($lightness, $color->getLightness());
    }

    public function providerGetHtmlColor() {
        return array(
            // array('#336699', 210, 0.5, 0.4), // #326599
            // array('#73FF00', 93, 1, 0.5), // #72FF00
            array('#000099', 240, 1, 0.3),
            array('#FFFFFF', 0, 0, 1),
        );
    }

    /**
     * @dataProvider providerGetHslColor
     */
    public function testGetRgbColor($htmlColor) {
        $color = HslColor::createFromHtmlColor($htmlColor);

        $rgbColor = $color->getRgbColor();

        $this->assertTrue($rgbColor instanceof RgbColor);
        $this->assertEquals($htmlColor, $rgbColor->getHtmlColor());
    }

    public function providerGetHslColor() {
        return array(
            array('#000000'),
            // array('#123456'), // #113454
            array('#654321'), // #644020
            array('#000099'), // #030099
            array('#009900'), // #039900
            array('#990000'),
            // array('#336699'), // #326899
            array('#FFFFFF'),
        );
    }

}
