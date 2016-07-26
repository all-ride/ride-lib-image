<?php

namespace ride\library\image\color;

use \PHPUnit_Framework_TestCase;

class RgbColorTest extends PHPUnit_Framework_TestCase {

    public function testRgb() {
        $red = 1;
        $green = 2;
        $blue = 3;
        $alpha = 0.5;

        $color = new RgbColor($red, $green, $blue, $alpha);

        $this->assertEquals($red, $color->getRed());
        $this->assertEquals($green, $color->getGreen());
        $this->assertEquals($blue, $color->getBlue());
        $this->assertEquals($alpha, $color->getAlpha());

        $alpha = 0.1;

        $color = $color->setRed($green)->setGreen($blue)->setBlue($red)->setAlpha($alpha);

        $this->assertEquals($green, $color->getRed());
        $this->assertEquals($blue, $color->getGreen());
        $this->assertEquals($red, $color->getBlue());
        $this->assertEquals($alpha, $color->getAlpha());
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructRedThrowsExceptionWhenInvalidValueProvided($value) {
        new RgbColor($value, 50, 50);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetRedThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new RgbColor(50, 50, 50);
        $color->setRed($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructGreenThrowsExceptionWhenInvalidValueProvided($value) {
        new RgbColor(50, $value, 50);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetGreenThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new RgbColor(50, 50, 50);
        $color->setGreen($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructBlueThrowsExceptionWhenInvalidValueProvided($value) {
        new RgbColor(50, 50, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetBlueThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new RgbColor(50, 50, 50);
        $color->setBlue($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructAlphaThrowsExceptionWhenInvalidValueProvided($value) {
        new RgbColor(50, 50, 50, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetAlphaThrowsExceptionWhenInvalidValueProvided($value) {
        $color = new RgbColor(50, 50, 50, 1);
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
    public function testToString($expected, $red, $green, $blue, $alpha = 1) {
        $color = new RgbColor($red, $green, $blue, $alpha);

        $this->assertEquals($expected, (string) $color);
    }

    public function providerToString() {
        return array(
            array('RGB(33,66,99)', 33, 66, 99),
            array('RGB(255,255,255)', 255, 255, 255),
            array('RGBA(140,150,160,0.7)', 140, 150, 160, 0.7),
        );
    }

    /**
     * @dataProvider providerGetHtmlColor
     */
    public function testGetHtmlColor($expected, $red, $green, $blue, $alpha = 1) {
        $color = new RgbColor($red, $green, $blue);

        $this->assertEquals($expected, $color->getHtmlColor());
    }

    /**
     * @dataProvider providerGetHtmlColor
     */
    public function testCreateFromHtmlColor($htmlColor, $red, $green, $blue) {
        $color = RgbColor::createFromHtmlColor($htmlColor);

        $this->assertEquals($red, $color->getRed());
        $this->assertEquals($green, $color->getGreen());
        $this->assertEquals($blue, $color->getBlue());
    }

    public function providerGetHtmlColor() {
        return array(
            array('#336699', 51, 102, 153),
            array('#FFFFFF', 255, 255, 255),
        );
    }

    /**
     * @dataProvider providerGetHslColor
     */
    public function testGetHslColor($htmlColor) {
        $color = RgbColor::createFromHtmlColor($htmlColor);

        $hslColor = $color->getHslColor();

        $this->assertTrue($hslColor instanceof HslColor);
        $this->assertEquals($htmlColor, $hslColor->getHtmlColor());
    }

    public function providerGetHslColor() {
        return array(
            array('#000000'),
//            array('#123456'), // #113454
            array('#654321'), // #644020
            array('#000099'), // #030099
            array('#009900'), // #039900
            array('#990000'),
//            array('#336699'), // #326899
            array('#FFFFFF'),
        );
    }

    /**
     * @dataProvider providerGetBrightness
     */
    public function testGetBrightness($expected, $htmlColor) {
        $color = RgbColor::createFromHtmlColor($htmlColor);

        $this->assertEquals($expected, $color->getBrightness());
    }

    public function providerGetBrightness() {
        return array(
            array(0, '#000000'),
            array(0.49803921568627452, '#7F7F7F'),
            array(1, '#ffffff'),
        );
    }

    /**
     * @dataProvider providerIsDark
     */
    public function testIsDark($expected, $htmlColor) {
        $color = RgbColor::createFromHtmlColor($htmlColor);

        $this->assertEquals($expected, $color->isDark());
    }

    public function providerIsDark() {
        return array(
            array(true, '#000000'),
            array(false, '#7F7F7F'),
            array(false, '#ffffff'),
        );
    }

    /**
     * @dataProvider providerLuminate
     */
    public function testLuminate($expected, $htmlColor, $factor) {
        $color = RgbColor::createFromHtmlColor($htmlColor);
        $color = $color->luminate($factor);

        $this->assertEquals($expected, $color->getHtmlColor());
    }

    public function providerLuminate() {
        return array(
            array('#000000', '#336699', -1),
            array('#1A334D', '#336699', -0.5),
            array('#2E5C8A', '#336699', -0.1),
            array('#3870A8', '#336699', 0.1),
            array('#4D99E6', '#336699', 0.5),
            array('#66CCFF', '#336699', 1),
        );
    }

    /**
     * @dataProvider providerLuminateThrowsExceptionWhenInvalidFactorProvided
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testLuminateThrowsExceptionWhenInvalidFactorProvided($factor) {
        $color = new RgbColor(1, 2, 3);
        $color->luminate($factor);
    }

    public function providerLuminateThrowsExceptionWhenInvalidFactorProvided() {
        return array(
            array(null),
            array(true),
            array(-2),
            array(2),
            array(1.1),
            array(array()),
            array($this),
        );
    }

}
