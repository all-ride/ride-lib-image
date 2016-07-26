<?php

namespace ride\library\image\point;

use \PHPUnit_Framework_TestCase;

class GenericPointTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerToString
     */
    public function testToString($expected, $x, $y) {
        $point = new GenericPoint($x, $y);

        $this->assertEquals($expected, (string) $point);
    }

    public function providerToString() {
        return array(
            array('[100,50]', 100, 50),
            array('[50,-10]', 50, -10),
        );
    }

    public function testXAndY() {
        $x = 50;
        $y = 100;

        $point = new GenericPoint($x, $y);

        $this->assertEquals($x, $point->getX());
        $this->assertEquals($y, $point->getY());

        $point = $point->setX($y)->setY($x);

        $this->assertEquals($y, $point->getX());
        $this->assertEquals($x, $point->getY());
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructXThrowsExceptionWhenInvalidValueProvided($value) {
        new GenericPoint($value, 50);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetXThrowsExceptionWhenInvalidValueProvided($value) {
        $point = new GenericPoint(50, 50);
        $point->setX($value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testConstructYThrowsExceptionWhenInvalidValueProvided($value) {
        $point = new GenericPoint(50, $value);
    }

    /**
     * @dataProvider providerInvalidValue
     * @expectedException ride\library\image\exception\ImageException
     */
    public function testSetYThrowsExceptionWhenInvalidValueProvided($value) {
        $point = new GenericPoint(50, 50);
        $point->setY($value);
    }

    public function providerInvalidValue() {
        return array(
            array(null),
            array('test'),
            array(array()),
            array($this),
        );
    }

}
