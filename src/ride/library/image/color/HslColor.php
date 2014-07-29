<?php

namespace ride\library\image\color;

use ride\library\image\exception\ImageException;

/**
 * HSL color implementation
 */
class HslColor extends AbstractColor {

    /**
     * Hue of the color
     * @var float
     */
    protected $hue;

    /**
     * Saturation of the color
     * @var float
     */
    protected $saturation;

    /**
     * Lightness of the color
     * @var float
     */
    protected $lightness;

    /**
     * Constructs a new color
     * @param float $hue Hue value between 0 and 1
     * @param float $saturation Saturation value between 0 and 1
     * @param float $lightness Hue value between 0 and 1
     * @param integer $alpha Alpha value between 0 and 1
     * @return null
     */
    public function __construct($hue, $saturation, $lightness, $alpha = 1) {
        $this->setHue($hue);
        $this->setSaturation($saturation);
        $this->setLightness($lightness);
        $this->setAlpha($alpha);
    }

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString() {
        if (!$this->alpha) {
            return 'HSL(' . $this->hue . ', ' . $this->saturation . ', ' . $this->lightness . ')';
        } else {
            return 'HSLA(' . $this->hue . ', ' . $this->saturation . ', ' . $this->lightness . ', ' . $this->alpha . ')';
        }
    }

    // /**
    //  * Allocates the color to the provided image resource
    //  * @param resource $resource Image resource
    //  * @return integer Color identifier
    //  * @throws ride\library\image\exception\ImageException when the color
    //  * could not be allocated
    //  */
    // public function allocate($resource) {
    //     $color = $this->getRgbColor();

    //     if ($color->getAlpha()) {
    //         $id = imageColorAllocateAlpha($resource, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha());
    //     } else {
    //         $id = imageColorAllocate($resource, $color->getRed(), $color->getGreen(), $color->getBlue());
    //     }

    //     if ($id === false) {
    //         throw new ImageException('Could not allocate color ' . $this);
    //     }

    //     return $id;
    // }

    /**
     * Sets the hue value
     * @param float $hue Value between 0 and 1
     * @return null
     */
    public function setHue($hue) {
        if (!is_numeric($hue) || $hue < 0 || $hue > 1) {
            throw new ImageException('Could not set the hue value: provided value is not between 0 and 1');
        }

        $this->hue = $hue;
    }

    /**
     * Gets the hue value
     * @return float Value between 0 and 1
     */
    public function getHue() {
        return $this->hue;
    }

    /**
     * Sets the saturation value
     * @param float $saturation Value between 0 and 1
     * @return null
     */
    public function setSaturation($saturation) {
        if (!is_numeric($saturation) || $saturation < 0 || $saturation > 1) {
            throw new ImageException('Could not set the saturation value: provided value is not between 0 and 1');
        }

        $this->saturation = $saturation;
    }

    /**
     * Gets the saturation value
     * @return float Value between 0 and 1
     */
    public function getSaturation() {
        return $this->saturation;
    }

    /**
     * Sets the lightness value
     * @param float $lightness Value between 0 and 1
     * @return null
     */
    public function setLightness($lightness) {
        if (!is_numeric($lightness) || $lightness < 0 || $lightness > 1) {
            throw new ImageException('Could not set the lightness value: provided value is not between 0 and 1');
        }

        $this->lightness = $lightness;
    }

    /**
     * Gets the lightness value
     * @return float Value between 0 and 1
     */
    public function getLightness() {
        return $this->lightness;
    }

    /**
     * Gets this color in a RGB implementation
     * @return RgbColor
     */
    public function getRgbColor() {
        if ($this->saturation == 0) {
            $red = $this->lightness * 255;
            $green = $this->lightness * 255;
            $blue = $this->lightness * 255;
        } else {
            $tmp1 = 2 * $this->lightness - $tmp2;
            if ($this->lightness < 0.5) {
                $tmp2 = $this->lightness * (1 + $this->saturation);
            } else {
                $tmp2 = ($this->lightness + $this->saturation) - ($this->saturation * $this->lightness);
            };

            $red = 255 * $this->hueToRgb($tmp1, $tmp2, $this->hue + (1 / 3));
            $green = 255 * $this->hueToRgb($tmp1, $tmp2, $this->hue);
            $blue = 255 * $this->hueToRgb($tmp1, $tmp2, $this->hue - (1 / 3));
        };

        return new RgbColor($red, $green, $blue, $this->alpha);
    }

    /**
     * Converts a hue value to an RGB value
     * @param float $value1
     * @param float $value2
     * @param float $hue
     * @return number|unknown
     */
    protected function hueToRgb($value1, $value2, $hue) {
        if ($hue < 0) {
            $hue += 1;
        };

        if ($hue > 1) {
            $hue -= 1;
        };

        if ((6 * $hue) < 1) {
            return ($value1 + ($value2 - $value1) * 6 * $hue);
        };

        if ((2 * $hue) < 1) {
            return ($value2);
        };

        if ((3 * $hue) < 2) {
            return ($value1 + ($value2 - $value1) * ((2 / 3 - $hue) * 6));
        };

        return ($value1);
    }

    /**
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor() {
        return $this->getRgbColor()->getHtmlColor();
    }

    /**
     * Creates a color object from a HTML color code
     * @param string $htmlColor HTML color code. Accepts the following formats:
     * #AABBCC, AABBCC, #ABC and ABC
     * @return HtmlColor
    */
    public static function createFromHtmlColor($htmlColor) {
        return RgbColor::createFromHtmlColor($htmlColor)->getHslColor();
    }

}
