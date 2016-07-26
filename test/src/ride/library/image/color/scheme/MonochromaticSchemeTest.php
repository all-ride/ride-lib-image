<?php

namespace ride\library\image\color\scheme;

use ride\library\image\color\RgbColor;

use \PHPUnit_Framework_TestCase;

class MonochromaticSchemeTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerGetColors
     */
    public function testGetColors($expected, $color, $number) {
        $scheme = new MonochromaticScheme();
        $colors = $scheme->getColors($color, $number);

        $this->assertEquals($number, count($colors));
        $this->assertEquals($expected, $colors);
    }

    public function providerGetColors() {
        return array(
            array(
                array(
                    RgbColor::createFromHtmlColor('#336699'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                1,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#2B435C'),
                    RgbColor::createFromHtmlColor('#336699'),
                    RgbColor::createFromHtmlColor('#68A9EB'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                3,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#1E2833'),
                    RgbColor::createFromHtmlColor('#2F5173'),
                    RgbColor::createFromHtmlColor('#336699'),
                    RgbColor::createFromHtmlColor('#448ED8'),
                    RgbColor::createFromHtmlColor('#7DB7F2'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                5,
            ),
        );
    }

}
