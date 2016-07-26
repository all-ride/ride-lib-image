<?php

namespace ride\library\image;

use ride\library\image\color\HslColor;
use ride\library\image\color\RgbColor;
use ride\library\image\dimension\GenericDimension;
use ride\library\image\exception\ImageException;
use ride\library\image\point\GenericPoint;

/**
 * Generic factory for image objects
 */
class GenericImageFactory implements ImageFactory {

    /**
     * Name of the GD image library
     * @var string
     */
    const IMAGE_GD = 'gd';

    /**
     * Name of the imagick library
     * @var string
     */
    const IMAGE_IMAGICK = 'imagick';

    /**
     * Name of the underlying image library (gd or imagick)
     * @var string
     */
    protected $image;

    /**
     * Constructs a new image factory
     * @param string $image Name of image library to use (gd or imagick)
     * @return null
     */
    public function __construct($image) {
        if ($image !== self::IMAGE_GD && $image !== self::IMAGE_IMAGICK) {
            throw new ImageException('Could not construct this image factory: provided image library should be gd or imagick');
        }

        $this->image = $image;
    }

    /**
     * Creates an image object
     * @return \ride\library\image\Image
     */
    public function createImage() {
        if ($this->image === self::IMAGE_GD) {
            return new GdImage();
        } else {
            return new ImagickImage();
        }
    }

    /**
     * Creates a dimension
     * @param integer $width
     * @param integer $height
     * @return \ride\library\image\dimension\Dimension
     */
    public function createDimension($width, $height) {
        return new GenericDimension($width, $height);
    }

    /**
     * Creates a point
     * @param integer $x
     * @param integer $y
     * @return \ride\library\image\point\Point
     */
    public function createPoint($x, $y) {
        return new GenericPoint($x, $y);
    }

    /**
     * Creates a HTML color
     * @param string $htmlColor
     * @return \ride\library\image\color\RgbColor
     */
    public function createHtmlColor($htmlColor) {
        return RgbColor::createFromHtmlColor($htmlColor);
    }

    /**
     * Creates a RGB color
     * @param integer $red Value between 0 and 255
     * @param integer $green Value between 0 and 255
     * @param integer $blue Value between 0 and 255
     * @param float $alpha Value between 0 and 1
     * @return \ride\library\image\color\RgbColor
     */
    public function createRgbColor($red, $green, $blue, $alpha = 1) {
        return new RgbColor($red, $green, $blue, $alpha);
    }

    /**
     * Creates a RGB color
     * @param integer $hue Value between 0 and 360
     * @param float $saturation Value between 0 and 1
     * @param float $lightness Value between 0 and 1
     * @param float $alpha Value between 0 and 1
     * @return \ride\library\image\color\HslColor
     */
    public function createHslColor($hue, $saturation, $lightness, $alpha = 1) {
        return new HslColor($hue, $saturation, $lightness, $alpha);
    }

}
