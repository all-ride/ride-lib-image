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
     * @param float $hue Hue value between 0 and 360
     * @param float $saturation Saturation value between 0 and 1
     * @param float $lightness Hue value between 0 and 1
     * @param float $alpha Alpha value between 0 and 1
     * @return null
     */
    public function __construct($hue, $saturation, $lightness, $alpha = 1) {
        $this->validateValue($hue, 'hue');
        $this->validatePercentValue($saturation, 'saturation');
        $this->validatePercentValue($lightness, 'lightness');

        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->lightness = $lightness;

        parent::__construct($alpha);
    }

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString() {
        if ($this->alpha === 1) {
            return 'HSL(' . $this->hue . ',' . $this->saturation . ',' . $this->lightness . ')';
        } else {
            return 'HSLA(' . $this->hue . ',' . $this->saturation . ',' . $this->lightness . ',' . $this->alpha . ')';
        }
    }

    /**
     * Sets the hue value
     * @param float $hue Value between 0 and 360
     * @return HslImage New instance with adjusted hue value
     */
    public function setHue($hue) {
        $this->validateValue($hue, 'hue');

        $color = clone $this;
        $color->hue = $hue;

        return $color;
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
     * @return HslImage New instance with adjusted saturation value
     */
    public function setSaturation($saturation) {
        $this->validatePercentValue($saturation, 'saturation');

        $color = clone $this;
        $color->saturation = $saturation;

        return $color;
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
     * @return HslImage New instance with adjusted lightness value
     */
    public function setLightness($lightness) {
        $this->validatePercentValue($lightness, 'lightness');

        $color = clone $this;
        $color->lightness = $lightness;

        return $color;
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
            $green = $red;
            $blue = $red;
        } else {
            if ($this->lightness < 0.5) {
                $tmp2 = $this->lightness * (1 + $this->saturation);
            } else {
                $tmp2 = ($this->lightness + $this->saturation) - ($this->saturation * $this->lightness);
            }
            $tmp1 = 2 * $this->lightness - $tmp2;

            $hue = $this->hue / 360;

            $red = 255 * $this->hueToRgb($tmp1, $tmp2, $hue + (1 / 3));
            $green = 255 * $this->hueToRgb($tmp1, $tmp2, $hue);
            $blue = 255 * $this->hueToRgb($tmp1, $tmp2, $hue - (1 / 3));
        }

        return new RgbColor($red, $green, $blue, $this->alpha);
    }

    /**
     * Converts a hue value to an RGB value
     * @param float $value1
     * @param float $value2
     * @param float $hue
     * @return number|unknown
     */
    private function hueToRgb($value1, $value2, $hue) {
        if ($hue < 0) {
            $hue += 1;
        }

        if ($hue > 1) {
            $hue -= 1;
        }

        if ((6 * $hue) < 1) {
            return ($value1 + ($value2 - $value1) * 6 * $hue);
        }

        if ((2 * $hue) < 1) {
            return $value2;
        }

        if ((3 * $hue) < 2) {
            return ($value1 + ($value2 - $value1) * ((2 / 3 - $hue) * 6));
        }

        return $value1;
    }

    /**
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor() {
        return $this->getRgbColor()->getHtmlColor();
    }

    /**
     * Validates a hue value
     * @param mixed $value Value to validate
     * @param string $name Name of the variable
     * @return null
     * @throws ride\library\image\exception\ImageException when the value is invalid
     */
    private function validateValue($value, $name) {
        if (!is_numeric($value) || $value < 0 || $value > 360) {
            throw new ImageException('Could not set ' . $name . ' value: provided value is not between 0 and 360');
        }
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
