<?php

namespace pallo\library\image;

/**
 * Data container for a color
 */
class Color {

    /**
     * Value for red
     * @var integer
     */
    private $red;

    /**
     * Value for green
     * @var integer
     */
    private $green;

    /**
     * Value for blue
     * @var integer
     */
    private $blue;

    /**
     * Value for aplha channel
     * @var integer
     */
    private $alpha;

    /**
     * Constructs a new color
     * @param integer|string $red
     * @param integer|string $green
     * @param integer|string $blue
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
     * Gets the red value
     * @return integer
     */
    public function getRed() {
        return $this->red;
    }

    /**
     * Gets the green value
     * @return integer
     */
    public function getGreen() {
        return $this->green;
    }

    /**
     * Gets the blue value
     * @return integer
     */
    public function getBlue() {
        return $this->blue;
    }

    /**
     * Gets the alpha channel value
     * @return integer
     */
    public function getAlpha() {
        return $this->alpha;
    }

    /**
     * Checks if this color is dark
     * @return boolean True when the color is dark, false for a light color
     */
    public function isDark() {
        $brightness = (($this->red * 299) + ($this->green * 587) + ($this->blue * 114)) / 1000;

        return $brightness < 125;
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
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor() {
        $red = str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT);

        return '#' . $red . $green . $blue;
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