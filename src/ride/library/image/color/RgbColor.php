<?php

namespace ride\library\image\color;

use ride\library\image\exception\ImageException;

/**
 * RGB color implementation
 */
class RgbColor implements HtmlColor {

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
     * Value for aplha channel
     * @var integer
     */
    protected $alpha;

    /**
     * Constructs a new color
     * @param integer $red Red value between 0 and 255
     * @param integer $green Green value between 0 and 255
     * @param integer $blue Blue value between 0 and 255
     * @param integer $alpha Alpha value between 0 and 127
     * @return null
     */
    public function __construct($red, $green, $blue, $alpha = 0) {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString() {
        if (!$this->alpha) {
            return 'RGB(' . $this->red . ', ' . $this->green . ', ' . $this->blue . ')';
        } else {
            return 'RGBA(' . $this->red . ', ' . $this->green . ', ' . $this->blue . ', ' . $this->alpha . ')';
        }
    }

    /**
     * Allocates the color to the provided image resource
     * @param resource $resource Image resource
     * @return integer Color identifier
     * @throws \ride\library\image\exception\ImageException when the color
     * could not be allocated
     */
    public function allocate($resource) {
        if ($this->alpha) {
            $id = imageColorAllocateAlpha($resource, $this->red, $this->green, $this->blue, $this->alpha);
        } else {
            $id = imageColorAllocate($resource, $this->red, $this->green, $this->blue);
        }

        if ($id === false) {
            throw new ImageException('Could not allocate color ' . $this);
        }

        return $id;
    }

    /**
     * Gets the red value
     * @return integer Value between 0 and 255
     */
    public function getRed() {
        return $this->red;
    }

    /**
     * Gets the green value
     * @return integer Value between 0 and 255
     */
    public function getGreen() {
        return $this->green;
    }

    /**
     * Gets the blue value
     * @return integer Value between 0 and 255
     */
    public function getBlue() {
        return $this->blue;
    }

    /**
     * Gets the alpha channel value
     * @return integer Value between 0 and 127
     */
    public function getAlpha() {
        return $this->alpha;
    }

    /**
     * Gets the brightness value of this color
     * @return integer Value between 0 and 500
     */
    public function getBrightness() {
        return (($this->red * 299) + ($this->green * 587) + ($this->blue * 114)) / 1000;
    }

    /**
     * Checks if this color is dark
     * @return boolean True when the color is dark, false for a light color
     */
    public function isDark() {
        return $this->getBrightness() < 125;
    }

    /**
     * Adjusts the luminance (visually perceived brightness) of the color
     * @param float $factor Value between -1 and 1
     * @return Color New color instance with the luminance adjusted
     */
    public function luminate($factor) {
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

        $hue;
        $saturation;
        $lightness = ($max + $min) / 2;
        $d = $max - $min;

        if ($d == 0) {
            return new HslColor(0, 0, $lightness, $this->alpha);
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

        return new HslColor(round($hue / 360, 2), round($saturation, 2), round($lightness, 2), $this->alpha);
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