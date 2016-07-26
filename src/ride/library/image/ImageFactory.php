<?php

namespace ride\library\image;

/**
 * Factory for image objects
 */
interface ImageFactory {

    /**
     * Creates an image object
     * @return \ride\library\image\Image
     */
    public function createImage();

    /**
     * Creates a dimension
     * @return \ride\library\image\dimension\Dimension
     */
    public function createDimension($width, $height);

    /**
     * Creates a point
     * @return \ride\library\image\point\Point
     */
    public function createPoint($x, $y);

    /**
     * Creates a RGB color
     * @param integer $red Value between 0 and 255
     * @param integer $green Value between 0 and 255
     * @param integer $blue Value between 0 and 255
     * @param float $alpha Value between 0 and 1
     * @return \ride\library\image\color\RgbColor
     */
    public function createRgbColor($red, $green, $blue, $alpha = 1);

    /**
     * Creates a HSL color
     * @param integer $hue Value between 0 and 360
     * @param float $saturation Value between 0 and 1
     * @param float $lightness Value between 0 and 1
     * @param float $alpha Value between 0 and 1
     * @return \ride\library\image\color\HslColor
     */
    public function createHslColor($hue, $saturation, $lightness, $alpha = 1);

}
