<?php

namespace ride\library\image\dimension;

use ride\library\image\exception\ImageException;

/**
 * Data container for a dimension
 */
class GenericDimension implements Dimension {

    /**
     * Width of the dimension
     * @var integer
     */
    protected $width;

    /**
     * Height of the dimension
     * @var integer
     */
    protected $height;

    /**
     * Constructs a new dimension
     * @param integer $width
     * @param integer $height
     * @return null
     */
    public function __construct($width, $height) {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    /**
     * Gets a string representation of this dimension
     * @return string;
     */
    public function __toString() {
        return $this->width . ' x ' . $this->height;
    }

    /**
     * Sets the width of this dimension
     * @param integer $width
     * @return null
     */
    public function setWidth($width) {
        if (!is_numeric($width)) {
            throw new ImageException('Could not set width of dimension: not a numeric value provided');
        }

        $this->width = $width;
    }

    /**
     * Gets the width of the dimension
     * @return integer
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Sets the width of this dimension
     * @param integer $width
     * @return null
     */
    public function setHeight($height) {
        if (!is_numeric($height)) {
            throw new ImageException('Could not set height of dimension: not a numeric value provided');
        }

        $this->height = $height;
    }

    /**
     * Gets the height of the dimension
     * @return integer
     */
    public function getHeight() {
        return $this->height;
    }

}
