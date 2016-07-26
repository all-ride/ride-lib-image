<?php

namespace ride\library\image\color\scheme;

use ride\library\image\color\RgbColor;

use \PHPUnit_Framework_TestCase;

class ComplementarySchemeTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerGetColors
     */
    public function testGetColors($expected, $color, $number) {
        $scheme = new ComplementaryScheme();
        $colors = $scheme->getColors($color, $number);

        $this->assertEquals($number, count($colors));
        $this->assertEquals($expected, $colors);
    }

    public function providerGetColors() {
        return array(
            array(
                array(
                    RgbColor::createFromHtmlColor('#009899'),
                ),
                RgbColor::createFromHtmlColor('#990000'),
                1,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#009899'),
                    RgbColor::createFromHtmlColor('#990000'),
                ),
                RgbColor::createFromHtmlColor('#990000'),
                2,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#996632'),
                    RgbColor::createFromHtmlColor('#273B4F'),
                    RgbColor::createFromHtmlColor('#336699'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                3,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#996632'),
                    RgbColor::createFromHtmlColor('#2D4C6B'),
                    RgbColor::createFromHtmlColor('#5198E0'),
                    RgbColor::createFromHtmlColor('#14191E'),
                    RgbColor::createFromHtmlColor('#336699'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                5,
            ),
            array(
                array(
                    RgbColor::createFromHtmlColor('#996632'),
                    RgbColor::createFromHtmlColor('#2F5377'),
                    RgbColor::createFromHtmlColor('#3F8AD5'),
                    RgbColor::createFromHtmlColor('#212F3C'),
                    RgbColor::createFromHtmlColor('#6FAEED'),
                    RgbColor::createFromHtmlColor('#090A0C'),
                    RgbColor::createFromHtmlColor('#336699'),
                ),
                RgbColor::createFromHtmlColor('#336699'),
                7,
            ),
        );
    }

}
