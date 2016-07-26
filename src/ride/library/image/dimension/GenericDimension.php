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
        $this->validateValue($width, 'width');
        $this->validateValue($height, 'height');

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Gets a string representation of this dimension
     * @return string;
     */
    public function __toString() {
        return $this->width . 'x' . $this->height;
    }

    /**
     * Sets the width of the dimension
     * @param integer $width
     * @return GenericDimension new dimension with adjusted width
     */
    public function setWidth($width) {
        $this->validateValue($width, 'width');

        $dimension = clone $this;
        $dimension->width = $width;

        return $dimension;
    }

    /**
     * Gets the width of the dimension
     * @return integer
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Sets the height of the dimension
     * @param integer $height
     * @return GenericDimension New instance with adjusted height
     */
    public function setHeight($height) {
        $this->validateValue($height, 'height');

        $dimension = clone $this;
        $dimension->height = $height;

        return $dimension;
    }

    /**
     * Gets the height of the dimension
     * @return integer
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Validates a dimension value
     * @param mixed $value Value to validate
     * @param string $name Name of the variable
     * @return null
     * @throws ride\library\image\exception\ImageException when the value is invalid
     */
    private function validateValue($value, $name) {
        if (!is_numeric($value) || $value < 0) {
            throw new ImageException('Could not set ' . $name . ' of dimension: provided value should be an integer greater or equals to 0');
        }
    }

}
