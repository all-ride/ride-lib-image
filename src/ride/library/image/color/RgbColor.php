<?php

namespace ride\library\image\color;

use ride\library\image\exception\ImageException;

/**
 * RGB color implementation
 */
class RgbColor extends AbstractColor {

    /**
     * Value for red
     * @var integer
     */
    protected $red;

    /**
     * Value for green
     * @var integer
     */
    protected $green;

    /**
     * Value for blue
     * @var integer
     */
    protected $blue;

    /**
     * Constructs a new color
     * @param integer $red Red value between 0 and 255
     * @param integer $green Green value between 0 and 255
     * @param integer $blue Blue value between 0 and 255
     * @param float $alpha Alpha value between 0 and 1
     * @return null
     */
    public function __construct($red, $green, $blue, $alpha = 1) {
        $this->validateValue($red, 'red');
        $this->validateValue($green, 'green');
        $this->validateValue($blue, 'blue');

        $this->red = (integer) $red;
        $this->green = (integer) $green;
        $this->blue = (integer) $blue;

        parent::__construct($alpha);
    }

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString() {
        if ($this->alpha === 1) {
            return 'RGB(' . $this->red . ',' . $this->green . ',' . $this->blue . ')';
        } else {
            return 'RGBA(' . $this->red . ',' . $this->green . ',' . $this->blue . ',' . $this->alpha . ')';
        }
    }

    /**
     * Sets the red value
     * @param integer $red Value between 0 and 255
     * @return RgbColor New instance with the adjusted red value
     */
    public function setRed($red) {
        $this->validateValue($red, 'red');

        $color = clone $this;
        $color->red = $red;

        return $color;
    }

    /**
     * Gets the red value
     * @return integer Value between 0 and 255
     */
    public function getRed() {
        return $this->red;
    }

    /**
     * Sets the green value
     * @param integer $green Value between 0 and 255
     * @return RgbColor New instance with the adjusted green value
     */
    public function setGreen($green) {
        $this->validateValue($green, 'green');

        $color = clone $this;
        $color->green = $green;

        return $color;
    }

    /**
     * Gets the green value
     * @return integer Value between 0 and 255
     */
    public function getGreen() {
        return $this->green;
    }

    /**
     * Sets the blue value
     * @param integer $blue Value between 0 and 255
     * @return RgbColor New instance with the adjusted blue value
     */
    public function setBlue($blue) {
        $this->validateValue($blue, 'blue');

        $color = clone $this;
        $color->blue = $blue;

        return $color;
    }

    /**
     * Gets the blue value
     * @return integer Value between 0 and 255
     */
    public function getBlue() {
        return $this->blue;
    }

    /**
     * Gets the brightness value of this color
     * @return float Value between 0 and 1
     */
    public function getBrightness() {
        return (($this->red * 299) + ($this->green * 587) + ($this->blue * 114)) / 255000;
    }

    /**
     * Checks if this color is dark
     * @return boolean True when the color is dark, false for a light color
     */
    public function isDark() {
        return $this->getBrightness() < 0.25;
    }

    /**
     * Adjusts the luminance (visually perceived brightness) of the color
     * @param float $factor Value between -1 and 1
     * @return Color New color instance with the luminance adjusted
     */
    public function luminate($factor) {
        if (!is_numeric($factor) || $factor < -1 || $factor > 1) {
            throw new ImageException('Could not luminate color: provided factor should be a number between -1 and 1');
        }

        $color = clone $this;
        $color->red = round(min(max(0, $color->red + ($color->red * $factor)), 255));
        $color->green = round(min(max(0, $color->green + ($color->green * $factor)), 255));
        $color->blue = round(min(max(0, $color->blue + ($color->blue * $factor)), 255));

        return $color;
    }

    /**
     * Gets this color in a HSL implementation
     * @return HslColor
     */
    public function getHslColor() {
        $red = $this->red / 255;
        $green = $this->green / 255;
        $blue = $this->blue / 255;

        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);

        $hue = 0;
        $saturation = 0;
        $lightness = ($max + $min) / 2;
        $d = $max - $min;

        if ($d == 0) {
            return new HslColor($hue, $saturation, $lightness, $this->alpha);
        }

        $saturation = $d / ( 1 - abs( 2 * $lightness - 1 ) );

        if ($max === $red) {
    	    $hue = 60 * fmod((($green - $blue) / $d), 6);
    	    if ($blue > $green) {
    	        $hue += 360;
    	    }
        } elseif ($max === $green) {
    	    $hue = 60 * (($blue - $red) / $d + 2);
        } elseif ($max === $blue) {
    	    $hue = 60 * (($red - $green) / $d + 4);
        }

        return new HslColor($hue, $saturation, $lightness, $this->alpha);
    }

    /**
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor() {
        $red = str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT);

        return strtoupper('#' . $red . $green . $blue);
    }

    /**
     * Validates a color value
     * @param mixed $value Value to validate
     * @param string $name Name of the variable
     * @return null
     * @throws ride\library\image\exception\ImageException when the value is invalid
     */
    private function validateValue($value, $name) {
        if (!is_numeric($value) || $value < 0 || $value > 255) {
            throw new ImageException('Could not set the ' . $name . ' value: provided value is not between 0 and 255');
        }
    }

    /**
     * Creates a color object from a HTML color code
     * @param string $htmlColor HTML color code. Accepts the following formats: #AABBCC, AABBCC, #ABC and ABC
     * @return Color
     */
    public static function createFromHtmlColor($htmlColor) {
        if ($htmlColor[0] == '#') {
            $htmlColor = substr($htmlColor, 1);
        }

        if (strlen($htmlColor) == 3) {
            list($red, $green, $blue) = str_split($htmlColor, 1);

            $red .= $red;
            $green .= $green;
            $blue .= $blue;
        } else {
            list($red, $green, $blue) = str_split($htmlColor, 2);
        }

        $red = hexdec($red);
        $green = hexdec($green);
        $blue = hexdec($blue);

        return new self($red, $green, $blue);
    }

}
