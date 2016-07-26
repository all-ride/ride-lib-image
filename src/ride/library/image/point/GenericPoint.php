<?php

namespace ride\library\image\point;

use ride\library\image\exception\ImageException;

/**
 * Data container for a point
 */
class GenericPoint implements Point {

    /**
     * Value on the X-axis
     * @var integer
     */
    protected $x;

    /**
     * Value on the Y-axis
     * @var integer
     */
    protected $y;

    /**
     * Constructs a new point
     * @param integer $x
     * @param integer $y
     * @return null
     */
    public function __construct($x, $y) {
        $this->validateValue($x, 'X');
        $this->validateValue($y, 'Y');

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Gets a string representation of this point
     * @return string
     */
    public function __toString() {
        return '[' . $this->x . ',' . $this->y . ']';
    }

    /**
     * Sets the value on the X-axis
     * @param integer $x
     * @return GenericPoint New instance with adjusted X value
     */
    public function setX($x) {
        $this->validateValue($x, 'X');

        $point = clone $this;
        $point->x = $x;

        return $point;
    }

    /**
     * Gets the value on the X-axis
     * @return integer
     */
    public function getX() {
        return $this->x;
    }

    /**
     * Sets the value on the Y-axis
     * @param integer $y
     * @return GenericPoint New instance  with adjusted Y value
     */
    public function setY($y) {
        $this->validateValue($y, 'Y');

        $point = clone $this;
        $point->y = $y;

        return $point;
    }

    /**
     * Gets the value on the Y-axis
     * @return integer
     */
    public function getY() {
        return $this->y;
    }

    /**
     * Validates a coordinate value
     * @param mixed $value Value to validate
     * @param string $name Name of the variable
     * @return null
     * @throws ride\library\image\exception\ImageException when the value is invalid
     */
    private function validateValue($value, $name) {
        if (!is_numeric($value)) {
            throw new ImageException('Could not set ' . $name . ' coordinate of point: not a numeric value provided');
        }
    }

}
