<?php

namespace ride\library\image\dimension;

use \PHPUnit_Framework_TestCase;

class GenericDimensionTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerToString
     */
    public function testToString($expected, $width, $height) {
        $dimension = new GenericDimension($width, $height);

        $this->assertEquals($expected, (string) $dimension);
    }

    public function providerToString() {
        return array(
            array('100x50', 100, 50),
            array('0x50', 0, 50),
        );
    }

    public function testWidthAndHeight() {
        $width = 50;
        $height = 100;

        $dimension = new GenericDimension($width, $height);

        $this->assertEquals($width, $dimension->getWidth());
        $this->assertEquals($height, $dimension->getHeight());

        $dimension = $dimension->setWidth($height)->setHeight($width);

        $this->assertEquals($height, $dimension->getWidth());
        $this->assertEquals($width, $dimension->getHeight());
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructWidthThrowsExceptionWhenInvalidValueProvided($value) {
        new GenericDimension($value, 50);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetWidthThrowsExceptionWhenInvalidValueProvided($value) {
        $dimension = new GenericDimension(50, 50);
        $dimension->setWidth($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructHeightThrowsExceptionWhenInvalidValueProvided($value) {
        new GenericDimension(50, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetHeightThrowsExceptionWhenInvalidValueProvided($value) {
        $dimension = new GenericDimension(50, 50);
        $dimension->setHeight($value);
    }

    public function providerInvalidValue() {
        return array(
            array(null),
            array(-50),
            array('test'),
            array(array()),
            array($this),
        );
    }

}
