<?php

namespace ride\library\image;

/**
 * Data container for a point
 */
class Point {

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
        $this->setX($x);
        $this->setY($y);
    }

    /**
     * Gets a string representation of this point
     * @return string
     */
    public function __toString() {
        return '(' . $this->x . ', ' . $this->y . ')';
    }

    /**
     * Sets the value on the X-axis
     * @param integer $x
     * @return null
     */
    public function setX($x) {
        if (!is_numeric($x)) {
            throw new ImageException('Could not set X coordinate of point: not a numeric value provided');
        }

        $this->x = $x;
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
     * @return null
     */
    public function setY($y) {
        if (!is_numeric($y)) {
            throw new ImageException('Could not set Y coordinate of point: not a numeric value provided');
        }

        $this->y = $y;
    }

    /**
     * Gets the value on the Y-axis
     * @return integer
     */
    public function getY() {
        return $this->y;
    }

}
